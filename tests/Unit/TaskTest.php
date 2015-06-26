<?php

namespace Tarantool\Queue\Tests\Unit;

use Tarantool\Queue\States;
use Tarantool\Queue\Task;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    static $isserMap = [
        States::READY => 'isReady',
        States::TAKEN => 'isTaken',
        States::DONE => 'isDone',
        States::BURIED => 'isBuried',
        States::DELAYED => 'isDelayed',
    ];

    /**
     * @dataProvider provideTuples
     */
    public function testCreateFromTuple(array $tuple)
    {
        $task = Task::createFromTuple($tuple);

        $this->assertSame($tuple[0], $task->getId());
        $this->assertSame($tuple[1], $task->getState());

        if (3 === count($tuple)) {
            $this->assertSame($tuple[2], $task->getData());
        }

        return $task;
    }

    public function provideTuples()
    {
        return [
            [[0, States::READY, [42]]],
            [[1, States::DONE, null]],
            [[2, States::BURIED]],
        ];
    }

    /**
     * @dataProvider provideStates
     */
    public function testIsser($state)
    {
        $task = Task::createFromTuple([0, $state, null]);

        $this->assertTrue($task->{self::$isserMap[$state]}());

        $issers = self::$isserMap;
        unset($issers[$state]);

        foreach ($issers as $isser) {
            $this->assertFalse($task->$isser());
        }
    }

    public function provideStates()
    {
        return [
            [States::READY],
            [States::TAKEN],
            [States::DONE],
            [States::BURIED],
            [States::DELAYED],
        ];
    }
}
