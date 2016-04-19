<?php
namespace Nicklasos;

class ProcessPoolTest extends \PHPUnit_Framework_TestCase
{
    public function testPool()
    {
        $file = FIXTURES_ROOT . '/child.php';

        $pool = new ProcessPool(
            "php $file",
            [
                1,
                2,
                3,
            ],
            2
        );

        $sum = 0;

        $result = $pool->run(function ($arg, $result) use (&$sum) {
            $sum += $result;
        });

        $this->assertEquals(9, $sum);
        $this->assertEquals(9, array_sum($result));
    }
}
