<?php

namespace TreeHouse\Domain;

use ArrayIterator;
use IteratorAggregate;

class EventStream implements IteratorAggregate, EventStreamInterface
{
    /**
     * @var object[]
     */
    protected $events = [];

    /**
     * @var EventStream[]
     */
    protected $tracking = [];

    /**
     * @var bool
     */
    protected $readOnly = false;

    /**
     * @param object[] $events
     */
    public function __construct(array $events = [])
    {
        $this->events = $events;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->events);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->events);
    }

    /**
     * @param object $event
     */
    public function append($event)
    {
        if (!$this->readOnly) {
            $this->events[] = $event;
        }

        foreach ($this->tracking as $stream) {
            $stream->append($event);
        }
    }

    /**
     * @param EventStream $stream
     */
    public function appendStream(EventStream $stream)
    {
        foreach ($stream as $event) {
            $this->append($event);
        }
    }

    /**
     * When tracking, appended events are also appended to the tracking streams.
     *
     * @param EventStream $stream
     */
    public function track(EventStream $stream)
    {
        if (!in_array($stream, $this->tracking, true)) {
            $this->tracking[] = $stream;
        }
    }

    /**
     * @param string $eventName
     *
     * @return bool
     */
    public function contains($eventName)
    {
        foreach ($this->events as $event) {
            if ((string) new EventName($event) === $eventName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $eventName
     *
     * @return object
     */
    public function findOne($eventName)
    {
        foreach ($this->events as $event) {
            if ((string) new EventName($event) === $eventName) {
                return $event;
            }
        }

        return null;
    }

    public function clear()
    {
        $this->events = [];
    }

    /**
     * @param bool $readOnly
     */
    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;
    }
}
