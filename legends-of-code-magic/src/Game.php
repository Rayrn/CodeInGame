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

    public function __construct(Player $player, Opponent $opponent)
    {
        $this->player = $player;
        $this->opponent = $opponent;

        $this->board = new CardReferenceCollection();
        $this->cardCollection = new CardCollection();
        $this->cardFactory = new CardFactory();
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

    public function applyOpponentsActions(): void
    {
        $actions = $this->opponent->getActions();

        foreach ($actions as $action) {
            $card = $this->cardCollection->find($action['cardNumber'], self::LOCATION_BOARD_OPPONENT);

            debug("{$card->getNumber()}, {$action['action']}");
        }

        $this->opponent->clearActions();
    }

    public function updateState(array $cardData): void
    {
        foreach ($cardData as $data) {
            [$card, $location] = $data;

            if (in_array($location, [self::LOCATION_BOARD_OPPONENT, self::LOCATION_BOARD_PLAYER])) {
                $this->cardCollection->add($card, $location);
                $this->board->add($card->getInstanceId());
            }
        }
    }

    public function cleanup(): void
    {
        $this->cardCollection->clear();
        $this->board->clear();
    }

    public function getPlayerActions(): string
    {
        $playerActions = [];
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
        $playerActions[] = 'PASS';

        return implode(';', $playerActions) . "\n";
    }
}
