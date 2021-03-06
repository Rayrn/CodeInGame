<?php
namespace CodeInGame\LegendsOfCodeMagic\Action {
use CodeInGame\LegendsOfCodeMagic\Game;
use CodeInGame\LegendsOfCodeMagic\Card\CardCollection;
use CodeInGame\LegendsOfCodeMagic\Player\Player;
use CodeInGame\LegendsOfCodeMagic\Player\Opponent;
use CodeInGame\LegendsOfCodeMagic\Debug;
class BattleAction
{
    private $cardCollection;
    private $cardEvaluator;
    private $player;
    private $opponent;
    public function __construct(CardCollection $cardCollection, Player $player, Opponent $opponent)
    {
        $this->cardCollection = $cardCollection;
        $this->player = $player;
        $this->opponent = $opponent;
        $this->cardEvaluator = new CardEvaluator();
    }
    public function getActions() : array
    {
        $actions = [];
        $actions = array_merge($actions, $this->getSummons());
        $actions = array_merge($actions, $this->getAttacks());
        return $actions;
    }
    private function getSummons() : array
    {
        $manaAvaliable = $this->player->getMana();
        $scores = [];
        $costs = [];
        foreach ($this->cardCollection->listForLocation(Game::LOCATION_HAND_PLAYER) as $card) {
            if ($card->getCost() > $manaAvaliable) {
                continue;
            }
            $scores[$card->getInstanceId()] = ['score' => $this->cardEvaluator->getScore($card), 'card' => $card];
            $costs[$card->getInstanceId()] = $card->getCost();
        }
        $combinations = $this->filterCombinations($this->getCombinations($costs, $manaAvaliable));
        if (empty($combinations)) {
            return [];
        }
        foreach ($combinations as $key => $combination) {
            $score = 0;
            foreach ($combination as $instanceId) {
                $score += $scores[$instanceId]['score'];
            }
            $combinations[$key] = $score;
        }
        asort($combinations);
        $commands = [];
        foreach (explode('.', array_key_last($combinations)) as $instanceId) {
            $commands[] = 'SUMMON ' . $instanceId;
        }
        return $commands;
    }
    private function getCombinations(array $costs, int $totalMana) : array
    {
        $combinations = [];
        foreach ($costs as $instanceId => $cost) {
            $manaAvaliable = $totalMana - $cost;
            $subCosts = $costs;
            unset($subCosts[$instanceId]);
            $subCombinations = $manaAvaliable > 0 ? $this->getCombinations($subCosts, $manaAvaliable) : [];
            foreach ($subCombinations as $combination) {
                $combinations[] = array_merge($combination, [$instanceId]);
            }
            if ($cost <= $totalMana) {
                $combinations[] = [$instanceId];
            }
        }
        return $combinations;
    }
    private function filterCombinations(array $combinations)
    {
        $uniqueKeys = [];
        foreach ($combinations as $combination) {
            sort($combination);
            $uniqueKeys[implode('.', $combination)] = $combination;
        }
        foreach (array_keys($uniqueKeys) as $key) {
            if (strpos($key, '.') !== false) {
                foreach (explode('.', $key) as $part) {
                    unset($uniqueKeys[$part]);
                }
            }
        }
        return $uniqueKeys;
    }
    private function getAttacks()
    {
        $commands = [];
        foreach ($this->cardCollection->listForLocation(Game::LOCATION_BOARD_PLAYER) as $card) {
            if ($card->getType() !== 'creature') {
                continue;
            }
            $commands[] = 'ATTACK ' . $card->getInstanceId() . ' -1';
        }
        return $commands;
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic\Action {
use CodeInGame\LegendsOfCodeMagic\Card\Card;
class CardEvaluator
{
    public function getScore(Card $card) : float
    {
        $score = $card->getCost() === 0 ? 0 : ($card->getAttack() + $card->getDefense()) / $card->getCost();
        if (intval($card->getAttack() - $card->getDefense()) >= $card->getCost()) {
            $score = $score / 3 * 2;
        }
        return $score;
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic\Action {
use CodeInGame\LegendsOfCodeMagic\Card\CardCollection;
class DraftAction
{
    private $cardCollection;
    private $cardEvaluator;
    public function __construct(CardCollection $cardCollection)
    {
        $this->cardCollection = $cardCollection;
        $this->cardEvaluator = new CardEvaluator();
    }
    public function getActions() : array
    {
        $picks = [];
        $pick = 0;
        $value = 0;
        foreach ($this->cardCollection->listAll() as $cardData) {
            $score = $this->cardEvaluator->getScore($cardData['card']);
            if ($score > $value) {
                $pick = $cardData['card']->getInstanceId() + 3;
                $value = $score;
            }
        }
        return ['PICK ' . $pick];
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic\Card {
class Card
{
    private $instanceId;
    private $number;
    private $name;
    private $type;
    private $cost;
    private $attack;
    private $defense;
    private $abilities;
    private $myHealthChange;
    private $opponentHealthChange;
    private $draw;
    public function __construct(int $instanceId, int $number, string $name, string $type, int $cost, int $attack, int $defense, string $abilities, int $myHealthChange, int $opponentHealthChange, int $draw)
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
    public function getInstanceId() : int
    {
        return $this->instanceId;
    }
    public function getNumber() : int
    {
        return $this->number;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getType() : string
    {
        return $this->type;
    }
    public function getCost() : int
    {
        return $this->cost;
    }
    public function getAttack() : int
    {
        return $this->attack;
    }
    public function getDefense() : int
    {
        return $this->defense;
    }
    public function getAbilities() : string
    {
        return $this->abilities;
    }
    public function getMyHealthChange() : int
    {
        return $this->myHealthChange;
    }
    public function getOpponentHealthChange() : int
    {
        return $this->opponentHealthChange;
    }
    public function getDraw() : int
    {
        return $this->draw;
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic\Card {
class CardCollection
{
    protected $collection = [];
    public function add(Card $card, int $location) : void
    {
        $this->collection[$card->getInstanceId()] = ['card' => $card, 'location' => $location];
    }
    public function clear() : void
    {
        $this->collection = [];
    }
    public function get(string $instanceId) : ?Card
    {
        return $this->collection[$instanceId] ?? null;
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
    public function listAll() : array
    {
        return $this->collection;
    }
    public function listForLocation(int $location) : array
    {
        $list = [];
        foreach ($this->collection as $data) {
            if ($data['location'] === $location) {
                $list[] = $data['card'];
            }
        }
        return $list;
    }
    public function remove(int $instanceId) : void
    {
        unset($this->collection[$instanceId]);
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic\Card {
class CardFactory
{
    private $dictionary = [1 => [1, 'Slimer', 'creature', 1, 2, 1, '------', 1, 0, 0, 'Summon: You gain 1 health.'], 2 => [2, 'Scuttler', 'creature', 1, 1, 2, '------', 0, -1, 0, 'Summon: Deal 1 damage to your opponent.'], 3 => [3, 'Beavrat', 'creature', 1, 2, 2, '------', 0, 0, 0, ''], 4 => [4, 'Plated Toad', 'creature', 2, 1, 5, '------', 0, 0, 0, ''], 5 => [5, 'Grime Gnasher', 'creature', 2, 4, 1, '------', 0, 0, 0, ''], 6 => [6, 'Murgling', 'creature', 2, 3, 2, '------', 0, 0, 0, ''], 7 => [7, 'Rootkin Sapling', 'creature', 2, 2, 2, '-----W', 0, 0, 0, ''], 8 => [8, 'Psyshroom', 'creature', 2, 2, 3, '------', 0, 0, 0, ''], 9 => [9, 'Corrupted Beavrat', 'creature', 3, 3, 4, '------', 0, 0, 0, ''], 10 => [10, 'Carnivorous Bush', 'creature', 3, 3, 1, '--D---', 0, 0, 0, ''], 11 => [11, 'Snowsaur', 'creature', 3, 5, 2, '------', 0, 0, 0, ''], 12 => [12, 'Woodshroom', 'creature', 3, 2, 5, '------', 0, 0, 0, ''], 13 => [13, 'Swamp Terror', 'creature', 4, 5, 3, '------', 1, -1, 0, 'Summon: You gain 1 health and deal 1 damage to your opponent.'], 14 => [14, 'Fanged Lunger', 'creature', 4, 9, 1, '------', 0, 0, 0, ''], 15 => [15, 'Pouncing Flailmouth', 'creature', 4, 4, 5, '------', 0, 0, 0, ''], 16 => [16, 'Wrangler Fish', 'creature', 4, 6, 2, '------', 0, 0, 0, ''], 17 => [17, 'Ash Walker', 'creature', 4, 4, 5, '------', 0, 0, 0, ''], 18 => [18, 'Acid Golem', 'creature', 4, 7, 4, '------', 0, 0, 0, ''], 19 => [19, 'Foulbeast', 'creature', 5, 5, 6, '------', 0, 0, 0, ''], 20 => [20, 'Hedge Demon', 'creature', 5, 8, 2, '------', 0, 0, 0, ''], 21 => [21, 'Crested Scuttler', 'creature', 5, 6, 5, '------', 0, 0, 0, ''], 22 => [22, 'Sigbovak', 'creature', 6, 7, 5, '------', 0, 0, 0, ''], 23 => [23, 'Titan Cave Hog', 'creature', 7, 8, 8, '------', 0, 0, 0, ''], 24 => [24, 'Exploding Skitterbug', 'creature', 1, 1, 1, '------', 0, -1, 0, 'Summon: Deal 1 damage to your opponent.'], 25 => [25, 'Spiney Chompleaf', 'creature', 2, 3, 1, '------', -2, -2, 0, 'Summon: Deal 2 damage to each player.'], 26 => [26, 'Razor Crab', 'creature', 2, 3, 2, '------', 0, -1, 0, 'Summon: Deal 1 damage to your opponent.'], 27 => [27, 'Nut Gatherer', 'creature', 2, 2, 2, '------', 2, 0, 0, 'Summon: You gain 2 health.'], 28 => [28, 'Infested Toad', 'creature', 2, 1, 2, '------', 0, 0, 1, 'Summon: Draw a card.'], 29 => [29, 'Steelplume Nestling', 'creature', 2, 2, 1, '------', 0, 0, 1, 'Summon: Draw a card.'], 30 => [30, 'Venomous Bog Hopper', 'creature', 3, 4, 2, '------', 0, -2, 0, 'Summon: Deal 2 damage to your opponent.'], 31 => [31, 'Woodland Hunter', 'creature', 3, 3, 1, '------', 0, -1, 0, 'Summon: Deal 1 damage to your opponent.'], 32 => [32, 'Sandsplat', 'creature', 3, 3, 2, '------', 0, 0, 1, 'Summon: Draw a card.'], 33 => [33, 'Chameleskulk', 'creature', 4, 4, 3, '------', 0, 0, 1, 'Summon: Draw a card.'], 34 => [34, 'Eldritch Cyclops', 'creature', 5, 3, 5, '------', 0, 0, 1, 'Summon: Draw a card.'], 35 => [35, 'Snail-eyed Hulker', 'creature', 6, 5, 2, 'B-----', 0, 0, 1, 'Summon: Draw a card.'], 36 => [36, 'Possessed Skull', 'creature', 6, 4, 4, '------', 0, 0, 2, 'Summon: Draw two cards.'], 37 => [37, 'Eldritch Multiclops', 'creature', 6, 5, 7, '------', 0, 0, 1, 'Summon: Draw a card.'], 38 => [38, 'Imp', 'creature', 1, 1, 3, '--D---', 0, 0, 0, ''], 39 => [39, 'Voracious Imp', 'creature', 1, 2, 1, '--D---', 0, 0, 0, ''], 40 => [40, 'Rock Gobbler', 'creature', 3, 2, 3, '--DG--', 0, 0, 0, ''], 41 => [41, 'Blizzard Demon', 'creature', 3, 2, 2, '-CD---', 0, 0, 0, ''], 42 => [42, 'Flying Leech', 'creature', 4, 4, 2, '--D---', 0, 0, 0, ''], 43 => [43, 'Screeching Nightmare', 'creature', 6, 5, 5, '--D---', 0, 0, 0, ''], 44 => [44, 'Deathstalker', 'creature', 6, 3, 7, '--D-L-', 0, 0, 0, ''], 45 => [45, 'Night Howler', 'creature', 6, 6, 5, 'B-D---', -3, 0, 0, 'Summon: You lose 3 health.'], 46 => [46, 'Soul Devourer', 'creature', 9, 7, 7, '--D---', 0, 0, 0, ''], 47 => [47, 'Gnipper', 'creature', 2, 1, 5, '--D---', 0, 0, 0, ''], 48 => [48, 'Venom Hedgehog', 'creature', 1, 1, 1, '----L-', 0, 0, 0, ''], 49 => [49, 'Shiny Prowler', 'creature', 2, 1, 2, '---GL-', 0, 0, 0, ''], 50 => [50, 'Puff Biter', 'creature', 3, 3, 2, '----L-', 0, 0, 0, ''], 51 => [51, 'Elite Bilespitter', 'creature', 4, 3, 5, '----L-', 0, 0, 0, ''], 52 => [52, 'Bilespitter', 'creature', 4, 2, 4, '----L-', 0, 0, 0, ''], 53 => [53, 'Possessed Abomination', 'creature', 4, 1, 1, '-C--L-', 0, 0, 0, ''], 54 => [54, 'Shadow Biter', 'creature', 3, 2, 2, '----L-', 0, 0, 0, ''], 55 => [55, 'Hermit Slime', 'creature', 2, 0, 5, '---G--', 0, 0, 0, ''], 56 => [56, 'Giant Louse', 'creature', 4, 2, 7, '------', 0, 0, 0, ''], 57 => [57, 'Dream-Eater', 'creature', 4, 1, 8, '------', 0, 0, 0, ''], 58 => [58, 'Darkscale Predator', 'creature', 6, 5, 6, 'B-----', 0, 0, 0, ''], 59 => [59, 'Sea Ghost', 'creature', 7, 7, 7, '------', 1, -1, 0, 'Summon: You gain 1 health and deal 1 damage to your opponent.'], 60 => [60, 'Gritsuck Troll', 'creature', 7, 4, 8, '------', 0, 0, 0, ''], 61 => [61, 'Alpha Troll', 'creature', 9, 10, 10, '------', 0, 0, 0, ''], 62 => [62, 'Mutant Troll', 'creature', 12, 12, 12, 'B--G--', 0, 0, 0, ''], 63 => [63, 'Rootkin Drone', 'creature', 2, 0, 4, '---G-W', 0, 0, 0, ''], 64 => [64, 'Coppershell Tortoise', 'creature', 2, 1, 1, '---G-W', 0, 0, 0, ''], 65 => [65, 'Steelplume Defender', 'creature', 2, 2, 2, '-----W', 0, 0, 0, ''], 66 => [66, 'Staring Wickerbeast', 'creature', 5, 5, 1, '-----W', 0, 0, 0, ''], 67 => [67, 'Flailing Hammerhead', 'creature', 6, 5, 5, '-----W', 0, -2, 0, 'Summon: Deal 2 damage to your opponent.'], 68 => [68, 'Giant Squid', 'creature', 6, 7, 5, '-----W', 0, 0, 0, ''], 69 => [69, 'Charging Boarhound', 'creature', 3, 4, 4, 'B-----', 0, 0, 0, ''], 70 => [70, 'Murglord', 'creature', 4, 6, 3, 'B-----', 0, 0, 0, ''], 71 => [71, 'Flying Murgling', 'creature', 4, 3, 2, 'BC----', 0, 0, 0, ''], 72 => [72, 'Shuffling Nightmare', 'creature', 4, 5, 3, 'B-----', 0, 0, 0, ''], 73 => [73, 'Bog Bounder', 'creature', 4, 4, 4, 'B-----', 4, 0, 0, 'Summon: You gain 4 health.'], 74 => [74, 'Crusher', 'creature', 5, 5, 4, 'B--G--', 0, 0, 0, ''], 75 => [75, 'Titan Prowler', 'creature', 5, 6, 5, 'B-----', 0, 0, 0, ''], 76 => [76, 'Crested Chomper', 'creature', 6, 5, 5, 'B-D---', 0, 0, 0, ''], 77 => [77, 'Lumbering Giant', 'creature', 7, 7, 7, 'B-----', 0, 0, 0, ''], 78 => [78, 'Shambler', 'creature', 8, 5, 5, 'B-----', 0, -5, 0, 'Summon: Deal 5 damage to your opponent.'], 79 => [79, 'Scarlet Colossus', 'creature', 8, 8, 8, 'B-----', 0, 0, 0, ''], 80 => [80, 'Corpse Guzzler', 'creature', 8, 8, 8, 'B--G--', 0, 0, 1, 'Summon: Draw a card.'], 81 => [81, 'Flying Corpse Guzzler', 'creature', 9, 6, 6, 'BC----', 0, 0, 0, ''], 82 => [82, 'Slithering Nightmare', 'creature', 7, 5, 5, 'B-D--W', 0, 0, 0, ''], 83 => [83, 'Restless Owl', 'creature', 0, 1, 1, '-C----', 0, 0, 0, ''], 84 => [84, 'Fighter Tick', 'creature', 2, 1, 1, '-CD--W', 0, 0, 0, ''], 85 => [85, 'Heartless Crow', 'creature', 3, 2, 3, '-C----', 0, 0, 0, ''], 86 => [86, 'Crazed Nose-pincher', 'creature', 3, 1, 5, '-C----', 0, 0, 0, ''], 87 => [87, 'Bloat Demon', 'creature', 4, 2, 5, '-C-G--', 0, 0, 0, ''], 88 => [88, 'Abyss Nightmare', 'creature', 5, 4, 4, '-C----', 0, 0, 0, ''], 89 => [89, 'Boombeak', 'creature', 5, 4, 1, '-C----', 2, 0, 0, 'Summon: You gain 2 health.'], 90 => [90, 'Eldritch Swooper', 'creature', 8, 5, 5, '-C----', 0, 0, 0, ''], 91 => [91, 'Flumpy', 'creature', 0, 1, 2, '---G--', 0, 1, 0, 'Summon: Your opponent gains 1 health.'], 92 => [92, 'Wurm', 'creature', 1, 0, 1, '---G--', 2, 0, 0, 'Summon: You gain 2 health.'], 93 => [93, 'Spinekid', 'creature', 1, 2, 1, '---G--', 0, 0, 0, ''], 94 => [94, 'Rootkin Defender', 'creature', 2, 1, 4, '---G--', 0, 0, 0, ''], 95 => [95, 'Wildum', 'creature', 2, 2, 3, '---G--', 0, 0, 0, ''], 96 => [96, 'Prairie Protector', 'creature', 2, 3, 2, '---G--', 0, 0, 0, ''], 97 => [97, 'Turta', 'creature', 3, 3, 3, '---G--', 0, 0, 0, ''], 98 => [98, 'Lilly Hopper', 'creature', 3, 2, 4, '---G--', 0, 0, 0, ''], 99 => [99, 'Cave Crab', 'creature', 3, 2, 5, '---G--', 0, 0, 0, ''], 100 => [100, 'Stalagopod', 'creature', 3, 1, 6, '---G--', 0, 0, 0, ''], 101 => [101, 'Engulfer', 'creature', 4, 3, 4, '---G--', 0, 0, 0, ''], 102 => [102, 'Mole Demon', 'creature', 4, 3, 3, '---G--', 0, -1, 0, 'Summon: Deal 1 damage to your opponent.'], 103 => [103, 'Mutating Rootkin', 'creature', 4, 3, 6, '---G--', 0, 0, 0, ''], 104 => [104, 'Deepwater Shellcrab', 'creature', 4, 4, 4, '---G--', 0, 0, 0, ''], 105 => [105, 'King Shellcrab', 'creature', 5, 4, 6, '---G--', 0, 0, 0, ''], 106 => [106, 'Far-reaching Nightmare', 'creature', 5, 5, 5, '---G--', 0, 0, 0, ''], 107 => [107, 'Worker Shellcrab', 'creature', 5, 3, 3, '---G--', 3, 0, 0, 'Summon: You gain 3 health.'], 108 => [108, 'Rootkin Elder', 'creature', 5, 2, 6, '---G--', 0, 0, 0, ''], 109 => [109, 'Elder Engulfer', 'creature', 5, 5, 6, '------', 0, 0, 0, ''], 110 => [110, 'Gargoyle', 'creature', 5, 0, 9, '---G--', 0, 0, 0, ''], 111 => [111, 'Turta Knight', 'creature', 6, 6, 6, '---G--', 0, 0, 0, ''], 112 => [112, 'Rootkin Leader', 'creature', 6, 4, 7, '---G--', 0, 0, 0, ''], 113 => [113, 'Tamed Bilespitter', 'creature', 6, 2, 4, '---G--', 4, 0, 0, 'Summon: You gain 4 health.'], 114 => [114, 'Gargantua', 'creature', 7, 7, 7, '---G--', 0, 0, 0, ''], 115 => [115, 'Rootkin Warchief', 'creature', 8, 5, 5, '---G-W', 0, 0, 0, ''], 116 => [116, 'Emperor Nightmare', 'creature', 12, 8, 8, 'BCDGLW', 0, 0, 0, ''], 117 => [117, 'Protein', 'itemGreen', 1, 1, 1, 'B-----', 0, 0, 0, 'Give a friendly creature +1/+1 and Breakthrough.'], 118 => [118, 'Royal Helm', 'itemGreen', 0, 0, 3, '------', 0, 0, 0, 'Give a friendly creature +0/+3.'], 119 => [119, 'Serrated Shield', 'itemGreen', 1, 1, 2, '------', 0, 0, 0, 'Give a friendly creature +1/+2.'], 120 => [120, 'Venomfruit', 'itemGreen', 2, 1, 0, '----L-', 0, 0, 0, 'Give a friendly creature +1/+0 and Lethal.'], 121 => [121, 'Enchanted Hat', 'itemGreen', 2, 0, 3, '------', 0, 0, 1, 'Give a friendly creature +0/+3. Draw a card.'], 122 => [122, 'Bolstering Bread', 'itemGreen', 2, 1, 3, '---G--', 0, 0, 0, 'Give a friendly creature +1/+3 and Guard.'], 123 => [123, 'Wristguards', 'itemGreen', 2, 4, 0, '------', 0, 0, 0, 'Give a friendly creature +4/+0.'], 124 => [124, 'Blood Grapes', 'itemGreen', 3, 2, 1, '--D---', 0, 0, 0, 'Give a friendly creature +2/+1 and Drain.'], 125 => [125, 'Healthy Veggies', 'itemGreen', 3, 1, 4, '------', 0, 0, 0, 'Give a friendly creature +1/+4.'], 126 => [126, 'Heavy Shield', 'itemGreen', 3, 2, 3, '------', 0, 0, 0, 'Give a friendly creature +2/+3.'], 127 => [127, 'Imperial Helm', 'itemGreen', 3, 0, 6, '------', 0, 0, 0, 'Give a friendly creature +0/+6.'], 128 => [128, 'Enchanted Cloth', 'itemGreen', 4, 4, 3, '------', 0, 0, 0, 'Give a friendly creature +4/+3.'], 129 => [129, 'Enchanted Leather', 'itemGreen', 4, 2, 5, '------', 0, 0, 0, 'Give a friendly creature +2/+5.'], 130 => [130, 'Helm of Remedy', 'itemGreen', 4, 0, 6, '------', 4, 0, 0, 'Give a friendly creature +0/+6. You gain 4 health.'], 131 => [131, 'Heavy Gauntlet', 'itemGreen', 4, 4, 1, '------', 0, 0, 0, 'Give a friendly creature +4/+1.'], 132 => [132, 'High Protein', 'itemGreen', 5, 3, 3, 'B-----', 0, 0, 0, 'Give a friendly creature +3/+3 and Breakthrough.'], 133 => [133, 'Pie of Power', 'itemGreen', 5, 4, 0, '-----W', 0, 0, 0, 'Give a friendly creature +4/+0 and Ward.'], 134 => [134, 'Light The Way', 'itemGreen', 4, 2, 2, '------', 0, 0, 1, 'Give a friendly creature +2/+2. Draw a card.'], 135 => [135, 'Imperial Armour', 'itemGreen', 6, 5, 5, '------', 0, 0, 0, 'Give a friendly creature +5/+5.'], 136 => [136, 'Buckler', 'itemGreen', 0, 1, 1, '------', 0, 0, 0, 'Give a friendly creature +1/+1.'], 137 => [137, 'Ward', 'itemGreen', 2, 0, 0, '-----W', 0, 0, 0, 'Give a friendly creature Ward.'], 138 => [138, 'Grow Horns', 'itemGreen', 2, 0, 0, '---G--', 0, 0, 1, 'Give a friendly creature Guard. Draw a card.'], 139 => [139, 'Grow Stingers', 'itemGreen', 4, 0, 0, '----LW', 0, 0, 0, 'Give a friendly creature Lethal and Ward.'], 140 => [140, 'Grow Wings', 'itemGreen', 2, 0, 0, '-C----', 0, 0, 0, 'Give a friendly creature Charge.'], 141 => [141, 'Throwing Knife', 'itemRed', 0, -1, -1, '------', 0, 0, 0, 'Give an enemy creature -1/-1.'], 142 => [142, 'Staff of Suppression', 'itemRed', 0, 0, 0, 'BCDGLW', 0, 0, 0, 'Remove all abilities from an enemy creature.'], 143 => [143, 'Pierce Armour', 'itemRed', 0, 0, 0, '---G--', 0, 0, 0, 'Remove Guard from an enemy creature.'], 144 => [144, 'Rune Axe', 'itemRed', 1, 0, -2, '------', 0, 0, 0, 'Deal 2 damage to an enemy creature.'], 145 => [145, 'Cursed Sword', 'itemRed', 3, -2, -2, '------', 0, 0, 0, 'Give an enemy creature -2/-2.'], 146 => [146, 'Cursed Scimitar', 'itemRed', 4, -2, -2, '------', 0, -2, 0, 'Give an enemy creature -2/-2. Deal 2 damage to your opponent.'], 147 => [147, 'Quick Shot', 'itemRed', 2, 0, -1, '------', 0, 0, 1, 'Deal 1 damage to an enemy creature. Draw a card.'], 148 => [148, 'Helm Crusher', 'itemRed', 2, 0, -2, 'BCDGLW', 0, 0, 0, 'Remove all abilities from an enemy creature, then deal 2 damage to it.'], 149 => [149, 'Rootkin Ritual', 'itemRed', 3, 0, 0, 'BCDGLW', 0, 0, 1, 'Remove all abilities from an enemy creature. Draw a card.'], 150 => [150, 'Throwing Axe', 'itemRed', 2, 0, -3, '------', 0, 0, 0, 'Deal 3 damage to an enemy creature.'], 151 => [151, 'Decimate', 'itemRed', 5, 0, -99, 'BCDGLW', 0, 0, 0, 'Remove all abilities from an enemy creature, then deal 99 damage to it.'], 152 => [152, 'Mighty Throwing Axe', 'itemRed', 7, 0, -7, '------', 0, 0, 1, 'Deal 7 damage to an enemy creature. Draw a card.'], 153 => [153, 'Healing Potion', 'itemBlue', 2, 0, 0, '------', 5, 0, 0, 'Gain 5 health.'], 154 => [154, 'Poison', 'itemBlue', 2, 0, 0, '------', 0, -2, 1, 'Deal 2 damage to your opponent. Draw a card.'], 155 => [155, 'Scroll of Firebolt', 'itemBlue', 3, 0, -3, '------', 0, -1, 0, 'Deal 3 damage. Deal 1 damage to your opponent'], 156 => [156, 'Major Life Steal Potion', 'itemBlue', 3, 0, 0, '------', 3, -3, 0, 'Deal 3 damage to your opponent and gain 3 health.'], 157 => [157, 'Life Sap Drop', 'itemBlue', 3, 0, -1, '------', 1, 0, 1, 'Deal 1 damage, gain 1 health, and draw a card.'], 158 => [158, 'Tome of Thunder', 'itemBlue', 3, 0, -4, '------', 0, 0, 0, 'Deal 4 damage.'], 159 => [159, 'Vial of Soul Drain', 'itemBlue', 4, 0, -3, '------', 3, 0, 0, 'Deal 3 damage and gain 3 health.'], 160 => [160, 'Minor Life Steal Potion', 'itemBlue', 2, 0, 0, '------', 2, -2, 0, 'Deal 2 damage to your opponent and gain 2 health.']];
    public function create(int $number, int $instanceId) : Card
    {
        $template = $this->dictionary[$number];
        return new Card($instanceId, ...$template);
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic\Card {
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
class Debug
{
    /**
     * Output data to the console
     *
     * @param mixed $entity
     */
    public function __construct($entity)
    {
        error_log(var_export($entity, true));
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic {
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
    public function updateState(array $cardData) : void
    {
        foreach ($cardData as $data) {
            [$card, $location] = $data;
            $this->cardCollection->add($card, $location);
            $this->board->add($card->getInstanceId());
        }
    }
    public function applyOpponentsActions() : void
    {
        $actions = $this->opponent->getActions();
        foreach ($actions as $action) {
            $card = $this->cardCollection->find($action['cardNumber'], self::LOCATION_BOARD_OPPONENT);
            // new Debug("{$card->getNumber()}, {$action['action']}");
        }
        $this->opponent->clearActions();
    }
    public function getPlayerActions() : string
    {
        $playerActions = in_array(-1, $this->board->list()) ? (new DraftAction($this->cardCollection))->getActions() : (new BattleAction($this->cardCollection, $this->player, $this->opponent))->getActions();
        return implode(';', $playerActions) . "\n";
    }
    public function cleanup() : void
    {
        $this->cardCollection->clear();
        $this->board->clear();
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic\Player {
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

namespace CodeInGame\LegendsOfCodeMagic\Player {
use CodeInGame\LegendsOfCodeMagic\Card\CardReferenceCollection;
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
    public function getDeckDefinition() : CardReferenceCollection
    {
        return $this->deck;
    }
}
}

namespace CodeInGame\LegendsOfCodeMagic {
$game = new Game(new Player\Player(), new Player\Opponent());
$stateReader = new StateReader($game);
// game loop
while (true) {
    $stateReader->updateState();
    $game->applyOpponentsActions();
    echo $game->getPlayerActions();
    $game->cleanup();
}
}

namespace CodeInGame\LegendsOfCodeMagic {
use CodeInGame\LegendsOfCodeMagic\Card\CardFactory;
class StateReader
{
    private $cardFactory;
    private $game;
    public function __construct(Game $game)
    {
        $this->game = $game;
        $this->cardFactory = new CardFactory();
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
        $cardData = [];
        for ($i = 0; $i < $cardCount; $i++) {
            fscanf(STDIN, "%d %d %d %d %d %d %d %s %d %d %d", $number, $instanceId, $location, $type, $cost, $att, $def, $abi, $myhealth, $opphealth, $draw);
            if ($instanceId == '-1') {
                $instanceId = $i - 3;
            }
            $cardData[] = [$this->cardFactory->create($number, $instanceId), $location];
        }
        $this->game->updateState($cardData);
    }
}
}

