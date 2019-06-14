<?php

namespace CodeInGame\LegendsOfCodeMagic;

$game = new Game(new CardFactory(), new Player(), new Opponent());

// game loop
while (true) {
    $game->updateState();
    $game->applyActions();

    debug($game->board);
    debug($game->cardCollection);

    echo("PASS\n");

    $game->cleanup();
}

/**
 * To debug (equivalent to var_dump)
 */
function debug($var)
{
    error_log(var_export($var, true));
}

class Game
{
    public const LOCATION_BOARD_PLAYER = 1;
    public const LOCATION_HAND_PLAYER = 0;
    public const LOCATION_BOARD_OPPONENT = -1;
    public const LOCATION_GRAVEYARD = -2;

    public $board;
    public $cardCollection;
    private $cardFactory;
    private $player;
    private $opponent;

    public function __construct(CardFactory $cardFactory, Player $player, Opponent $opponent)
    {
        $this->cardFactory = $cardFactory;
        $this->player = $player;
        $this->opponent = $opponent;

        $this->board = new Board();
    }

    public function updateState(): void
    {
        $this->player->updateState();
        $this->opponent->updateState();

        fscanf(STDIN, "%d", $cardCount);

        for ($i = 0; $i < $cardCount; $i++) {
            fscanf(STDIN, "%d %d %d %d %d %d %d %s %d %d %d", $number, $instanceId, $location, $type, $cost, $att, $def, $abi, $myhealth, $opphealth, $draw);

            $this->cardFactory->addTemplate($number, $type, $cost, $att, $def, $abi, $myhealth, $opphealth, $draw);

            if (array_key_exists($instanceId, $this->cardCollection)) {
                $this->update($instanceId, $location);
            } else {
                $this->add($this->cardFactory->create($number, $instanceId), $location);
            }
        }

        $this->updateBoardState();
    }

    public function applyActions()
    {
        $actions = $this->opponent->getActions();

        foreach ($actions as $action) {
            $card = $this->find($action['cardNumber'], self::LOCATION_BOARD_OPPONENT);

            $this->board->doAction($card->getInstanceId(), $action['action']);
        }

        $this->opponent->clearActions();
    }

    public function cleanup(): void
    {
        foreach ($this->cardCollection as $instanceId => $cardData) {
            if (in_array($cardData['location'], [self::LOCATION_BOARD_OPPONENT, self::LOCATION_BOARD_PLAYER])) {
                $this->update($instanceId, self::LOCATION_GRAVEYARD);
            }
        }
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

    private function update(int $instanceId, $location): void
    {
        $this->cardCollection[$instanceId]['location'] = $location;
    }

    private function updateBoardState()
    {
        foreach ($this->cardCollection as $instanceId => $cardData) {
            if (in_array($cardData['location'], [self::LOCATION_BOARD_OPPONENT, self::LOCATION_BOARD_PLAYER])) {
                $this->board->add($instanceId);
            }
        }
    }
}

class CardReferenceCollection
{
    protected $collection = [];

    public function add(int $instanceId): void
    {
        $this->collection[$instanceId] = $instanceId;
    }

    public function clear(): void
    {
        $this->collection = [];
    }
}

class Board extends CardReferenceCollection
{
    public function doAction(int $instanceId, string $action): void
    {
        foreach ($this->collection as $card)
        {
            if ($card->getInstanceId() == $instanceId) {
                $this->processAction($action, $card);
            }
        }
    }
}

class Player
{
    protected $health;
    protected $mana;
    protected $rune;
    protected $draw;

    protected $deck;
    protected $hand;

    public function __construct()
    {
        $this->deck = new CardReferenceCollection();
        $this->hand = new CardReferenceCollection();
    }

    public function updateState(): void
    {
        fscanf(STDIN, "%d %d %d %d %d", $health, $mana, $deck, $rune, $draw);

        $this->health = $health;
        $this->mana = $mana;
        $this->rune = $rune;
        $this->draw = $draw;
    }
}

class Opponent extends Player
{
    private $cardsInHand = 0;
    private $actions = [];

    public function updateState(): void
    {
        parent::updateState();

        fscanf(STDIN, "%d %d", $cardsInHand, $action);

        $this->cardsInHand = $cardsInHand;

        for ($i = 0; $i < $action; $i++) {
            [$cardNumber, $action] = explode(' ', stream_get_line(STDIN, 20 + 1, "\n"));

            $this->actions[] = ['cardNumber' => $cardNumber, 'action' => $action];
        }
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function clearActions(): void
    {
        $this->actions = [];
    }
}

class Card
{
    private $instanceId;
    private $number;
    private $type;
    private $cost;
    private $attack;
    private $defense;
    private $abilities;
    private $myHealthChange;
    private $opponentHealthChange;
    private $draw;

    public function __construct(
        int $instanceId,
        int $number,
        int $type,
        int $cost,
        int $attack,
        int $defense,
        string $abilities,
        int $myHealthChange,
        int $opponentHealthChange,
        int $draw
    ) {
        $this->instanceId = $instanceId;
        $this->number = $number;
        $this->type = $type;
        $this->cost = $cost;
        $this->attack = $attack;
        $this->defense = $defense;
        $this->abilities = $abilities;
        $this->myHealthChange = $myHealthChange;
        $this->opponentHealthChange = $opponentHealthChange;
        $this->draw = $draw;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getInstanceId(): int
    {
        return $this->instanceId;
    }
}

class CardFactory
{
    private $dictionary = [];

    public function addTemplate(
        int $number,
        int $type,
        int $cost,
        int $attack,
        int $defense,
        string $abilities,
        int $myHealthChange,
        int $opponentHealthChange,
        int $draw
    ) {
        if (empty($this->dictionary[$number])) {
            $this->dictionary[$number] = [$number, $type, $cost, $attack, $defense, $abilities, $myHealthChange, $opponentHealthChange, $draw];
        }
    }

    public function create(int $number, int $instanceId): Card
    {
        $template = $this->dictionary[$number];

        return new Card($instanceId, ...$template);
    }
}
