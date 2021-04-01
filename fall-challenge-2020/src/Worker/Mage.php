<?php

namespace CodeInGame\FallChallenge2020\Worker;

use CodeInGame\FallChallenge2020\Entity\Cupboard;
use CodeInGame\FallChallenge2020\Entity\Book;
use CodeInGame\FallChallenge2020\Entity\Recipe;
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
     * Find the lowest level spell that makes something used by this recipe
     */
    public function getBestSpell(Cupboard $cupboard, Book $spellbook, Recipe $recipe): ?Spell
    {
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
