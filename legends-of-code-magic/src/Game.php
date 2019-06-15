<?php

namespace CodeInGame\LegendsOfCodeMagic;

class Game
{
    public const LOCATION_BOARD_PLAYER = 1;
    public const LOCATION_HAND_PLAYER = 0;
    public const LOCATION_BOARD_OPPONENT = -1;

    private $cardCollection;
    private $cardFactory;
    private $player;
    private $opponent;

    public function __construct(CardFactory $cardFactory, Player $player, Opponent $opponent)
    {
        $this->cardFactory = $cardFactory;
        $this->player = $player;
        $this->opponent = $opponent;

        $this->board = new CardCollection();
        $this->cardCollection = [];
    }

    public function updateState(): void
    {
        $this->player->updateState();
        $this->opponent->updateState();

        fscanf(STDIN, "%d", $cardCount);

        for ($i = 0; $i < $cardCount; $i++) {
            fscanf(STDIN, "%d %d %d %d %d %d %d %s %d %d %d", $number, $instanceId, $location, $type, $cost, $att, $def, $abi, $myhealth, $opphealth, $draw);


            if ($instanceId == '-1') {
                $this->cardFactory->addTemplate($number, $type, $cost, $att, $def, $abi, $myhealth, $opphealth, $draw);
                $instanceId = $i;
            }

            $this->add($this->cardFactory->create($number, $instanceId), $location);

            $this->updateBoardState();
        }
    }

    public function applyOpponentsActions()
    {
        $actions = $this->opponent->getActions();

        foreach ($actions as $action) {
            $card = $this->find($action['cardNumber'], self::LOCATION_BOARD_OPPONENT);

            debug("$card, $action['action']");
        }

        $this->opponent->clearActions();
    }

    public function cleanup(): void
    {
        $this->cardCollection = [];
    }

    private function add(Card $card, int $location): void
    {
        $this->cardCollection[$card->getInstanceId()] = ['card' => $card, 'location' => $location];
    }

    private function find(int $cardNumber, int $location): ?Card
    {
        foreach ($this->cardCollection as $cardData) {
            if ($cardData['location'] !== $location) {
                continue;
            }

            if ($cardData['card']->getNumber() == $cardNumber) {
                return $cardData['card'];
            }
        }

        return null;
    }

    private function updateBoardState(): void
    {
        foreach ($this->cardCollection as $instanceId => $cardData) {
            if (in_array($cardData['location'], [self::LOCATION_BOARD_OPPONENT, self::LOCATION_BOARD_PLAYER])) {
                $this->board->add($instanceId);
            }
        }
    }

    public function getPlayerActions(): array
    {
        if (!$this->player->isDeckComplete()) {
            $pick = 0;
            $value = 0;

            foreach ($this->cardCollection as $cardData) {
                $card = $cardData['card'];

                $costStatsRatio = $card->getCost() === 0 ? 0 : ($card->getAttack() + $card->getDefense()) / $card->getCost();

                if (intval($card->getAttack() - $card->getDefense()) >= $card->getCost()) {
                    $costStatsRatio = ($costStatsRatio / 3) * 2;
                }

                if ($costStatsRatio > $value) {
                    $pick = $card->getInstanceId();
                    $value = $costStatsRatio;
                }
            }

            debug($this->cardCollection);

            return [$pick];
        }

        return ['PASS'];
    }
}

