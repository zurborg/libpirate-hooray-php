<?php

namespace Pirate\Hooray;

use Pirate\Hooray\Str;

class PirateHoorayStrTest extends \PHPUnit_Framework_TestCase
{
    public function testOk()
    {
        $this->assertSame(Str::ok(null), false);
        $this->assertSame(Str::ok(false), false);
        $this->assertSame(Str::ok(true), false);
        $this->assertSame(Str::ok([]), false);
        $this->assertSame(Str::ok(new \Exception('foo')), false);
        $this->assertSame(Str::ok(''), 0);
        $this->assertSame(Str::ok('foo'), 3);
        $this->assertSame(Str::ok("123"), 3);
        $this->assertSame(Str::ok(12345), 5);
        $this->assertSame(Str::ok(1e3), 4);
        $this->assertSame(Str::ok(0e0), 1);
    }

    public function testMatch()
    {
        $this->assertSame(Str::fullmatch('abc', '[abc]{3}'), [ '0' => 'abc' ]);
        $this->assertSame(Str::fullmatch('abcdef', '[cde]{3}'), null);
        $this->assertSame([['zero','one','two','three']], Str::matchall('-{zero|||}-{|one||}-{||two|}-{|||three}-', '/\w+/'));
    }

    public function testSplit()
    {
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

    public function testLoop()
    {
        $matches = [];
        Str::loop('-abc-def', '/-(\w+)/', function ($match) use (&$matches) {
            $matches[] = $match;
        });
        $this->assertSame([['-abc', 'abc'], ['-def', 'def']], $matches);
    }

    public function testPluralize()
    {
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

    public function testUuid()
    {
        $uuids = [];
        for ($i = 0; $i < 10; $i++) {
            $uuid = Str::uuidV4();
            $this->assertFalse(array_key_exists($uuid, $uuids), "UUID was generated before");
            $this->assertRegExp('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid, "uuid v4 round $i");
            $uuids[$uuid] = $i;
        }
    }

    public function testEnbrace()
    {
        $this->assertSame('Hello <World>!', Str::enbrace('Hello {World}!', '<%s>'));
        $this->assertSame('Hello <{World>}!', Str::enbrace('Hello {\{World}\}!', '<%s>'));
        $this->assertSame('<Hello> <World>!', Str::enbrace('{Hello} {World}!', '<%s>'));
    }

    public function testUpper()
    {
        $str = 'asdfghæðđŋħ';
        Str::upper($str);
        $this->assertSame('ASDFGHÆÐĐŊĦ', $str);
    }

    public function testLower()
    {
        $str = 'ASDFGHÆÐĐŊĦ';
        Str::lower($str);
        $this->assertSame('asdfghæðđŋħ', $str);
    }
}
