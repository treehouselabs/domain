<?php

namespace TreeHouse\Domain;

interface EventStreamInterface extends \Traversable, \Countable
{
    /**
     * @param object $event
     */
    public function append($event);

    /**
     * @param EventStream $stream
     */
    public function track(EventStream $stream);

    /**
     * @param string $eventName
     *
     * @return bool
     */
    public function contains($eventName);

    /**
     * Clear events in stream.
     */
    public function clear();
}
