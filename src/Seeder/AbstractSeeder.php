<?php

namespace ChessData\Seeder;

use Chess\Exception\UnknownNotationException;
use Chess\PGN\Validate;
use ChessData\File\AbstractFile;

abstract class AbstractSeeder extends AbstractFile
{
    public function seed(): \stdClass
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
                        if ($this->insert($tags, $validMovetext)) {
                            $this->result->valid++;
                        }
                    }
                    $tags = [];
                    $movetext = '';
                    $this->result->total++;
                } elseif ($this->line->startsMovetext($line)) {
                    if (Validate::tags($tags)) {
                        $movetext .= ' ' . $line;
                    }
                } elseif ($this->line->endsMovetext($line)) {
                    $movetext .= ' ' . $line;
                    if ($validMovetext = Validate::movetext($movetext)) {
                        if ($this->insert($tags, $validMovetext)) {
                            $this->result->valid++;
                        }
                    }
                    $tags = [];
                    $movetext = '';
                    $this->result->total++;
                } else {
                    $movetext .= ' ' . $line;
                }
            }
        }

        return $this->result;
    }

    abstract protected function insert(array $tags, string $movetext);
}
