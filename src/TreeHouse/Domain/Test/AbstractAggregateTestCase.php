<?php

namespace TreeHouse\Domain\Test;

use PHPUnit_Framework_TestCase;
use TreeHouse\CommandHandling\CommandHandlerInterface;
use TreeHouse\CommandHandling\CommandInterface;
use TreeHouse\Domain\AggregateInterface;
use TreeHouse\Domain\AggregateRepository;
use TreeHouse\Domain\AggregateRepositoryInterface;
use TreeHouse\Domain\AggregateStoreInterface;
use TreeHouse\Domain\EventEnvelopeInterface;
use TreeHouse\MessageBus\MessageBus;
use TreeHouse\MessageBus\MessageBusInterface;
use TreeHouse\MessageBus\Middleware\TraceableMiddleware;

abstract class AbstractAggregateTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var MessageBusInterface
     */
    protected $eventBus;

    /**
     * @var TraceableMiddleware
     */
    protected $eventBusTrace;

    /**
     * @var AggregateRepositoryInterface[]
     */
    protected $aggregateRepositories = [];

    /**
     * @var AggregateStoreInterface[]
     */
    protected $aggregateStores = [];

    protected function setUp()
    {
        $messageBus = new MessageBus();
        $messageBus->registerMiddleware($this->eventBusTrace = new TraceableMiddleware());

        $this->eventBus = $messageBus;
    }

    /**
     * @param CommandInterface        $command
     * @param CommandHandlerInterface $handler
     *
     * @return $this
     */
    protected function when(CommandInterface $command, CommandHandlerInterface $handler)
    {
        $handler->handle($command);

        return $this;
    }

    /**
     * @param object[] $expectedEvents
     *
     * @return $this
     */
    protected function then(array $expectedEvents)
    {
        $traced = array_map(function ($m) {
            if ($m instanceof EventEnvelopeInterface) {
                return $m->getEvent();
            }

            return $m;
        }, $this->eventBusTrace->getTracedMessages());

        $this->assertEquals(
            $expectedEvents,
            $traced
        );

        return $this;
    }

    /**
     * @param string $aggregateClass
     *
     * @return AggregateRepositoryInterface
     */
    protected function getAggregateRepository($aggregateClass)
    {
        if (!isset($this->aggregateRepositories[$aggregateClass])) {
            $this->aggregateRepositories[$aggregateClass] = new AggregateRepository(
                $this->aggregateStores[$aggregateClass],
                $this->eventBus,
                $aggregateClass
            );
        }

        return $this->aggregateRepositories[$aggregateClass];
    }

    /**
     * @param AggregateStoreInterface $aggregateStore
     * @param string                  $aggregateClass
     */
    protected function registerAggregateStore(AggregateStoreInterface $aggregateStore, $aggregateClass)
    {
        $this->aggregateStores[$aggregateClass] = $aggregateStore;
    }

    /**
     * @param string $aggregateId
     * @param string $aggregateClass
     *
     * @return AggregateInterface
     */
    protected function getAggregate($aggregateId, $aggregateClass)
    {
        return $aggregateClass::createFromData(
            $this->aggregateStores[$aggregateClass]->load($aggregateId)
        );
    }
}
