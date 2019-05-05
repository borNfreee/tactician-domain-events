<?php

namespace BornFree\TacticianDomainEvent\Tests\Middleware;

use BornFree\TacticianDomainEvent\EventDispatcher\EventDispatcher;
use BornFree\TacticianDomainEvent\Middleware\ReleaseRecordedEventsMiddleware;
use BornFree\TacticianDomainEvent\Recorder\ContainsRecordedEvents;
use BornFree\TacticianDomainEvent\Recorder\EventRecorder;
use BornFree\TacticianDomainEvent\Tests\Fixtures\CreateUserCommand;
use BornFree\TacticianDomainEvent\Tests\Fixtures\UserWasCreated;

class ReleaseRecordedEventsMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReleaseRecordedEventsMiddleware
     */
    private $middleware;

    /**
     * @var ContainsRecordedEvents|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventRecorderMock;

    /**
     * @var EventDispatcher|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventDispatcherMock;

    /**
     * @var callable
     */
    private $nextMiddlewareMock;

    public function setUp()
    {
        $this->eventRecorderMock = $this->getMockBuilder(ContainsRecordedEvents::class)->getMock();
        $this->eventDispatcherMock = $this->getMockBuilder(EventDispatcher::class)->getMock();

        $this->middleware = new ReleaseRecordedEventsMiddleware(
            $this->eventRecorderMock,
            $this->eventDispatcherMock
        );

        $this->nextMiddlewareMock = function () {
        };
    }

    /**
     * @test
     */
    public function it_dispatches_recorded_events()
    {
        $command = new CreateUserCommand();

        $this->middleware = new ReleaseRecordedEventsMiddleware(
            new EventRecorder(),
            $this->eventDispatcherMock
        );

        $this->middleware->execute($command, $this->nextMiddlewareMock);

        $this->assertTrue(true);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_erases_events_when_exception_is_raised()
    {
        $command = new CreateUserCommand();

        $nextMiddlewareMock = function () {
            throw new \Exception('Error');
        };

        $this->middleware = new ReleaseRecordedEventsMiddleware(
            new EventRecorder(),
            $this->eventDispatcherMock
        );

        $this->middleware->execute($command, $nextMiddlewareMock);
    }
}
