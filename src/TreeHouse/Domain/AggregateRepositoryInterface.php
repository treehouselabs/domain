<?php

namespace TreeHouse\Domain;

interface AggregateRepositoryInterface
{
    /**
     * @param mixed $id
     *
     * @return AggregateInterface|null
     */
    public function load($id);

    /**
     * @param AggregateInterface $aggregate
     */
    public function save(AggregateInterface $aggregate);
}
