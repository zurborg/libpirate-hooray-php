<?php

namespace Pirate\Hooray;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit_Framework_TestCase;

class PirateHoorayArrTest extends PHPUnit_Framework_TestCase
{
    public function testOk()
    {
        $this->assertSame(Arr::ok(null), false);
        $this->assertSame(Arr::ok(''), false);
        $this->assertSame(Arr::ok('foo'), false);
        $this->assertSame(Arr::ok(new \Exception('foo')), false);
        $this->assertSame(Arr::ok([]), 0);
        $this->assertSame(Arr::ok([1]), 1);
        $this->assertSame(Arr::ok([1, 2]), 2);
        $this->assertSame(Arr::ok([1, 2, 3]), 3);
        $this->assertSame(Arr::ok(['foo' => 'bar']), 1);
        $this->assertSame(Arr::ok(['foo' => 'bar', 'bar' => 'foo']), 2);
        $this->assertSame(Arr::ok([null]), 1);
        $this->assertSame(Arr::ok([null, null]), 2);
    }

    public function testIndex()
    {
        $this->assertSame(Arr::index([], 0), null);

        $this->assertSame(Arr::index([1], -2), 0);
        $this->assertSame(Arr::index([1], -1), 0);
        $this->assertSame(Arr::index([1], 0), 0);
        $this->assertSame(Arr::index([1], +1), 0);
        $this->assertSame(Arr::index([1], +2), 0);

        $this->assertSame(Arr::index([1, 2], -2), 0);
        $this->assertSame(Arr::index([1, 2], -1), 1);
        $this->assertSame(Arr::index([1, 2], 0), 0);
        $this->assertSame(Arr::index([1, 2], +1), 1);
        $this->assertSame(Arr::index([1, 2], +2), 0);

        $this->assertSame(Arr::index([1, 2, 3], -9), 0);
        $this->assertSame(Arr::index([1, 2, 3], -8), 1);
        $this->assertSame(Arr::index([1, 2, 3], -7), 2);
        $this->assertSame(Arr::index([1, 2, 3], -6), 0);
        $this->assertSame(Arr::index([1, 2, 3], -5), 1);
        $this->assertSame(Arr::index([1, 2, 3], -4), 2);
        $this->assertSame(Arr::index([1, 2, 3], -3), 0);
        $this->assertSame(Arr::index([1, 2, 3], -2), 1);
        $this->assertSame(Arr::index([1, 2, 3], -1), 2);
        $this->assertSame(Arr::index([1, 2, 3], 0), 0);
        $this->assertSame(Arr::index([1, 2, 3], +1), 1);
        $this->assertSame(Arr::index([1, 2, 3], +2), 2);
        $this->assertSame(Arr::index([1, 2, 3], +3), 0);
        $this->assertSame(Arr::index([1, 2, 3], +4), 1);
        $this->assertSame(Arr::index([1, 2, 3], +5), 2);
        $this->assertSame(Arr::index([1, 2, 3], +6), 0);
        $this->assertSame(Arr::index([1, 2, 3], +7), 1);
        $this->assertSame(Arr::index([1, 2, 3], +8), 2);
        $this->assertSame(Arr::index([1, 2, 3], +9), 0);
    }

    public function testGet()
    {
        $A = [
            'foo' => 123,
            'bar' => 456,
        ];
        $this->assertSame(Arr::get($A, 'foo'), 123);
        $this->assertSame(Arr::get($A, 'bla'), null);
        $this->assertSame(Arr::get($A, 'bla', 'blubb'), 'blubb');
    }

    public function testLoad1()
    {
        $A = [
            'foo' => 123,
            'bar' => 456,
        ];
        $this->assertSame(Arr::load($A, 'foo', 'foo does not exists'), 123);
        $this->assertSame(Arr::load($A, 'bar', 'bar does not exists'), 456);
    }

    /**
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage foo does not exists
     */
    public function testLoad2()
    {
        $A = [];
        Arr::load($A, 'foo', 'foo does not exists');
    }

