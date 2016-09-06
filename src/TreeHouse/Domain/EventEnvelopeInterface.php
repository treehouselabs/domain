<?php

namespace TreeHouse\Domain;

interface EventEnvelopeInterface
{
    public function getEventName();

    public function getEvent();
}
