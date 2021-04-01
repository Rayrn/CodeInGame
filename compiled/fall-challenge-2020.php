<?php
namespace CodeInGame\FallChallenge2020 {
class Debug
{
    public function __construct(...$entity)
    {
        foreach ($entity as $item) {
            error_log(var_export($item, true));
        }
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
    public function getIngredients() : array
    {
        return $this->ingredients;
    }
    public function getRupees() : int
    {
        return $this->rupees;
    }
    public function canMake(Item $item) : bool
    {
        foreach ($item->getIngredientCost() as $key => $count) {
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
        foreach ($item->getIngredientCost() as $key => $count) {
            $this->ingredients[$key] - $count;
        }
        return true;
    }
    public function listUseable(Book $book) : Book
    {
        $newBook = clone $book;
        foreach ($newBook as $item) {
            if (!$this->canMake($item)) {
                $newBook->remove($item);
            }
        }
        return $newBook;
    }
    public function listMissingIngredients(Item $item) : array
    {
        $required = $item->getIngredientCost();
        $missing = [];
        foreach ($required as $key => $count) {
            if ($this->ingredients[$key] < $count) {
                $missing[$key] = abs($this->ingredients[$key] - $count);
            }
        }
        return $missing;
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
    public function getIngredientGain() : array
    {
        $ingredientGain = [];
        foreach ($this->ingredients as $key => $count) {
            if ($count > 0) {
                $ingredientGain[$key] = $count;
            }
        }
        return $ingredientGain;
    }
    public function getIngredientCost() : array
    {
        $ingredientGain = [];
        foreach ($this->ingredients as $key => $count) {
            if ($count < 0) {
                $ingredientGain[$key] = abs($count);
            }
        }
        return $ingredientGain;
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
use CodeInGame\FallChallenge2020\Entity\Item;
use CodeInGame\FallChallenge2020\Entity\Spell;
class Game
{
    /**
     * @var gameState
     */
    private $gameState;
    /**
     * @var Brewer
     */
    private $brewer;
    /**
     * @var Mage
     */
    private $mage;
    public function __construct(GameState $gameState, Brewer $brewer, Mage $mage)
    {
        $this->gameState = $gameState;
        $this->hats['brewer'] = $brewer;
        $this->hats['mage'] = $mage;
    }
    public function getGameState() : GameState
    {
        return $this->gameState;
    }
    public function process() : string
    {
        // Supplies!
        $cupboard = $this->gameState->getPlayerCupboard();
        // Actions!
        $orders = $this->gameState->getOrders();
        $spells = $this->gameState->getPlayerSpells();
        // Start by seeing if there are any potions we can make
        $brewable = $cupboard->listUseable($orders);
        // If we can make something, do!
        if (count($brewable->list()) > 0) {
            return $this->hats['brewer']->makeRecipe($brewable);
        }
        // Okay, we can't make anything. Can we cast anything?
        $castable = $cupboard->listUseable($spells);
        // If we can't cast anything, rest!
        if (count($castable->list()) == 0) {
            return 'REST';
        }
        // Find the most valuable recipe to start working towards
        $recipe = $this->hats['brewer']->getBestRecipe($cupboard, $orders);
        // Find the most valuable spell for the recipe (probably FIREBALL)
        $spell = $this->hats['mage']->getBestSpell($cupboard, $castable, $recipe);
        // FIREBALL!!!!
        return $this->hats['mage']->castSpell($spell);
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

namespace CodeInGame\FallChallenge2020\Helper {
use CodeInGame\FallChallenge2020\Entity\Cupboard;
use CodeInGame\FallChallenge2020\Entity\Recipe;
class PrepTimeCalculator
{
    /**
     * How long would it take us to make this recipe?
     */
    public function calculatePrepTime(Cupboard $cupboard, Recipe $recipe) : int
    {
        $missing = $cupboard->listMissingIngredients($recipe);
        // For now, lets just hard-code this as the spells all exchange one ingredient for the level above
        $timeToPrep = 0;
        foreach ($missing as $level => $count) {
            $timeToPrep += $level * $count;
        }
        // Add in level 0 generation
        $missingRawIngredientCount = array_sum($cupboard->getIngredients()) - array_sum($recipe->getIngredientCost());
        if ($missingRawIngredientCount > 0) {
            $timeToPrep += ceil($missingRawIngredientCount / 2);
        }
        // Add in rests
        $timeToPrep += max($missing);
        return $timeToPrep;
    }
}
}

namespace CodeInGame\FallChallenge2020 {
use CodeInGame\FallChallenge2020\Factory\Printer;
use CodeInGame\FallChallenge2020\Factory\Workshop;
use CodeInGame\FallChallenge2020\Helper\PrepTimeCalculator;
use CodeInGame\FallChallenge2020\Worker\Brewer;
use CodeInGame\FallChallenge2020\Worker\Mage;
// I miss autowiring already
$game = new Game(new GameState(), new Brewer(new PrepTimeCalculator()), new Mage());
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

namespace CodeInGame\FallChallenge2020\Worker {
use CodeInGame\FallChallenge2020\Entity\Cupboard;
use CodeInGame\FallChallenge2020\Entity\Book;
use CodeInGame\FallChallenge2020\Entity\Recipe;
use CodeInGame\FallChallenge2020\Helper\PrepTimeCalculator;
class Brewer
{
    /**
     * @var PrepTimeCalculator
     */
    private $prepTimeCalculator;
    public function __construct(PrepTimeCalculator $prepTimeCalculator)
    {
        $this->prepTimeCalculator = $prepTimeCalculator;
    }
    /**
     * Generate the make recipe command for the most expensive recipe in the book
     */
    public function makeRecipe(Book $book)
    {
        usort($book, function (Recipe $recipeA, Recipe $recipeB) {
            return $recipeA->getPrice() < $recipeB->getPrice();
        });
        return 'BREW ' . reset($book->list())->getId();
    }
    /**
     * Find the most valuable recipe (based on time to make) in the current round
     */
    public function getBestRecipe(Cupboard $cupboard, Book $orders) : Recipe
    {
        // Check how long each will take
        $prepTimes = [];
        foreach ($orders as $recipe) {
            $prepTime = $this->prepTimeCalculator->calculatePrepTime($cupboard, $recipe);
            $prepTimes[$prepTime][] = $recipe;
        }
        // Sort into ROI => Time
        $roi = [];
        foreach ($prepTimes as $time => $recipes) {
            foreach ($recipes as $recipe) {
                $actionRoI = $recipe->getPrice() / $time * 1000;
                $roi[$actionRoI][$time][] = $recipe;
            }
        }
        // Get the most valuable ROI set first
        $mostValuable = $roi[max(array_keys($roi))];
        // Then find the quickest
        $quickest = $mostValuable[min(array_keys($mostValuable))];
        // Return the first item (as they're all theoretically identical at this point)
        return reset($quickest);
    }
}
}

namespace CodeInGame\FallChallenge2020\Worker {
use CodeInGame\FallChallenge2020\Entity\Cupboard;
use CodeInGame\FallChallenge2020\Entity\Book;
use CodeInGame\FallChallenge2020\Entity\Spell;
class Mage
{
    /**
     * Try to generate the command to cast a spell, or rest if thats impossible
     */
    public function castSpell(?Spell $spell)
    {
        return $spell ? 'CAST ' . $spell->getId() : 'REST';
    }
    /**
     * Find the most valuable recipe (based on time to make) in the current round
     */
    private function getBestSpell(Cupboard $cupboard, Book $spellbook, Recipe $recipe) : ?Spell
    {
        $cupboard = $this->gameState->getPlayerCupboard();
        $missingIngredients = $cupboard->listMissingIngredients($recipe);
        // Deal with the first use case a little differently
        if (array_sum($missingIngredients) > array_sum($cupboard->getIngredients())) {
            foreach ($spellbook as $spell) {
                if (array_key_exists(0, $spell->getIngredientGain())) {
                    return $spell;
                }
            }
        }
        // Find the first spell that makes an ingredient of the required level
        foreach (array_keys($missingIngredients) as $level) {
            foreach ($spellbook as $spell) {
                if (array_key_exists($level, $spell->getIngredientGain())) {
                    return $spell;
                }
            }
        }
        return null;
    }
}
}

