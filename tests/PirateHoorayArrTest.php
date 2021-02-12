<?php

namespace Pirate\Hooray;

use Exception;
use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Throwable;

class PirateHoorayArrTest extends TestCase
{
    public function testOk()
    {
        $this->assertFalse(Arr::ok(null));
        $this->assertFalse(Arr::ok(''));
        $this->assertFalse(Arr::ok('foo'));
        $this->assertFalse(Arr::ok(new Exception('foo')));
        $this->assertSame(0, Arr::ok([]));
        $this->assertSame(1, Arr::ok([1]));
        $this->assertSame(2, Arr::ok([1, 2]));
        $this->assertSame(3, Arr::ok([1, 2, 3]));
        $this->assertSame(1, Arr::ok(['foo' => 'bar']));
        $this->assertSame(2, Arr::ok(['foo' => 'bar', 'bar' => 'foo']));
        $this->assertSame(1, Arr::ok([null]));
        $this->assertSame(2, Arr::ok([null, null]));
    }

    public function testIndex()
    {
        $this->assertNull(Arr::index([], 0));

        $this->assertSame(0, Arr::index([1], -2));
        $this->assertSame(0, Arr::index([1], -1));
        $this->assertSame(0, Arr::index([1], 0));
        $this->assertSame(0, Arr::index([1], +1));
        $this->assertSame(0, Arr::index([1], +2));

        $this->assertSame(0, Arr::index([1, 2], -2));
        $this->assertSame(1, Arr::index([1, 2], -1));
        $this->assertSame(0, Arr::index([1, 2], 0));
        $this->assertSame(1, Arr::index([1, 2], +1));
        $this->assertSame(0, Arr::index([1, 2], +2));

        $this->assertSame(0, Arr::index([1, 2, 3], -9));
        $this->assertSame(1, Arr::index([1, 2, 3], -8));
        $this->assertSame(2, Arr::index([1, 2, 3], -7));
        $this->assertSame(0, Arr::index([1, 2, 3], -6));
        $this->assertSame(1, Arr::index([1, 2, 3], -5));
        $this->assertSame(2, Arr::index([1, 2, 3], -4));
        $this->assertSame(0, Arr::index([1, 2, 3], -3));
        $this->assertSame(1, Arr::index([1, 2, 3], -2));
        $this->assertSame(2, Arr::index([1, 2, 3], -1));
        $this->assertSame(0, Arr::index([1, 2, 3], 0));
        $this->assertSame(1, Arr::index([1, 2, 3], +1));
        $this->assertSame(2, Arr::index([1, 2, 3], +2));
        $this->assertSame(0, Arr::index([1, 2, 3], +3));
        $this->assertSame(1, Arr::index([1, 2, 3], +4));
        $this->assertSame(2, Arr::index([1, 2, 3], +5));
        $this->assertSame(0, Arr::index([1, 2, 3], +6));
        $this->assertSame(1, Arr::index([1, 2, 3], +7));
        $this->assertSame(2, Arr::index([1, 2, 3], +8));
        $this->assertSame(0, Arr::index([1, 2, 3], +9));
    }

    public function testGet()
    {
        $A = [
            'foo' => 123,
            'bar' => 456,
        ];
        $this->assertSame(123, Arr::get($A, 'foo'));
        $this->assertNull(Arr::get($A, 'bla'));
        $this->assertSame('blubb', Arr::get($A, 'bla', 'blubb'));
    }

    public function testLoad1()
    {
        $A = [
            'foo' => 123,
            'bar' => 456,
        ];
        $this->assertSame(123, Arr::load($A, 'foo', 'foo does not exists'));
        $this->assertSame(456, Arr::load($A, 'bar', 'bar does not exists'));
    }

    public function testLoad2()
    {
        $this->expectExceptionMessage("foo does not exists");
        $this->expectException(OutOfBoundsException::class);
        $A = [];
        Arr::load($A, 'foo', 'foo does not exists');
    }

    public function testHas()
    {
        $A = [
            'foo' => 123,
        ];
        $this->assertTrue(Arr::has($A, 'foo'));
        $this->assertFalse(Arr::has($A, 'bla'));
    }

    public function testGetIndex()
    {
        $A = ['foo', 'bar'];
        $this->assertSame('bar', Arr::getIndex($A, -1));
    }

