<?php

namespace TreeHouse\Domain;

interface AggregateStoreInterface
{
    /**
     * @param $id
     *
     * @return mixed|null
     */
    public function load($id);

    /**
     * @param AggregateInterface $aggregate
     */
    public function save(AggregateInterface $aggregate);
}
