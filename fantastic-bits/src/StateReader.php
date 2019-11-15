<?php

namespace CodeInGame\FantasticBits;

use CodeInGame\FantasticBits\Location\Position;
use CodeInGame\FantasticBits\Map\Team;
use CodeInGame\FantasticBits\Map\Entity\AbstractEntity;
use CodeInGame\FantasticBits\Map\Entity\Bludger;
use CodeInGame\FantasticBits\Map\Entity\EntityCollection;
use CodeInGame\FantasticBits\Map\Entity\Snaffle;
use CodeInGame\FantasticBits\Map\Entity\Wizard;
use InvalidArgumentException;

class StateReader
{
    private const BLUDGER = 'BLUDGER';
    private const SNAFFLE = 'SNAFFLE';
    private const FRIENDLY_WIZARD = 'WIZARD';
    private const OPPONENT_WIZARD = 'OPPONENT_WIZARD';

    private const WIZARD = [self::FRIENDLY_WIZARD, self::OPPONENT_WIZARD];

    public function getPlayDirection(): int
    {
        fscanf(STDIN, '%d', $playDirection);

        return $playDirection;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getGameState(): array
    {
        [$myScore, $myMagic] = $this->getTeamStats();
        [$oppScore, $oppMagic] = $this->getTeamStats();
        [$snaffles, $bludgers, $myPlayers, $oppPlayers] = $this->getEntityList();

        $myTeam = new Team(0, $myMagic, $myScore);
        $myTeam->setWizards($myPlayers);

        $oppTeam = new Team(1, $oppScore, $oppMagic);
        $oppTeam->setWizards($oppPlayers);

        return [$myTeam, $oppTeam, $snaffles, $bludgers];
    }

    private function getTeamStats(): array
    {
        fscanf(STDIN, '%d %d', $score, $magic);

        return [$score, $magic];
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getEntityList(): array
    {
        fscanf(STDIN, '%d', $entities);

        $bludgers = new EntityCollection();
        $myPlayers = new EntityCollection();
        $oppPlayers = new EntityCollection();
        $snaffles = new EntityCollection();

        $entityList = [];
        for ($i = 0; $i < $entities; $i++) {
            $entity = $this->loadEntity();

            if ($entity instanceof Snaffle) {
                $snaffles->add($entity);
                continue;
            }

            if ($entity instanceof Bludger) {
                $bludgers->add($entity);
                continue;
            }

            if ($entity instanceof Wizard && $entity->getTeam() == 0) {
                $myPlayers->add($entity);
                continue;
            }

            if ($entity instanceof Wizard && $entity->getTeam() == 1) {
                $oppPlayers->add($entity);
                continue;
            }

            throw new InvalidArgumentException('Invalid Entity Type');
        }

        return [$snaffles, $bludgers, $myPlayers, $oppPlayers];
    }

    /**
     * @throws InvalidArgumentException
     */
    private function loadEntity(): AbstractEntity
    {
        fscanf(STDIN, '%d %s %d %d %d %d %d', $entityId, $entityType, $x, $y, $vx, $vy, $state);

        if ($entityType == self::BLUDGER) {
            return new Bludger($entityId, new Position($x, $y), new Position($vx, $vy), $state);
        }

        if ($entityType == self::SNAFFLE) {
            return new Snaffle($entityId, new Position($x, $y), new Position($vx, $vy), $state);
        }

        if (in_array($entityType, self::WIZARD)) {
            $wizard = new Wizard($entityId, new Position($x, $y), new Position($vx, $vy), $state);
            $wizard->setTeam($entityType == self::OPPONENT_WIZARD);

            return $wizard;
        }

        throw new InvalidArgumentException('Unknown Entity Type: ' . $entityType);
    }
}
