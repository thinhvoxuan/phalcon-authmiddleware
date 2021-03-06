<?php

namespace Sid\Phalcon\AuthMiddleware\Tests\Unit;

class MiddlewareTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
        \Phalcon\Di::reset();

        $di = new \Phalcon\Di\FactoryDefault();

        $di->set(
            "dispatcher",
            function () {
                $dispatcher = new \Phalcon\Mvc\Dispatcher();

                $eventsManager = new \Phalcon\Events\Manager();

                $eventsManager->attach("dispatch", new \Sid\Phalcon\AuthMiddleware\Event());

                $dispatcher->setEventsManager($eventsManager);

                return $dispatcher;
            },
            true
        );

        $this->dispatcher = $di->get("dispatcher");
    }

    protected function _after()
    {
    }

    // tests
    public function testMiddlewareIsAbleToInterfereWhenReturningTrue()
    {
        $dispatcher = $this->dispatcher;

        $dispatcher->setControllerName("index");
        $dispatcher->setActionName("index");

        $dispatcher->dispatch();

        $this->assertEquals(
            "Goodbye cruel world",
            $dispatcher->getReturnedValue()
        );
    }


    public function testMiddlewareDoesNotInterfereWhenReturningFalse()
    {
        $dispatcher = $this->dispatcher;

        $dispatcher->setControllerName("index");
        $dispatcher->setActionName("index2");

        $dispatcher->dispatch();

        $this->assertEquals(
            "Hello world",
            $dispatcher->getReturnedValue()
        );
    }

    public function testDispatcherWorksAsNormalWithoutAnyMiddleware()
    {
        $dispatcher = $this->dispatcher;

        $dispatcher->setControllerName("index");
        $dispatcher->setActionName("noMiddleware");

        $dispatcher->dispatch();

        $this->assertEquals(
            "Hello world",
            $dispatcher->getReturnedValue()
        );
    }

    public function testAnExceptionIsThrownIfWePassSomethingThatIsntProperMiddleware()
    {
        $dispatcher = $this->dispatcher;

        $dispatcher->setControllerName("index");
        $dispatcher->setActionName("notProperMiddleware");

        try {
            $dispatcher->dispatch();
            
            $this->assertTrue(false);
        } catch (\Sid\Phalcon\AuthMiddleware\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
