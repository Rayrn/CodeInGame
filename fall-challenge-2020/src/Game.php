<?php

namespace CodeInGame\LegendsOfCodeMagic;

use CodeInGame\LegendsOfCodeMagic\Action\BattleAction;
use CodeInGame\LegendsOfCodeMagic\Action\DraftAction;
use CodeInGame\LegendsOfCodeMagic\Card\Card;
use CodeInGame\LegendsOfCodeMagic\Card\CardCollection;
use CodeInGame\LegendsOfCodeMagic\Card\CardFactory;
use CodeInGame\LegendsOfCodeMagic\Card\CardReferenceCollection;
use CodeInGame\LegendsOfCodeMagic\Player\Opponent;
use CodeInGame\LegendsOfCodeMagic\Player\Player;

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

    public function updateState(array $cardData): void
    {
        foreach ($cardData as $data) {
            [$card, $location] = $data;

            $this->cardCollection->add($card, $location);
            $this->board->add($card->getInstanceId());
        }
    }

    public function applyOpponentsActions(): void
    {
        $actions = $this->opponent->getActions();

        foreach ($actions as $action) {
            $card = $this->cardCollection->find($action['cardNumber'], self::LOCATION_BOARD_OPPONENT);

            // new Debug("{$card->getNumber()}, {$action['action']}");
        }

        $this->opponent->clearActions();
    }

    public function getPlayerActions(): string
    {
        $playerActions = in_array(-1, $this->board->list())
            ? (new DraftAction($this->cardCollection))->getActions()
            : (new BattleAction($this->cardCollection, $this->player, $this->opponent))->getActions();

        return implode(';', $playerActions) . "\n";
    }

    public function cleanup(): void
    {
        $this->cardCollection->clear();
        $this->board->clear();
    }
}
