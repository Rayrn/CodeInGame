<?php

namespace CodeInGame\LegendsOfCodeMagic;

class StateReader
{
    private $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function updateState(): void
    {
        $this->updatePlayerState();
        $this->updateOpponentState();
        $this->updateBoardState();
    }

    private function updatePlayerState(): void
    {
        fscanf(STDIN, "%d %d %d %d %d", $health, $mana, $cardsInDeck, $rune, $draw);

        $this->game->getPlayer()->updateState($health, $mana, $rune, $draw);
    }

    private function updateOpponentState(): void
    {
        fscanf(STDIN, "%d %d %d %d %d", $health, $mana, $cardsInDeck, $rune, $draw);
        fscanf(STDIN, "%d %d", $cardsInHand, $action);

        $actions = [];
        for ($i = 0; $i < $action; $i++) {
            [$cardNumber, $action] = explode(' ', stream_get_line(STDIN, 20 + 1, "\n"));

            $actions[] = ['cardNumber' => $cardNumber, 'action' => $action];
        }

        $this->game->getOpponent()->updateState($health, $mana, $rune, $draw, $cardsInHand, $actions);
    }

    private function updateBoardState(): void
    {
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
        
        foreach ($this->cardCollection as $instanceId => $cardData) {
            if (in_array($cardData['location'], [self::LOCATION_BOARD_OPPONENT, self::LOCATION_BOARD_PLAYER])) {
                $this->board->add($instanceId);
            }
        }
    }
}
