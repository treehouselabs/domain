<?php

namespace TreeHouse\Domain;

class EventName
{
    /**
     * @var object
     */
    private $event;

    /**
     * @var string
     */
    private $name;

    /**
     * @param object|string $event
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (null === $this->name) {
            $this->name = $this->parseName($this->event);
        }

        return $this->name;
    }

    /**
     * @param object $event
     *
     * @return string
     */
    private function parseName($event)
    {
        if ($event instanceof EventEnvelopeInterface) {
            return $event->getEventName();
        }

        if (is_object($event)) {
            $class = get_class($event);
        } elseif (is_string($event)) {
            $class = $event;
        } else {
            throw new \InvalidArgumentException(sprintf('Cannot parse name for type %s', gettype($event)));
        }

        if (substr($class, -5) === 'Event') {
            $class = substr($class, 0, -5);
        }

        $parts = explode('\\', $class);

        return end($parts);
    }
}
