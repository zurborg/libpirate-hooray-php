<?php

namespace Pirate\Hooray;

use Pirate\Hooray\Arr;
class ArrTest extends \PHPUnit_Framework_TestCase
{

    public function testOk() {
        $this->assertSame(Arr::ok(null), false);
        $this->assertSame(Arr::ok(''), false);
        $this->assertSame(Arr::ok('foo'), false);
        $this->assertSame(Arr::ok(new \Exception('foo')), false);
        $this->assertSame(Arr::ok([]), 0);
        $this->assertSame(Arr::ok([1]), 1);
        $this->assertSame(Arr::ok([1,2]), 2);
        $this->assertSame(Arr::ok([1,2,3]), 3);
        $this->assertSame(Arr::ok(['foo'=>'bar']), 1);
        $this->assertSame(Arr::ok(['foo'=>'bar','bar'=>'foo']), 2);
        $this->assertSame(Arr::ok([null]), 1);
        $this->assertSame(Arr::ok([null,null]), 2);
    }

    public function testIndex() {
        $this->assertSame(Arr::index([], 0), null);

        $this->assertSame(Arr::index([1], -2), 0);
        $this->assertSame(Arr::index([1], -1), 0);
        $this->assertSame(Arr::index([1],  0), 0);
        $this->assertSame(Arr::index([1], +1), 0);
        $this->assertSame(Arr::index([1], +2), 0);

        $this->assertSame(Arr::index([1,2], -2), 0);
        $this->assertSame(Arr::index([1,2], -1), 1);
        $this->assertSame(Arr::index([1,2],  0), 0);
        $this->assertSame(Arr::index([1,2], +1), 1);
        $this->assertSame(Arr::index([1,2], +2), 0);

        $this->assertSame(Arr::index([1,2,3], -9), 0);
        $this->assertSame(Arr::index([1,2,3], -8), 1);
        $this->assertSame(Arr::index([1,2,3], -7), 2);
        $this->assertSame(Arr::index([1,2,3], -6), 0);
        $this->assertSame(Arr::index([1,2,3], -5), 1);
        $this->assertSame(Arr::index([1,2,3], -4), 2);
        $this->assertSame(Arr::index([1,2,3], -3), 0);
        $this->assertSame(Arr::index([1,2,3], -2), 1);
        $this->assertSame(Arr::index([1,2,3], -1), 2);
        $this->assertSame(Arr::index([1,2,3],  0), 0);
        $this->assertSame(Arr::index([1,2,3], +1), 1);
        $this->assertSame(Arr::index([1,2,3], +2), 2);
        $this->assertSame(Arr::index([1,2,3], +3), 0);
        $this->assertSame(Arr::index([1,2,3], +4), 1);
        $this->assertSame(Arr::index([1,2,3], +5), 2);
        $this->assertSame(Arr::index([1,2,3], +6), 0);
        $this->assertSame(Arr::index([1,2,3], +7), 1);
        $this->assertSame(Arr::index([1,2,3], +8), 2);
        $this->assertSame(Arr::index([1,2,3], +9), 0);
    }

    public function testGet() {
        $A = [
            'foo' => 123,
            'bar' => 456
        ];
        $this->assertSame(Arr::get($A, 'foo'), 123);
        $this->assertSame(Arr::get($A, 'bla'), null);
        $this->assertSame(Arr::get($A, 'bla', 'blubb'), 'blubb');
    }

    public function testGetIndex() {
        $A = [ 'foo', 'bar' ];
        $this->assertSame(Arr::getIndex($A, -1), 'bar');
    }

    public function testInit() {
        $A = [
            'foo' => 123
        ];
        $this->assertSame(Arr::init($A, 'foo', 234), 123);
        $this->assertSame(Arr::init($A, 'bar', 456), 456);
        $this->assertSame($A, ['foo'=>123,'bar'=>456]);
    }

    public function testConsume() {
        $A = [
            'foo' => 123
        ];
        $this->assertSame(Arr::consume($A, 'foo'), 123);
        $this->assertSame($A, []);
        $this->assertSame(Arr::consume($A, 'bar', 456), 456);
        $this->assertSame($A, []);
    }

    public function testAssert1() {
        $A = [
            'foo' => 123
        ];
        Arr::assert($A, 'foo', 'meh');
    }

    /**
     * @expectedException        DomainException
     * @expectedExceptionMessage this does not exists
     */
    public function testAssert2() {
        $A = [
            'foo' => 123
        ];
        Arr::assert($A, 'bar', 'this does not exists');
    }

    /**
     * @expectedException        LogicException
     * @expectedExceptionMessage that does not exists
     */
    public function testAssert3() {
        $A = [
            'foo' => 123
        ];
        $e = new \InvalidArgumentException('that does not exists');
        Arr::assert($A, 'bar', $e);
    }