    public function testInit()
    {
        $A = [
            'foo' => 123,
        ];
        $this->assertSame(123, Arr::init($A, 'foo', 234));
        $this->assertSame(456, Arr::init($A, 'bar', 456));
        $this->assertSame(['foo' => 123, 'bar' => 456], $A);
    }

    public function testConsume()
    {
        $A = [
            'foo' => 123,
        ];
        $this->assertSame(123, Arr::consume($A, 'foo'));
        $this->assertSame([], $A);
        $this->assertSame(456, Arr::consume($A, 'bar', 456));
        $this->assertSame([], $A);
    }

    /**
     * @throws Throwable
     */
    public function testAssert1()
    {
        $this->expectNotToPerformAssertions();
        $A = [
            'foo' => 123,
        ];
        Arr::assert($A, 'foo', 'meh');
    }

    /**
     * @throws Throwable
     */
    public function testAssert2()
    {
        $this->expectExceptionMessage("this does not exists");
        $this->expectException(OutOfBoundsException::class);
        $A = [
            'foo' => 123,
        ];
        Arr::assert($A, 'bar', 'this does not exists');
    }

    /**
     * @throws Throwable
     */
    public function testAssert3()
    {
        $this->expectExceptionMessage("that does not exists");
        $this->expectException(InvalidArgumentException::class);
        $A = [
            'foo' => 123,
        ];
        $e = new InvalidArgumentException('that does not exists');
        Arr::assert($A, 'bar', $e);
    }

    /**
     * @throws Throwable
     */
    public function testAssert4()
    {
        $A = [
            'foo' => 123,
        ];
        $this->assertSame(
            '-bar-',
            Arr::assert(
                $A,
                'bar',
                function ($key) {
                    return "-$key-";
                }
            )
        );
    }

    public function testIn()
    {
        $A = ['123', 456];
        $this->assertFalse(Arr::in($A, 123));
        $this->assertTrue(Arr::in($A, '123'));
        $this->assertTrue(Arr::in($A, 456));
        $this->assertFalse(Arr::in($A, '456'));
    }

    public function testIs()
    {
        $A = [
            'foo' => 123,
            'bar' => '456',
        ];
        $this->assertTrue(Arr::is($A, 'foo', 123));
        $this->assertFalse(Arr::is($A, 'bar', 456));
        $this->assertFalse(Arr::is($A, 'xxx', 'yyy'));
        $this->assertTrue(Arr::is($A, 'yyy', null));
    }

    public function testAny()
    {
        $A = ['aaa', 'bbb'];
        $this->assertTrue(Arr::any($A, ['aaa', 'ccc']));
        $this->assertFalse(Arr::any($A, ['ccc', 'ddd']));
        $this->assertFalse(Arr::any($A, []));
    }

    public function testAll()
    {
        $A = ['aaa', 'bbb', 'ccc'];
        $this->assertTrue(Arr::all($A, ['aaa', 'ccc']));
        $this->assertFalse(Arr::all($A, ['ccc', 'ddd']));
        $this->assertFalse(Arr::all($A, []));
    }

    public function testAssoc()
    {
        $A = [4, 9, 1];
        $B = ['foo' => 'bar'];
        $this->assertFalse(Arr::assoc($A));
        $this->assertTrue(Arr::assoc($B));
        $this->assertFalse(Arr::assoc([]));
    }

    public function testFlist()
    {
        $this->assertSame(['foo', 'bar'], Arr::flist(['foo', 'bar']));
        $this->assertSame([['foo' => 'bar']], Arr::flist(['foo' => 'bar']));
    }

    public function testGetDeep()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
        ];
        $this->assertSame(123, Arr::getDeep($A, ['foo', 'bar']));
        $this->assertSame(456, Arr::getDeep($A, ['bar', 'foo'], 456));
    }

    public function testIsDeep()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
        ];
        $this->assertTrue(Arr::isDeep($A, ['foo', 'bar'], 123));
        $this->assertFalse(Arr::isDeep($A, ['bar', 'foo'], 456));
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
        $this->assertSame(123, Arr::getPath($A, '/foo/bar'));
        $this->assertSame(456, Arr::getPath($A, '/bar/foo', 456));
    }

    public function testIsPath()
    {
        $A = [
            'foo' => [
                'bar' => 123,
            ],
        ];
        $this->assertTrue(Arr::isPath($A, '/foo/bar', 123));
        $this->assertFalse(Arr::isPath($A, '/foo/bar', 456));
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
