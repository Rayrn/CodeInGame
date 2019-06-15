<?php

namespace CodeInGame\LegendsOfCodeMagic;

class Board extends CardReferenceCollection
{
    public function doAction(Card $card, string $action): void
    {
        foreach ($this->collection as $instanceId)
        {
            if ($instanceId == $card->getInstanceId()) {
                debug("$action, {$card->getNumber()}");
            }
        }
    }
}
