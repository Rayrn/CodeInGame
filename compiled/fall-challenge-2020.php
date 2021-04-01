<?php
namespace CodeInGame\FallChallenge2020 {
class Debug
{
    public function __construct($entity)
    {
        error_log(var_export($entity, true));
    }
}
}

namespace CodeInGame\FallChallenge2020\Entity {
use ArrayIterator;
use IteratorAggregate;
class Book implements IteratorAggregate
{
    /**
     * @var Items[]
     */
    private $items = [];
    public function add(Item ...$items)
    {
        foreach ($items as $item) {
            $this->items[$item->getId()] = $item;
        }
    }
    public function remove(Item $item) : void
    {
        unset($this->items[$item->getId()]);
    }
    public function list() : array
    {
        return $this->items;
    }
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->list());
    }
}
}

namespace CodeInGame\FallChallenge2020\Entity {
class Cupboard
{
    /**
     * @var array
     */
    private $ingredients;
    /**
     * @var int
     */
    private $rupees;
    public function __construct(int $ingredientZeroCount, int $ingredientOneCount, int $ingredientTwoCount, int $ingredientThreeCount, int $rupees)
    {
        $this->ingredients[] = $ingredientZeroCount;
        $this->ingredients[] = $ingredientOneCount;
        $this->ingredients[] = $ingredientTwoCount;
        $this->ingredients[] = $ingredientThreeCount;
        $this->rupees = $rupees;
    }
    public function canMake(Item $item) : bool
    {
        foreach ($item->getIngredients() as $key => $count) {
            if ($this->ingredients[$key] - $count < 0) {
                return false;
            }
        }
        return true;
    }
    public function make(Item $item) : bool
    {
        if (!$this->canMake($item)) {
            return false;
        }
        foreach ($item->getIngredients() as $key => $count) {
            $this->ingredients[$key] -= $count;
        }
        return true;
    }
    public function toArray() : array
    {
        return ['ingredients' => $this->ingredients, 'rupees' => $this->rupees];
    }
}
}

namespace CodeInGame\FallChallenge2020\Entity {
use InvalidArgumentException;
class Item
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var array
     */
    protected $ingredients;
    public function getId() : int
    {
        return $this->id;
    }
    public function getIngredients() : array
    {
        return $this->ingredients;
    }
}
}

namespace CodeInGame\FallChallenge2020\Entity {
use InvalidArgumentException;
class Recipe extends Item
{
    /**
     * @var int
     */
    private $price;
    public function __construct(int $id, array $ingredients, int $price)
    {
        $this->id = $id;
        $this->ingredients = array_map(function ($cost) {
            return (int) $cost;
        }, $ingredients);
        $this->price = $price;
    }
    public function getPrice() : int
    {
        return $this->price;
    }
}
}

namespace CodeInGame\FallChallenge2020\Entity {
use InvalidArgumentException;
class Spell extends Item
{
    /**
     * @var bool
     */
    private $castable;
    public function __construct(int $id, array $ingredients, bool $castable)
    {
        $this->id = $id;
        $this->ingredients = array_map(function ($cost) {
            return (int) $cost;
        }, $ingredients);
        $this->castable = $castable;
    }
    public function isCastable() : bool
    {
        return $this->castable;
    }
}
}

namespace CodeInGame\FallChallenge2020\Factory {
use CodeInGame\FallChallenge2020\Entity\Book;
use CodeInGame\FallChallenge2020\Entity\Item;
use CodeInGame\FallChallenge2020\Entity\Recipe;
use CodeInGame\FallChallenge2020\Entity\Spell;
class Printer
{
    public function writeBook(Item ...$items) : Book
    {
        $book = new Book();
        $book->add(...$items);
        return $book;
    }
    public function writeRecipe(int $id, array $ingredients, int $price) : Recipe
    {
        return new Recipe($id, $ingredients, $price);
    }
    public function writeSpell(int $id, array $ingredients, bool $castabble) : Spell
    {
        return new Spell($id, $ingredients, $castabble);
    }
}
}

namespace CodeInGame\FallChallenge2020\Factory {
use CodeInGame\FallChallenge2020\Entity\Cupboard;
class Workshop
{
    public function build(int $ingredientZeroCount, int $ingredientOneCount, int $ingredientTwoCount, int $ingredientThreeCount, int $rupees) : Cupboard
    {
        return new Cupboard($ingredientZeroCount, $ingredientOneCount, $ingredientTwoCount, $ingredientThreeCount, $rupees);
    }
}
}

namespace CodeInGame\FallChallenge2020 {
use CodeInGame\FallChallenge2020\Entity\Cupboard;
use CodeInGame\FallChallenge2020\Factory\Printer;
use CodeInGame\FallChallenge2020\Entity\Book;
use CodeInGame\FallChallenge2020\Entity\Recipe;
class Game
{
    /**
     * @var gameState
     */
    private $gameState;
    /**
     * @var Printer
     */
    private $printer;
    public function __construct(GameState $gameState, Printer $printer)
    {
        $this->gameState = $gameState;
        $this->printer = $printer;
    }
    public function getGameState() : GameState
    {
        return $this->gameState;
    }
    public function process() : string
    {
        // Start by seeing if there are any potions we can make
        $brewable = $this->getBrewable();
        if (!$brewable) {
            return 'BREW ' . reset($brewable->list())->getId();
        }
        // If we can't brew anything, find the most valuable potion to start working towards
        // Output the ID of the potion we made
        return 'WAIT';
    }
    private function getBrewable() : Book
    {
        $brewable = array_filter($this->gameState->getOrders()->list(), function (Recipe $recipe) {
            return $this->gameState->getPlayerCupboard()->canMake($recipe);
        });
        usort($brewable, function (Recipe $recipeA, Recipe $recipeB) {
            return $recipeA->getPrice() < $recipeB->getPrice();
        });
        return $this->printer->writeBook(...$brewable);
    }
    private function getEffort()
    {
        foreach ($this->gameState->getOrders() as $recipe) {
            # code...
        }
    }
}
}

