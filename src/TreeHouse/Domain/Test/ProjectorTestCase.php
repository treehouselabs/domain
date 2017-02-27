<?php

namespace TreeHouse\Domain\Test;

use PHPUnit\Framework\TestCase;
use TreeHouse\Domain\EventName;
use TreeHouse\EventSourcing\InMemoryProjectionRepository;
use TreeHouse\EventSourcing\ProjectionRepositoryInterface;
use TreeHouse\EventSourcing\ProjectorInterface;
use TreeHouse\EventSourcing\VersionedEvent;

/**
 * @codeCoverageIgnore
 */
abstract class ProjectorTestCase extends TestCase
{
    /**
     * @var ProjectorInterface
     */
    private $projector;

    /**
     * @var ProjectionRepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    private $aggregateId;

    /**
     * @var int
     */
    private $version = 1;

    /**
     * @return ProjectionRepositoryInterface
     */
    protected function getRepository()
    {
        if (null === $this->repository) {
            $this->repository = new InMemoryProjectionRepository();
        }

        return $this->repository;
    }

    /**
     * @param string $aggregateId
     * @param ProjectorInterface $projector
     * @param array $events
     * @param \DateTime|null $occurredOn
     *
     * @return $this
     */
    protected function given($aggregateId, ProjectorInterface $projector, array $events = [], \DateTime $occurredOn = null)
    {
        $this->aggregateId = $aggregateId;
        $this->projector = $projector;

        foreach ($events as $event) {
            $this->projector->handle(
                new VersionedEvent(
                    $this->aggregateId,
                    $event,
                    (string) new EventName($event),
                    $this->version,
                    $occurredOn
                )
            );
            ++$this->version;
        }

        return $this;
    }

    /**
     * @param object         $event
     * @param \DateTime|null $occurredOn
     * @param null|string    $aggregateId
     *
     * @return $this
     */
    protected function when($event, \DateTime $occurredOn = null, $aggregateId = null)
    {
        $this->projector->handle(
            new VersionedEvent(
                $aggregateId ?: $this->aggregateId,
                $event,
                (string) new EventName($event),
                $this->version,
                $occurredOn
            )
        );
        ++$this->version;

        return $this;
    }

    /**
     * @param array $expectedData
     *
     * @return $this
     */
    protected function then(array $expectedData = [])
    {
        $this->assertEquals(
            $expectedData,
            $this->getRepository()->findAll()
        );

        return $this;
    }
}
