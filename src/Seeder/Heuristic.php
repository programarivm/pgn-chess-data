<?php

namespace ChessData\Seeder;

use Chess\Heuristic\Picture\Standard as StandardHeuristicPicture;
use Chess\PGN\Tag;
use ChessData\Pdo;

class Heuristic extends AbstractSeeder
{
    protected $dimensions;

    public function __construct(string $filepath)
    {
        parent::__construct($filepath);

        $this->dimensions = json_encode(
            array_map(function($item) {
                return (new \ReflectionClass($item))->getShortName();
            }, (new StandardHeuristicPicture(''))->getDimensions())
        );
    }

    protected function insert(array $tags, string $movetext)
    {
        $values = [];
        $params = '';
        $sql = 'INSERT INTO games (';

        foreach (Tag::mandatory() as $name) {
            $values[] = [
                'param' => ":$name",
                'value' => $tags[$name],
                'type' => \PDO::PARAM_STR
            ];
            $params .= ":$name, ";
            $sql .= "$name, ";
        }

        $sql .= "`movetext`,
                `heuristic_picture`,
                `heuristic_evaluation`)
                VALUES (
                    $params:movetext,
                    :heuristic_picture,
                    :heuristic_evaluation
                )";

        try {
            array_push($values,
                [
                    'param' => ':movetext',
                    'value' => $movetext,
                    'type' => \PDO::PARAM_STR,
                ],
                [
                    'param' => ':heuristic_picture',
                    'value' => json_encode((new StandardHeuristicPicture($movetext))->take()),
                    'type' => \PDO::PARAM_STR,
                ],
                [
                    'param' => ':heuristic_evaluation',
                    'value' => $this->dimensions,
                    'type' => \PDO::PARAM_STR,
                ],
            );

            return Pdo::getInstance()->query($sql, $values);
        } catch (\Exception $e) {}

        return false;
    }
}