namespace CodeInGame\FallChallenge2020 {
use CodeInGame\FallChallenge2020\Entity\Book;
use CodeInGame\FallChallenge2020\Entity\Cupboard;
class GameState
{
    /**
     * @var Cupboard
     */
    private $opponentCupboard;
    /**
     * @var Cupboard
     */
    private $opponentSpells;
    /**
     * @var Cupboard
     */
    private $playerCupboard;
    /**
     * @var Cupboard
     */
    private $playerSpells;
    /**
     * @var Book
     */
    private $orders;
    public function getOrders() : ?Book
    {
        return $this->orders;
    }
    public function getOpponentCupboard() : ?Cupboard
    {
        return $this->opponentCupboard;
    }
    public function getOpponentSpells() : ?Book
    {
        return $this->opponentSpells;
    }
    public function getPlayerCupboard() : ?Cupboard
    {
        return $this->playerCupboard;
    }
    public function getPlayerSpells() : ?Book
    {
        return $this->playerSpells;
    }
    public function setOrders(Book $orders)
    {
        $this->orders = $orders;
    }
    public function setOpponentCupboard(Cupboard $cupboard)
    {
        $this->opponentCupboard = $cupboard;
    }
    public function setOpponentSpells(Book $spells)
    {
        $this->opponentSpells = $spells;
    }
    public function setPlayerCupboard(Cupboard $cupboard)
    {
        $this->playerCupboard = $cupboard;
    }
    public function setPlayerSpells(Book $spells)
    {
        $this->playerSpells = $spells;
    }
}
}

namespace CodeInGame\FallChallenge2020 {
use CodeInGame\FallChallenge2020\Factory\Printer;
use CodeInGame\FallChallenge2020\Factory\Workshop;
$game = new Game(new GameState(), new Printer());
$stateReader = new StateReader($game, new Printer(), new Workshop());
// game loop
while (true) {
    $stateReader->updateState();
    echo $game->process() . PHP_EOL;
}
}

namespace CodeInGame\FallChallenge2020 {
use CodeInGame\FallChallenge2020\Factory\Printer;
use CodeInGame\FallChallenge2020\Factory\Workshop;
class StateReader
{
    /**
     * @var Game
     */
    private $game;
    /**
     * @var Printer
     */
    private $printer;
    /**
     * @var Workshop
     */
    private $workshop;
    public function __construct(Game $game, Printer $printer, Workshop $workshop)
    {
        $this->game = $game;
        $this->printer = $printer;
        $this->workshop = $workshop;
    }
    public function updateState() : void
    {
        $this->updateRecipeCollection();
        $this->updateCupboards();
    }
    private function updateRecipeCollection() : void
    {
        $items = ['orders' => [], 'playerSpells' => [], 'opponentSpells' => []];
        fscanf(STDIN, "%d", $actionCount);
        for ($i = 0; $i < $actionCount; $i++) {
            fscanf(STDIN, "%d %s %d %d %d %d %d %d %d %d %d", $id, $type, $ingredientCost0, $ingredientCost1, $ingredientCost2, $ingredientCost3, $price, $tomeIndex, $tax, $castable, $repeatable);
            switch ($type) {
                case 'BREW':
                    $items['orders'][] = $this->printer->writeRecipe($id, [$ingredientCost0, $ingredientCost1, $ingredientCost2, $ingredientCost3], $price);
                    break;
                case 'CAST':
                    $items['playerSpells'][] = $this->printer->writeSpell($id, [$ingredientCost0, $ingredientCost1, $ingredientCost2, $ingredientCost3], $castable);
                    break;
                case 'OPPONENT_CAST':
                    $items['opponentSpells'][] = $this->printer->writeSpell($id, [$ingredientCost0, $ingredientCost1, $ingredientCost2, $ingredientCost3], $castable);
                    break;
                default:
                    new Debug($type);
                    break;
            }
        }
        $this->game->getGameState()->setOrders($this->printer->writeBook(...$items['orders']));
        $this->game->getGameState()->setPlayerSpells($this->printer->writeBook(...$items['playerSpells']));
        $this->game->getGameState()->setOpponentSpells($this->printer->writeBook(...$items['opponentSpells']));
    }
    private function updateCupboards() : void
    {
        $cupboards = [];
        for ($i = 0; $i < 2; $i++) {
            fscanf(STDIN, "%d %d %d %d %d", $ingredientZero, $ingredientOne, $ingredientTwo, $ingredientThree, $score);
            $cupboards[] = $this->workshop->build($ingredientZero, $ingredientOne, $ingredientTwo, $ingredientThree, $score);
        }
        $this->game->getGameState()->setPlayerCupboard($cupboards[0]);
        $this->game->getGameState()->setOpponentCupboard($cupboards[1]);
    }
}
}

