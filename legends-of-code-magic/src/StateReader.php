<?php

namespace CodeInGame\LegendsOfCodeMagic;

class StateReader
{
    private $cardFactory;
    private $game;

    public function __construct(Game $game)
    {
        $this->game = $game;

        $this->cardFactory = new CardFactory();
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

        $cardData = [];
        for ($i = 0; $i < $cardCount; $i++) {
            fscanf(STDIN, "%d %d %d %d %d %d %d %s %d %d %d", $number, $instanceId, $location, $type, $cost, $att, $def, $abi, $myhealth, $opphealth, $draw);


            if ($instanceId == '-1') {
                $instanceId = $i;
            }

            $cardData[] = [$this->cardFactory->create($number, $instanceId), $location];
        }

        $this->game->updateState($cardData);
    }
}