    public function testAssert4() {
        $A = [
            'foo' => 123
        ];
        $this->assertSame(Arr::assert($A, 'bar', function ($key) { return "-$key-"; }), '-bar-');
    }

    public function testIn() {
        $A = [ '123', 456 ];
        $this->assertSame(Arr::in($A,  123 ), false);
        $this->assertSame(Arr::in($A, '123'), true);
        $this->assertSame(Arr::in($A,  456 ), true);
        $this->assertSame(Arr::in($A, '456'), false);
    }

    public function testIs() {
        $A = [
            'foo' =>  123,
            'bar' => '456'
        ];
        $this->assertSame(Arr::is($A, 'foo', 123), true);
        $this->assertSame(Arr::is($A, 'bar', 456), false);
        $this->assertSame(Arr::is($A, 'xxx', 'yyy'), false);
        $this->assertSame(Arr::is($A, 'yyy', null), true);
    }

    public function testAny() {
        $A = [ 'aaa', 'bbb' ];
        $this->assertSame(Arr::any($A, [ 'aaa', 'ccc' ]), true);
        $this->assertSame(Arr::any($A, [ 'ccc', 'ddd' ]), false);
        $this->assertSame(Arr::any($A, []), null);
    }

    public function testAll() {
        $A = [ 'aaa', 'bbb', 'ccc' ];
        $this->assertSame(Arr::all($A, [ 'aaa', 'ccc' ]), true);
        $this->assertSame(Arr::all($A, [ 'ccc', 'ddd' ]), false);
        $this->assertSame(Arr::all($A, []), null);
    }

    public function testAssoc() {
        $A = [ 4, 9, 1 ];
        $B = [ 'foo' => 'bar' ];
        $this->assertSame(Arr::assoc($A), false);
        $this->assertSame(Arr::assoc($B), true);
        $this->assertSame(Arr::assoc([]), null);
    }

    public function testFlist() {
        $this->assertSame(Arr::flist('foo'), [ 'foo' ]);
        $this->assertSame(Arr::flist([ 'foo' ,  'bar' ]),   [ 'foo' ,  'bar' ]  );
        $this->assertSame(Arr::flist([ 'foo' => 'bar' ]), [ [ 'foo' => 'bar' ] ]);
        $this->assertSame(Arr::flist(null), []);
        $this->assertSame(Arr::flist(null, false), false);
    }

    public function testGetDeep() {
        $A = [
            'foo' => [
                'bar' => 123
            ]
        ];
        $this->assertSame(Arr::getDeep($A, [ 'foo', 'bar' ]     ), 123);
        $this->assertSame(Arr::getDeep($A, [ 'bar', 'foo' ], 456), 456);
    }

    public function testIsDeep() {
        $A = [
            'foo' => [
                'bar' => 123
            ]
        ];
        $this->assertSame(Arr::isDeep($A, [ 'foo', 'bar' ], 123), true);
        $this->assertSame(Arr::isDeep($A, [ 'bar', 'foo' ], 456), false);
    }

    public function testSetDeep() {
        $A = [
            'foo' => [
                'bar' => 123
            ]
        ];
        Arr::setDeep($A, [ 'foo', 'bar' ], 456);
        $this->assertSame($A, [ 'foo' => [ 'bar' => 456 ] ]);
        Arr::setDeep($A, [ 'foo' ], 789);
        $this->assertSame($A, [ 'foo' => 789 ]);
    }

    public function testGetPath() {
        $A = [
            'foo' => [
                'bar' => 123
            ]
        ];
        $this->assertSame(Arr::getPath($A, '/foo/bar'     ), 123);
        $this->assertSame(Arr::getPath($A, '/bar/foo', 456), 456);
    }

    public function testIsPath() {
        $A = [
            'foo' => [
                'bar' => 123
            ]
        ];
        $this->assertSame(Arr::isPath($A, '/foo/bar', 123), true);
        $this->assertSame(Arr::isPath($A, '/foo/bar', 456), false);
        $this->assertSame(Arr::isPath($A, '/bar/foo', 789), false);
    }

    public function testSetPath() {
        $A = [
            'foo' => [
                'bar' => 123
            ]
        ];
        Arr::setPath($A, '/foo/bar', 456);
        $this->assertSame($A, [ 'foo' => [ 'bar' => 456 ] ]);
        Arr::setPath($A, '/foo', 789);
        $this->assertSame($A, [ 'foo' => 789 ]);
    }
}

use Pirate\Hooray\Str;
class StrTest extends \PHPUnit_Framework_TestCase
{
    public function testOk() {
        $this->assertSame(Str::ok(null), false);
        $this->assertSame(Str::ok(false), false);
        $this->assertSame(Str::ok(true), false);
        $this->assertSame(Str::ok([]), false);
        $this->assertSame(Str::ok(new \Exception('foo')), false);
        $this->assertSame(Str::ok(''), 0);
        $this->assertSame(Str::ok('foo'), 3);
    }

