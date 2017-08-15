<?php
namespace PGNChess\Tests\Board;

use PGNChess\Board;
use PGNChess\Game;
use PGNChess\PGN\Converter;
use PGNChess\PGN\Symbol;
use PGNChess\Piece\Bishop;
use PGNChess\Piece\King;
use PGNChess\Piece\Knight;
use PGNChess\Piece\Pawn;
use PGNChess\Piece\Queen;
use PGNChess\Piece\Rook;
use PGNChess\Piece\Type\RookType;

class AnalyzerTest extends \PHPUnit_Framework_TestCase
{
    public function testCheck()
    {
        $pieces = [
            new Rook(Symbol::WHITE, 'a7', RookType::CASTLING_LONG),
            new Pawn(Symbol::WHITE, 'd4'),
            new Queen(Symbol::WHITE, 'e3'),
            new King(Symbol::WHITE, 'g1'),
            new Pawn(Symbol::WHITE, 'g2'),
            new Pawn(Symbol::WHITE, 'h2'),
            new King(Symbol::BLACK, 'e8'),
            new Knight(Symbol::BLACK, 'e4'),
            new Pawn(Symbol::BLACK, 'f7'),
            new Pawn(Symbol::BLACK, 'g7'),
            new Rook(Symbol::BLACK, 'g5', RookType::CASTLING_LONG),
            new Rook(Symbol::BLACK, 'h8', RookType::CASTLING_SHORT),
            new Pawn(Symbol::BLACK, 'h7')
        ];

        $game = new Game;
        $board = new Board($pieces);
        $game->setBoard($board);

        $this->assertEquals(true, $game->play(Converter::toObject(Symbol::WHITE, 'Ra8+')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(true, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->play(Converter::toObject(Symbol::BLACK, 'Kd8')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(true, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->play(Converter::toObject(Symbol::BLACK, 'Kf8')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(true, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(true, $game->play(Converter::toObject(Symbol::BLACK, 'Ke7')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(false, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(true, $game->play(Converter::toObject(Symbol::WHITE, 'h3')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(false, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->play(Converter::toObject(Symbol::BLACK, 'Nc2')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(false, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(true, $game->play(Converter::toObject(Symbol::BLACK, 'Rxg2+')));
        $this->assertEquals(true, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(false, $game->isChecked(Symbol::BLACK));
    }

    public function testCheckandCheckmate()
    {
        $pieces = [
            new Pawn(Symbol::WHITE, 'd5'),
            new Queen(Symbol::WHITE, 'f5'),
            new King(Symbol::WHITE, 'g2'),
            new Pawn(Symbol::WHITE, 'h2'),
            new Rook(Symbol::WHITE, 'h8', RookType::CASTLING_LONG),
            new King(Symbol::BLACK, 'e7'),
            new Pawn(Symbol::BLACK, 'f7'),
            new Pawn(Symbol::BLACK, 'g7'),
            new Pawn(Symbol::BLACK, 'h7')
        ];

        $castling = (object) [
            Symbol::WHITE => (object) [
                'castled' => true,
                Symbol::CASTLING_SHORT => false,
                Symbol::CASTLING_LONG => false
            ],
            Symbol::BLACK => (object) [
                'castled' => true,
                Symbol::CASTLING_SHORT => false,
                Symbol::CASTLING_LONG => false
            ]
        ];

        $game = new Game;
        $board = new Board($pieces, $castling);
        $game->setBoard($board);

        $this->assertEquals(true, $game->play(Converter::toObject(Symbol::WHITE, 'd6+')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(true, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->isMated(Symbol::WHITE));
        $this->assertEquals(false, $game->isMated(Symbol::BLACK));
        $this->assertEquals(false, $game->play(Converter::toObject(Symbol::BLACK, 'Kd7')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(true, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->isMated(Symbol::WHITE));
        $this->assertEquals(false, $game->isMated(Symbol::BLACK));
        $this->assertEquals(false, $game->play(Converter::toObject(Symbol::BLACK, 'Ke6')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(true, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->isMated(Symbol::WHITE));
        $this->assertEquals(false, $game->isMated(Symbol::BLACK));
        $this->assertEquals(true, $game->play(Converter::toObject(Symbol::BLACK, 'Kxd6')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(false, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->isMated(Symbol::WHITE));
        $this->assertEquals(false, $game->isMated(Symbol::BLACK));
        $this->assertEquals(true, $game->play(Converter::toObject(Symbol::WHITE, 'Re8')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(false, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->isMated(Symbol::WHITE));
        $this->assertEquals(false, $game->isMated(Symbol::BLACK));
        $this->assertEquals(true, $game->play(Converter::toObject(Symbol::BLACK, 'Kc7')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(false, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->isMated(Symbol::WHITE));
        $this->assertEquals(false, $game->isMated(Symbol::BLACK));
        $this->assertEquals(true, $game->play(Converter::toObject(Symbol::WHITE, 'Re7+')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(true, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->isMated(Symbol::WHITE));
        $this->assertEquals(false, $game->isMated(Symbol::BLACK));
        $this->assertEquals(true, $game->play(Converter::toObject(Symbol::BLACK, 'Kd8')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(false, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->isMated(Symbol::WHITE));
        $this->assertEquals(false, $game->isMated(Symbol::BLACK));
        $this->assertEquals(true, $game->play(Converter::toObject(Symbol::WHITE, 'Qd7#')));
        $this->assertEquals(false, $game->isChecked(Symbol::WHITE));
        $this->assertEquals(true, $game->isChecked(Symbol::BLACK));
        $this->assertEquals(false, $game->isMated(Symbol::WHITE));
        $this->assertEquals(true, $game->isMated(Symbol::BLACK));
    }
}
