<?php

namespace CodeInGame\LegendsOfCodeMagic\Action;

use CodeInGame\LegendsOfCodeMagic\Card\CardCollection;

class DraftAction
{
    private $cardCollection;
    private $cardEvaluator;

    public function __construct(CardCollection $cardCollection)
    {
        $this->cardCollection = $cardCollection;

        $this->cardEvaluator = new CardEvaluator();
    }

    public function getActions(): array
    {
        $picks = [];
        
        $pick = 0;
        $value = 0;

        foreach ($this->cardCollection->listAll() as $cardData) {
            $score = $this->cardEvaluator->getScore($cardData['card']);

            if ($score > $value) {
                $pick = $cardData['card']->getInstanceId() + 3;
                $value = $score;
            }
        }

        return ['PICK ' . $pick];
    }
}
