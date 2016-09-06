<?php

namespace TreeHouse\Domain;

interface RecordsEventsInterface
{
    /**
     * @param object $event
     */
    public function recordEvent($event);

    /**
     * @return EventStream
     */
    public function getRecordedEvents();

    /**
     * @return $this
     */
    public function clearRecordedEvents();
}
