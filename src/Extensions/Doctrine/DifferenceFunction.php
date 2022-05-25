<?php


namespace App\Extensions\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * DifferenceFunction ::= "DIFFERENCE" "(" StringPrimary "," StringPrimary ")
 */
class DifferenceFunction extends FunctionNode
{

    public $firstString = null;
    public $secondString = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstString = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondString = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
//        return 'DIFFERENCE(' .
//            $this->stringExpression->dispatch($sqlWalker) .
//            ')';
        return 'DIFFERENCE(' .
            $this->firstString->dispatch($sqlWalker) . ', ' .
            $this->secondString->dispatch($sqlWalker) .
            ')';
    }

}