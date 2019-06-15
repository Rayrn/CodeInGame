<?php

namespace CodeInGame\LegendsOfCodeMagic;

class Game
{
    public const LOCATION_BOARD_PLAYER = 1;
    public const LOCATION_HAND_PLAYER = 0;
    public const LOCATION_BOARD_OPPONENT = -1;

    private $board;
    private $cardCollection;
    private $cardFactory;
    private $player;
    private $opponent;

    public function __construct(CardFactory $cardFactory, Player $player, Opponent $opponent)
    {
        $this->cardFactory = $cardFactory;
        $this->player = $player;
        $this->opponent = $opponent;

        $this->board = new CardReferenceCollection();
        $this->cardCollection = new CardCollection();
    }

    public function getBoard(): CardReferenceCollection
    {
        return $this->board;
    }

    public function getCardCollection(): CardCollection
    {
        return $this->cardCollection;
    }

    public function getCardFactory(): CardFactory
    {
        return $this->cardFactory;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getOpponent(): Opponent
    {
        return $this->opponent;
    }

    public function applyOpponentsActions()
    {
        $actions = $this->opponent->getActions();

        foreach ($actions as $action) {
            $card = $this->find($action['cardNumber'], self::LOCATION_BOARD_OPPONENT);

            debug("{$card->getNumber()}, {$action['action']}");
        }

        $this->opponent->clearActions();
    }

    public function getPlayerActions(): array
    {
        // if (!$this->player->isDeckComplete()) {
        //     $pick = 0;
        //     $value = 0;

        //     foreach ($this->cardCollection as $cardData) {
        //         $card = $cardData['card'];

        //         $costStatsRatio = $card->getCost() === 0 ? 0 : ($card->getAttack() + $card->getDefense()) / $card->getCost();

        //         if (intval($card->getAttack() - $card->getDefense()) >= $card->getCost()) {
        //             $costStatsRatio = ($costStatsRatio / 3) * 2;
        //         }

        //         if ($costStatsRatio > $value) {
        //             $pick = $card->getInstanceId();
        //             $value = $costStatsRatio;
        //         }
        //     }

        //     debug($this->cardCollection);

        //     return [$pick];
        // }

        return ['PASS'];
    }
}
