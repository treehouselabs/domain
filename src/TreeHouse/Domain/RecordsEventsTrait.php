<?php

namespace TreeHouse\Domain;

trait RecordsEventsTrait
{
    /*
     * @var EventStream|null
     */
    protected $stream;

    /**
     * @param object $event
     */
    public function recordEvent($event)
    {
        $this->getStream()->append($event);
    }

    /**
     * @return EventStream
     */
    public function getRecordedEvents()
    {
        return $this->getStream();
    }

    /**
     * @return $this
     */
    public function clearRecordedEvents()
    {
        if ($this->stream) {
            $this->stream->clear();
        }

        return $this;
    }

    /**
     * @return EventStream
     */
    private function getStream()
    {
        if (null === $this->stream) {
            $this->stream = new EventStream();
        }

        return $this->stream;
    }
}
