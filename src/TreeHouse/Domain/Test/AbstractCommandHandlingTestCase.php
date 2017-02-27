<?php

namespace TreeHouse\Domain\Test;

use PHPUnit\Framework\TestCase;
use TreeHouse\CommandHandling\CommandHandlerInterface;
use TreeHouse\CommandHandling\CommandInterface;
use TreeHouse\Domain\EventName;
use TreeHouse\Domain\EventStream;
use TreeHouse\EventSourcing\Bridge\EventStore\TreeHouse\EventStore;
use TreeHouse\EventSourcing\Bridge\EventStore\TreeHouse\TraceableEventStore;
use TreeHouse\EventSourcing\Bridge\EventStore\TreeHouse\VersionedEventFactory;
use TreeHouse\EventSourcing\EventBusInterface;
use TreeHouse\EventSourcing\EventSourcingRepository;
use TreeHouse\EventSourcing\VersionedEvent;
use TreeHouse\EventStore\InMemoryEventStore;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractCommandHandlingTestCase extends TestCase
{
    /**
     * @var EventBusInterface
     */
    protected $eventBus;

    /**
     * @var TraceableEventStore
     */
    protected $eventStore;

    /**
     * @var EventSourcingRepository[]
     */
    protected $eventRepositories = [];

    /**
     * @var string
     */
    private $aggregateId;

    /**
     * @var int
     */
    protected $version = 1;

    /**
     * @var string
     */
    private $aggregateClass;

    public function setUp()
    {
        $this->eventBus = $this->prophesize(EventBusInterface::class)->reveal();

        $this->eventStore = new TraceableEventStore(
            new EventStore(
                new InMemoryEventStore(),
                new VersionedEventFactory()
            )
        );
    }

    /**
     * @param null $aggregateClass
     *
     * @return EventSourcingRepository
     */
    public function getEventRepository($aggregateClass)
    {
        if (!isset($this->eventRepositories[$aggregateClass])) {
            $this->eventRepositories[$aggregateClass] = new EventSourcingRepository(
                $this->eventStore,
                $this->eventBus,
                $aggregateClass
            );
        }

        return $this->eventRepositories[$aggregateClass];
    }

    /**
     * @param $aggregateId
     * @param $aggregateClass
     * @param array|null $events
     * @param \DateTime $occurredOn
     *
     * @return $this
     */
    protected function given($aggregateId, $aggregateClass, array $events = [], \DateTime $occurredOn = null)
    {
        $this->aggregateId = $aggregateId;
        $this->aggregateClass = $aggregateClass;

        $this->eventStore->append(
            $this->getEventsForStream($aggregateId, $events, $occurredOn)
        );

        return $this;
    }

    /**
     * @param CommandInterface        $command
     * @param CommandHandlerInterface $handler
     *
     * @return $this
     */
    protected function when(CommandInterface $command, CommandHandlerInterface $handler)
    {
        $this->eventStore->trace();

        $handler->handle($command);

        return $this;
    }

    /**
     * @param array $expectedEvents
     *
     * @return $this
     */
    protected function then(array $expectedEvents = [])
    {
        $this->assertEquals(
            $expectedEvents,
            $this->eventStore->getTracedEvents()
        );

        return $this;
    }

    /**
     * @param null|string $aggregateId
     *
     * @return mixed
     */
    protected function getAggregate($aggregateId = null, $aggregateClass = null)
    {
        $aggregateClass = $aggregateClass ?: $this->aggregateClass;

        return $aggregateClass::createFromStream(
            $this->eventStore->getStream($aggregateId ?: $this->aggregateId)
        );
    }

    /**
     * @param string $aggregateId
     * @param array  $events
     * @param \DateTime $occurredOn
     *
     * @return EventStream
     */
    private function getEventsForStream($aggregateId, array $events = [], \DateTime $occurredOn = null)
    {
        $eventsForStream = new EventStream();

        foreach ($events as $event) {
            $eventsForStream->append(new VersionedEvent(
                $aggregateId,
                $event,
                (string) new EventName($event),
                $this->version,
                $occurredOn
            ));

            ++$this->version;
        }

        return $eventsForStream;
    }
}
