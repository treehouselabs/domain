<?php

namespace TreeHouse\Domain;

interface AggregateInterface extends RecordsEventsInterface
{
    /**
     * @param mixed $data
     *
     * @return AggregateInterface
     */
    public static function createFromData($data);

    /**
     * @return string
     */
    public function getId();
}
