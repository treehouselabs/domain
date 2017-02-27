<?php

namespace TreeHouse\Domain\Tests;

use Iterator;
use PHPUnit\Framework\TestCase;
use stdClass;
use TreeHouse\Domain\EventStream;

class EventStreamTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_countable()
    {
        $eventStream = new EventStream([
            new stdClass(),
            new stdClass(),
        ]);

        $this->assertCount(
            2, $eventStream
        );
    }

    /**
     * @test
     */
    public function it_is_iterable()
    {
        $eventStream = new EventStream();

        $this->assertInstanceOf(
            Iterator::class,
            $eventStream->getIterator()
        );
    }

    /**
     * @test
     */
    public function it_appends()
    {
        $event = new stdClass();

        $eventStream = new EventStream();
        $eventStream->append($event);

        $this->assertSame(
            $event,
            iterator_to_array($eventStream->getIterator())[0]
        );
    }

    /**
     * @test
     */
    public function it_tracks_for_another_event_stream()
    {
        $eventStream = new EventStream();
        $trackingEventStream = new EventStream();

        $event = new stdClass();

        $eventStream->track($trackingEventStream);
        $eventStream->append($event);

        $this->assertSame(
            $event,
            iterator_to_array($eventStream->getIterator())[0]
        );

        $this->assertSame(
            $event,
            iterator_to_array($trackingEventStream->getIterator())[0]
        );
    }

    /**
     * @test
     */
    public function it_contains()
    {
        $event = new DummyEvent();

        $eventStream = new EventStream([$event]);

        $this->assertEquals(
            true,
            $eventStream->contains('Dummy')
        );
    }

    /**
     * @test
     */
    public function it_contains_not()
    {
        $event = new DummyEvent();

        $eventStream = new EventStream([$event]);

        $this->assertEquals(
            false,
            $eventStream->contains('SomeOther')
        );
    }

    /**
     * @test
     */
    public function it_finds_one()
    {
        $event = new DummyEvent();

        $eventStream = new EventStream([$event]);

        $this->assertEquals(
            $event,
            $eventStream->findOne('Dummy')
        );
    }

    /**
     * @test
     */
    public function it_returns_null_if_none_found()
    {
        $event = new DummyEvent();

        $eventStream = new EventStream([$event]);

        $this->assertEquals(
            null,
            $eventStream->findOne('SomeOther')
        );
    }

    /**
     * @test
     */
    public function it_clears()
    {
        $eventStream = new EventStream([new stdClass()]);

        $eventStream->clear();

        $this->assertCount(
            0, $eventStream
        );
    }
}
