<?php
namespace CodeInGame\LegendsOfCodeMagic {
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
    public function __construct(int $instanceId, int $number, int $type, int $cost, int $attack, int $defense, string $abilities, int $myHealthChange, int $opponentHealthChange, int $draw)
    {
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
    public function getNumber() : int
    {
        return $this->number;
    }
    public function getInstanceId() : int
    {
        return $this->instanceId;
    }
    public function getAttack() : int
    {
        return $this->attack;
    }
    public function getDefense() : int
    {
        return $this->defense;
    }
    public function getCost() : int
    {
        return $this->cost;
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic {
class CardCollection
{
    protected $collection = [];
    public function add(Card $card) : void
    {
        $this->collection[$card->getInstanceId()] = $card;
    }
    public function clear() : void
    {
        $this->collection = [];
    }
    public function find(int $cardNumber, int $location) : ?Card
    {
        foreach ($this->collection as $cardData) {
            if ($cardData['location'] !== $location) {
                continue;
            }
            if ($cardData['card']->getNumber() == $cardNumber) {
                return $cardData['card'];
            }
        }
        return null;
    }
    public function list() : array
    {
        return $this->collection;
    }
    public function remove(int $instanceId) : void
    {
        unset($this->collection[$instanceId]);
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic {
class CardFactory
{
    private $dictionary = [];
    public function addTemplate(int $number, int $type, int $cost, int $attack, int $defense, string $abilities, int $myHealthChange, int $opponentHealthChange, int $draw)
    {
        if (empty($this->dictionary[$number])) {
            $this->dictionary[$number] = [$number, $type, $cost, $attack, $defense, $abilities, $myHealthChange, $opponentHealthChange, $draw];
        }
    }
    public function create(int $number, int $instanceId) : Card
    {
        $template = $this->dictionary[$number];
        return new Card($instanceId, ...$template);
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic {
class CardReferenceCollection
{
    protected $collection = [];
    public function add(int $instanceId) : void
    {
        $this->collection[$instanceId] = $instanceId;
    }
    public function clear() : void
    {
        $this->collection = [];
    }
    public function list() : array
    {
        return $this->collection;
    }
    public function remove(int $instanceId) : void
    {
        unset($this->collection[$instanceId]);
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic {
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
    public function getBoard() : CardReferenceCollection
    {
        return $this->board;
    }
    public function getCardCollection() : CardCollection
    {
        return $this->cardCollection;
    }
    public function getCardFactory() : CardFactory
    {
        return $this->cardFactory;
    }
    public function getPlayer() : Player
    {
        return $this->player;
    }
    public function getOpponent() : Opponent
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
    public function getPlayerActions() : array
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
}

namespace CodeInGame\LegendsOfCodeMagic {
class Opponent extends Player
{
    private $cardsInHand = 0;
    private $actions = [];
    public function updateState(int $health, int $mana, int $rune, int $draw, int $cardsInHand = 0, array $actions = []) : void
    {
        parent::updateState($health, $mana, $rune, $draw);
        $this->cardsInHand = $cardsInHand;
        $this->actions = $actions;
    }
    public function getCardsInHand() : int
    {
        return $this->cardsInHand;
    }
    public function getActions() : array
    {
        return $this->actions;
    }
    public function clearActions() : void
    {
        $this->actions = [];
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic {
class Player
{
    protected $health;
    protected $mana;
    protected $rune;
    protected $draw;
    protected $deck;
    public function __construct()
    {
        $this->deck = new CardReferenceCollection();
    }
    public function updateState(int $health, int $mana, int $rune, int $draw) : void
    {
        $this->health = $health;
        $this->mana = $mana;
        $this->rune = $rune;
        $this->draw = $draw;
    }
    public function getHealth() : int
    {
        return $this->health;
    }
    public function getMana() : int
    {
        return $this->mana;
    }
    public function getRune() : int
    {
        return $this->rune;
    }
    public function getDraw() : int
    {
        return $this->draw;
    }
    public function getDeckDefinition()
    {
        return $this->deck;
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic {
$game = new Game(new CardFactory(), new Player(), new Opponent());
// game loop
while (true) {
    $game->updateState();
    $game->applyOpponentsActions();
    $playerActions = $game->getPlayerActions();
    echo implode(';', $playerActions) . "\n";
    $game->cleanup();
}
/**
 * To debug (equivalent to var_dump)
 */
function debug($var)
{
    error_log(var_export($var, true));
}
}

namespace CodeInGame\LegendsOfCodeMagic {
class StateReader
{
    private $game;
    public function __construct(Game $game)
    {
        $this->game = $game;
    }
    public function updateState() : void
    {
        $this->updatePlayerState();
        $this->updateOpponentState();
        $this->updateBoardState();
    }
    private function updatePlayerState() : void
    {
        fscanf(STDIN, "%d %d %d %d %d", $health, $mana, $cardsInDeck, $rune, $draw);
        $this->game->getPlayer()->updateState($health, $mana, $rune, $draw);
    }
    private function updateOpponentState() : void
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
    private function updateBoardState() : void
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
}

