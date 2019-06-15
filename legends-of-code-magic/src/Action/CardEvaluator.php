<?php

namespace CodeInGame\LegendsOfCodeMagic\Action;

use CodeInGame\LegendsOfCodeMagic\Card\Card;

class CardEvaluator
{
    public function getScore(Card $card): float
    {
        $score = $card->getCost() === 0 ? 0 : ($card->getAttack() + $card->getDefense()) / $card->getCost();

        if (intval($card->getAttack() - $card->getDefense()) >= $card->getCost()) {
            $score = ($score / 3) * 2;
        }

        return $score;
    }
}
