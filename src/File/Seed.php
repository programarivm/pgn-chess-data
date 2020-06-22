<?php

namespace PGNChessData\File;

use PGNChess\Exception\UnknownNotationException;
use PGNChess\PGN\Tag;
use PGNChess\PGN\Validate;
use PGNChessData\Pdo;

class Seed extends AbstractFile
{
    private $result = [];

    public function __construct(string $filepath)
    {
        parent::__construct($filepath);
    }

    public function db()
    {
        $tags = [];
        $movetext = '';
        $file = new \SplFileObject($this->filepath);
        while (!$file->eof()) {
            $line = rtrim($file->fgets());
            try {
                $tag = Validate::tag($line);
                $tags[$tag->name] = $tag->value;
            } catch (UnknownNotationException $e) {
                if ($this->line->isOneLinerMovetext($line)) {
                    if (Validate::tags($tags) && $validMovetext = Validate::movetext($line)) {
                        try {
                            Pdo::getInstance()->query(
                                $this->sql(),
                                $this->values($tags, $validMovetext)
                            );
                        } catch (\PDOException $e) {
                        }
                    }
                    $tags = [];
                    $movetext = '';
                } elseif ($this->line->startsMovetext($line)) {
                    if (Validate::tags($tags)) {
                        $movetext .= ' ' . $line;
                    }
                } elseif ($this->line->endsMovetext($line)) {
                    $movetext .= ' ' . $line;
                    if ($validMovetext = Validate::movetext($line)) {
                        try {
                            Pdo::getInstance()->query(
                                $this->sql(),
                                $this->values($tags, $validMovetext)
                            );
                        } catch (\PDOException $e) {
                        }
                    }
                    $tags = [];
                    $movetext = '';
                } else {
                    $movetext .= ' ' . $line;
                }
            }
        }
    }

    protected function sql(): string
    {
        $sql = 'INSERT INTO games (';

        foreach (Tag::mandatory() as $name) {
            $sql .= "$name, ";
        }

        $sql .= 'movetext) VALUES (';

        foreach (Tag::mandatory() as $name) {
            $sql .= ":$name, ";
        }

        $sql .= ':movetext)';

        return $sql;
    }

    protected function values(array $tags, string $movetext): array
    {
        $values = [];

        foreach (Tag::mandatory() as $name) {
            $values[] = [
                'param' => ":$name",
                'value' => $tags[$name],
                'type' => \PDO::PARAM_STR
            ];
        }

        $values[] = [
            'param' => ':movetext',
            'value' => trim($movetext),
            'type' => \PDO::PARAM_STR
        ];

        return $values;
    }
}
