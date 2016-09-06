<?php

namespace TreeHouse\Domain;

abstract class AbstractAggregate implements AggregateInterface
{
    use RecordsEventsTrait;

    /**
     * Update in-memory state.
     *
     * @param object $event
     */
    private function mutate($event)
    {
        $method = 'on' . (string) new EventName($event);

        if (method_exists($this, $method)) {
            $this->$method($event);
        } else {
            throw new \RuntimeException(sprintf('Method %s does not exist on aggregate %s', $method, get_class($this)));
        }
    }

    /**
     * @param object $event
     */
    protected function apply($event)
    {
        $this->recordEvent($event);

        $this->mutate($event);
    }
}