    public function testSplit() {
        $this->assertSame(Str::split(''), null);
        $this->assertSame(Str::split('/'), ['']);
        $this->assertSame(Str::split('/foo'), ['foo']);
        $this->assertSame(Str::split('/foo/bar'), ['foo', 'bar']);
        $this->assertSame(Str::split('/foo/bar/'), ['foo', 'bar', '']);
        $this->assertSame(Str::split('//'), ['', '']);
        $this->assertSame(Str::split('///'), ['', '', '']);
        $this->assertSame(Str::split('//foo//bar//'), ['', 'foo', '', 'bar', '', '']);
        $this->assertSame(Str::split('.foo.bar'), ['foo', 'bar']);
        $this->assertSame(Str::split('#foo#bar'), ['foo', 'bar']);
        $this->assertSame(Str::split(':foo:bar'), ['foo', 'bar']);
        $this->assertSame(Str::split('\foo\bar'), ['foo', 'bar']);
        $this->assertSame(Str::split(' '), ['']);
        $this->assertSame(Str::split('  '), ['', '']);
        $this->assertSame(Str::split('/a/b/c/d/e', 0), ['a/b/c/d/e']);
        $this->assertSame(Str::split('/a/b/c/d/e', 1), ['a/b/c/d/e']);
        $this->assertSame(Str::split('/a/b/c/d/e', 2), ['a', 'b/c/d/e']);
        $this->assertSame(Str::split('/a/b/c/d/e', 3), ['a', 'b', 'c/d/e']);
        $this->assertSame(Str::split('/a/b/c/d/e', 4), ['a', 'b', 'c', 'd/e']);
        $this->assertSame(Str::split('/a/b/c/d/e', 5), ['a', 'b', 'c', 'd', 'e']);
        $this->assertSame(Str::split('/a/b/c/d/e', 6), ['a', 'b', 'c', 'd', 'e']);
    }

    public function testPluralize() {
        $this->assertSame(Str::pluralize('', 0), '');
        $this->assertSame(Str::pluralize('$', 123), '123');
        $this->assertSame(Str::pluralize('$$', 123), '123123');

        $this->assertSame(Str::pluralize('$ item(s) need{s}', 0), '0 items need');
        $this->assertSame(Str::pluralize('$ item(s) need{s}', 1), '1 item needs');
        $this->assertSame(Str::pluralize('$ item(s) need{s}', 2), '2 items need');

        $this->assertSame(Str::pluralize('(1st|2nd|3rd|$th)', 0), '0th');
        $this->assertSame(Str::pluralize('(1st|2nd|3rd|$th)', 1), '1st');
        $this->assertSame(Str::pluralize('(1st|2nd|3rd|$th)', 2), '2nd');
        $this->assertSame(Str::pluralize('(1st|2nd|3rd|$th)', 3), '3rd');
        $this->assertSame(Str::pluralize('(1st|2nd|3rd|$th)', 4), '4th');
        $this->assertSame(Str::pluralize('(1st|2nd|3rd|$th)', 5), '5th');

        $this->assertSame(Str::pluralize('{zero|one|two|three|more}', 0), 'zero');
        $this->assertSame(Str::pluralize('{zero|one|two|three|more}', 1), 'one');
        $this->assertSame(Str::pluralize('{zero|one|two|three|more}', 2), 'two');
        $this->assertSame(Str::pluralize('{zero|one|two|three|more}', 3), 'three');
        $this->assertSame(Str::pluralize('{zero|one|two|three|more}', 4), 'more');
        $this->assertSame(Str::pluralize('{zero|one|two|three|more}', 5), 'more');

        $this->assertSame(Str::pluralize('{No|One|$} quer(y|ies) (is|are)', 0), 'No queries are');
        $this->assertSame(Str::pluralize('{No|One|$} quer(y|ies) (is|are)', 1), 'One query is');
        $this->assertSame(Str::pluralize('{No|One|$} quer(y|ies) (is|are)', 2), '2 queries are');
        $this->assertSame(Str::pluralize('{No|One|$} quer(y|ies) (is|are)', 3), '3 queries are');

        $this->assertSame(Str::pluralize('-{zero|||}-{|one||}-{||two|}-{|||three}-', 0), '-zero----');
        $this->assertSame(Str::pluralize('-{zero|||}-{|one||}-{||two|}-{|||three}-', 1), '--one---');
        $this->assertSame(Str::pluralize('-{zero|||}-{|one||}-{||two|}-{|||three}-', 2), '---two--');
        $this->assertSame(Str::pluralize('-{zero|||}-{|one||}-{||two|}-{|||three}-', 3), '----three-');
        $this->assertSame(Str::pluralize('-{zero|||}-{|one||}-{||two|}-{|||three}-', 4), '----three-');
    }
}
