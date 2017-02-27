<?php

namespace TreeHouse\Domain\Tests;

use PHPUnit\Framework\TestCase;
use TreeHouse\Domain\EventEnvelopeInterface;
use TreeHouse\Domain\EventName;

class EventNameTest extends TestCase
{
    /**
     * @test
     */
    public function it_makes_string()
    {
        $event = new EventName(new DummyEvent());

        $this->assertEquals('Dummy', (string) $event);
    }

    /**
     * @test
     */
    public function it_accepts_string()
    {
        $event = new EventName('\Tests\TreeHouse\EventSourcing\DummyEvent');

        $this->assertEquals('Dummy', (string) $event);
    }

    /**
     * @test
     */
    public function it_accepts_event_envelope()
    {
        $envelope = $this->prophesize(EventEnvelopeInterface::class);
        $envelope->getEventName()->willReturn('Dummy');

        $event = new EventName($envelope->reveal());

        $this->assertEquals('Dummy', (string) $event);
    }
}