    public function testHas()
    {
        $A = [
            'foo' => 123,
        ];
        $this->assertSame(true, Arr::has($A, 'foo'));
        $this->assertSame(false, Arr::has($A, 'bla'));
    }

    public function testGetIndex()
    {
        $A = ['foo', 'bar'];
        $this->assertSame(Arr::getIndex($A, -1), 'bar');
    }

    public function testInit()
    {
        $A = [
            'foo' => 123,
        ];
        $this->assertSame(Arr::init($A, 'foo', 234), 123);
        $this->assertSame(Arr::init($A, 'bar', 456), 456);
        $this->assertSame($A, ['foo' => 123, 'bar' => 456]);
    }

    public function testConsume()
    {
        $A = [
            'foo' => 123,
        ];
        $this->assertSame(Arr::consume($A, 'foo'), 123);
        $this->assertSame($A, []);
        $this->assertSame(Arr::consume($A, 'bar', 456), 456);
        $this->assertSame($A, []);
    }

    public function testAssert1()
    {
        $A = [
            'foo' => 123,
        ];
        Arr::assert($A, 'foo', 'meh');
    }

    /**
     * @expectedException        OutOfBoundsException
     * @expectedExceptionMessage this does not exists
     */
    public function testAssert2()
    {
        $A = [
            'foo' => 123,
        ];
        Arr::assert($A, 'bar', 'this does not exists');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage that does not exists
     */
    public function testAssert3()
    {
        $A = [
            'foo' => 123,
        ];
        $e = new InvalidArgumentException('that does not exists');
        Arr::assert($A, 'bar', $e);
    }

    public function testAssert4()
    {
        $A = [
            'foo' => 123,
        ];
        $this->assertSame(
            Arr::assert(
                $A,
                'bar',
                function ($key) {
                    return "-$key-";
                }
            ),
            '-bar-'
        );
    }

    public function testIn()
    {
        $A = ['123', 456];
        $this->assertSame(Arr::in($A, 123), false);
        $this->assertSame(Arr::in($A, '123'), true);
        $this->assertSame(Arr::in($A, 456), true);
        $this->assertSame(Arr::in($A, '456'), false);
    }

    public function testIs()
    {
        $A = [
            'foo' => 123,
            'bar' => '456',
        ];
        $this->assertSame(Arr::is($A, 'foo', 123), true);
        $this->assertSame(Arr::is($A, 'bar', 456), false);
        $this->assertSame(Arr::is($A, 'xxx', 'yyy'), false);
        $this->assertSame(Arr::is($A, 'yyy', null), true);
    }

    public function testAny()
    {
        $A = ['aaa', 'bbb'];
        $this->assertSame(Arr::any($A, ['aaa', 'ccc']), true);
        $this->assertSame(Arr::any($A, ['ccc', 'ddd']), false);
        $this->assertSame(Arr::any($A, []), null);
    }

    public function testAll()
    {
        $A = ['aaa', 'bbb', 'ccc'];
        $this->assertSame(Arr::all($A, ['aaa', 'ccc']), true);
        $this->assertSame(Arr::all($A, ['ccc', 'ddd']), false);
        $this->assertSame(Arr::all($A, []), null);
    }

    public function testAssoc()
    {
        $A = [4, 9, 1];
        $B = ['foo' => 'bar'];
        $this->assertSame(Arr::assoc($A), false);
        $this->assertSame(Arr::assoc($B), true);
        $this->assertSame(Arr::assoc([]), null);
    }

    public function testFlist()
    {
        $this->assertSame(Arr::flist('foo'), ['foo']);
        $this->assertSame(Arr::flist(['foo', 'bar']), ['foo', 'bar']);
        $this->assertSame(Arr::flist(['foo' => 'bar']), [['foo' => 'bar']]);
        $this->assertSame(Arr::flist(null), []);
        $this->assertSame(Arr::flist(null, false), false);
    }

    public function testGetDeep()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
        ];
        $this->assertSame(Arr::getDeep($A, ['foo', 'bar']), 123);
        $this->assertSame(Arr::getDeep($A, ['bar', 'foo'], 456), 456);
    }

    public function testIsDeep()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
        ];
        $this->assertSame(Arr::isDeep($A, ['foo', 'bar'], 123), true);
        $this->assertSame(Arr::isDeep($A, ['bar', 'foo'], 456), false);
    }

    public function testSetDeep()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
        ];
        $old = Arr::setDeep($A, ['foo', 'bar'], 456);
        $this->assertSame(123, $old);
        $this->assertSame($A, ['foo' => ['bar' => 456]]);

        $old = Arr::setDeep($A, ['foo'], 789);
        $this->assertSame(['bar' => 456], $old);
        $this->assertSame($A, ['foo' => 789]);
    }

    public function testUnsetDeep()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
        ];
        Arr::unsetDeep($A, ['foo', 'bar']);
        $this->assertSame($A, ['foo' => []]);
        Arr::unsetDeep($A, ['foo']);
        $this->assertSame($A, []);
    }

    public function testGetPath()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
        ];
        $this->assertSame(Arr::getPath($A, '/foo/bar'), 123);
        $this->assertSame(Arr::getPath($A, '/bar/foo', 456), 456);
    }

    public function testIsPath()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
        ];
        $this->assertSame(Arr::isPath($A, '/foo/bar', 123), true);
        $this->assertSame(Arr::isPath($A, '/foo/bar', 456), false);
        $this->assertSame(Arr::isPath($A, '/bar/foo', 789), false);
    }

    public function testSetPath()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
        ];

        $old = Arr::setPath($A, '/foo/bar', 456);
        $this->assertSame(123, $old);
        $this->assertSame($A, ['foo' => ['bar' => 456]]);

        $old = Arr::setPath($A, '/foo', 789);
        $this->assertSame(['bar' => 456], $old);
        $this->assertSame($A, ['foo' => 789]);
    }

    public function testUnsetPath()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
        ];
        $old = Arr::unsetPath($A, '/foo/bar');
        $this->assertSame(123, $old);
        $this->assertSame($A, ['foo' => []]);

        $old = Arr::unsetPath($A, '/foo');
        $this->assertSame([], $old);
        $this->assertSame($A, []);
    }

    public function testSet()
    {
        $A = [
            'foo' => 123,
            'bar' => 456,
        ];

        $old = Arr::set($A, 'foo', 789);
        $this->assertSame(123, $old);
        $this->assertSame($A, ['foo' => 789, 'bar' => 456]);

        $old = Arr::set($A, 'xxx', 'yyy');
        $this->assertNull($old);
        $this->assertSame($A, ['foo' => 789, 'bar' => 456, 'xxx' => 'yyy']);
    }

    public function testMerge()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
            'bar' => 456,
        ];
        Arr::merge($A, ['foo' => ['bar' => 789]]);
        $this->assertSame($A, ['foo' => ['bar' => 789], 'bar' => 456]);
    }

    public function testDefaults()
    {
        $A = [
            'foo' => 123,
        ];
        Arr::defaults($A, ['foo' => 456, 'bar' => 789]);
        $this->assertSame($A, ['foo' => 123, 'bar' => 789]);
    }

    public function testShift()
    {
        $A = [1, 2, 3];
        $B = Arr::shift($A);
        $this->assertSame([2, 3], $A);
        $this->assertSame(1, $B);

        $A = [];
        $B = Arr::shift($A, 123);
        $this->assertSame([], $A);
        $this->assertSame(123, $B);
    }

    public function testPop()
    {
        $A = [1, 2, 3];
        $B = Arr::pop($A);
        $this->assertSame([1, 2], $A);
        $this->assertSame(3, $B);

        $A = [];
        $B = Arr::pop($A, 123);
        $this->assertSame([], $A);
        $this->assertSame(123, $B);
    }

    public function testReverse()
    {
        $A = [1, 2, 3, 4];
        Arr::reverse($A);
        $this->assertSame([4, 3, 2, 1], $A);

        $B = ['foo' => 123, 'bar' => 456];
        Arr::reverse($B);
        $this->assertSame(['bar' => 456, 'foo' => 123], $B);
    }
}
