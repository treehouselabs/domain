<?php

namespace TreeHouse\Domain;

use TreeHouse\MessageBus\MessageBusInterface;

class AggregateRepository implements AggregateRepositoryInterface
{
    /**
     * @var AggregateStoreInterface
     */
    protected $store;

    /**
     * @var MessageBusInterface
     */
    protected $eventBus;

    /**
     * @var string
     */
    protected $aggregateClassName;

    /**
     * @param AggregateStoreInterface $store
     * @param MessageBusInterface     $eventBus
     * @param string                  $aggregateClassName
     */
    public function __construct(AggregateStoreInterface $store, MessageBusInterface $eventBus, $aggregateClassName)
    {
        $this->store = $store;
        $this->eventBus = $eventBus;
        $this->aggregateClassName = $aggregateClassName;
    }

    /**
     * @inheritdoc
     */
    public function load($id)
    {
        $data = $this->store->load($id);

        if (!$data) {
            return null;
        }

        $aggregateClass = $this->aggregateClassName;

        $aggregate = $aggregateClass::createFromData($data);

        return $aggregate;
    }

    /**
     * @inheritdoc
     */
    public function save(AggregateInterface $aggregate)
    {
        $events = $aggregate->getRecordedEvents();

        $this->store->save($aggregate);

        foreach ($events as $event) {
            $this->eventBus->handle($event);
        }

        $aggregate->clearRecordedEvents();
    }
}
