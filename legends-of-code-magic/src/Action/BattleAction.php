<?php

namespace CodeInGame\LegendsOfCodeMagic\Action;

use CodeInGame\LegendsOfCodeMagic\Game;
use CodeInGame\LegendsOfCodeMagic\Card\CardCollection;
use CodeInGame\LegendsOfCodeMagic\Player\Player;
use CodeInGame\LegendsOfCodeMagic\Player\Opponent;
use CodeInGame\LegendsOfCodeMagic\Debug;

class BattleAction
{
    private $cardCollection;
    private $cardEvaluator;
    private $player;
    private $opponent;

    public function __construct(CardCollection $cardCollection, Player $player, Opponent $opponent)
    {
        $this->cardCollection = $cardCollection;
        $this->player = $player;
        $this->opponent = $opponent;

        $this->cardEvaluator = new CardEvaluator();
    }

    public function getActions(): array
    {
        $actions = [];

        $actions = array_merge($actions, $this->getSummons());
        $actions = array_merge($actions, $this->getAttacks());

        return $actions;
    }

    private function getSummons(): array
    {
        $manaAvaliable = $this->player->getMana();

        $scores = [];
        $costs = [];
        
        foreach($this->cardCollection->listForLocation(Game::LOCATION_HAND_PLAYER) as $card) {
            if ($card->getCost() > $manaAvaliable) {
                continue;
            }
            
            $scores[$card->getInstanceId()] = ['score' => $this->cardEvaluator->getScore($card), 'card' => $card];
            $costs[$card->getInstanceId()] = $card->getCost();
        }
        
        $combinations = $this->filterCombinations($this->getCombinations($costs, $manaAvaliable));

        if (empty($combinations)) {
            return [];
        }

        foreach ($combinations as $key => $combination) {
            $score = 0;

            foreach ($combination as $instanceId) {
                $score += $scores[$instanceId]['score'];
            }

            $combinations[$key] = $score;
        }

        asort($combinations);

        $commands = [];
        foreach (explode('.', array_key_last($combinations)) as $instanceId) {
            $commands[] = 'SUMMON ' . $instanceId;
        }

        return $commands;
    }

    private function getCombinations(array $costs, int $totalMana): array
    {
        $combinations = [];
        foreach ($costs as $instanceId => $cost) {
            $manaAvaliable = $totalMana - $cost;

            $subCosts = $costs;
            unset($subCosts[$instanceId]);

            $subCombinations = ($manaAvaliable > 0)
                ? $this->getCombinations($subCosts, $manaAvaliable)
                : [];
            
            foreach ($subCombinations as $combination) {
                $combinations[] = array_merge($combination, [$instanceId]);
            }

            if ($cost <= $totalMana) {
                $combinations[] = [$instanceId];
            }
        }
        
        return $combinations;
    }

    private function filterCombinations(array $combinations)
    {
        $uniqueKeys = [];

        foreach ($combinations as $combination) {
            sort($combination);

            $uniqueKeys[implode('.', $combination)] = $combination;
        }

        foreach (array_keys($uniqueKeys) as $key) {
            if (strpos($key, '.') !== false) {
                foreach (explode('.', $key) as $part) {
                    unset($uniqueKeys[$part]);
                }
            }
        }
        
        return $uniqueKeys;
    }

    private function getAttacks()
    {
        $commands = [];
        foreach ($this->cardCollection->listForLocation(Game::LOCATION_BOARD_PLAYER) as $card) {
            if ($card->getType() !== 'creature') {
                continue;
            }

            $commands[] = 'ATTACK ' . $card->getInstanceId() . ' -1';
        }

        return $commands;
    }
}
