<?php

namespace Pirate\Hooray;

use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class PirateHoorayStrTest extends TestCase
{
    public function testOk()
    {
        $this->assertSame(Str::ok(null), false);
        $this->assertSame(Str::ok(false), false);
        $this->assertSame(Str::ok(true), false);
        $this->assertSame(Str::ok([]), false);
        $this->assertSame(Str::ok(new Exception('foo')), false);
        $this->assertSame(Str::ok(''), 0);
        $this->assertSame(Str::ok('foo'), 3);
        $this->assertSame(Str::ok("123"), 3);
        $this->assertSame(Str::ok(12345), 5);
        $this->assertSame(Str::ok(1e3), 4);
        $this->assertSame(Str::ok(0e0), 1);
    }

    public function testMatch()
    {
        $this->assertSame(Str::fullmatch('abc', '[abc]{3}'), ['0' => 'abc']);
        $this->assertSame(Str::fullmatch('abcdef', '[cde]{3}'), null);
        $this->assertSame([['zero', 'one', 'two', 'three']], Str::matchall('-{zero|||}-{|one||}-{||two|}-{|||three}-', '/\w+/'));
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
        Str::loop(
            '-abc-def',
            '/-(\w+)/',
            function ($match) use (&$matches) {
                $matches[] = $match;
            }
        );
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
            $this->assertArrayNotHasKey($uuid, $uuids, "UUID was generated before");
            $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid, "uuid v4 round $i");
            $uuids[$uuid] = $i;
        }
    }

    public function testSurround()
    {
        $this->assertSame('Hello <World>!', Str::surround('Hello {World}!', '<', '>'));
        $this->assertSame('Hello <{World>}!', Str::surround('Hello {\{World}\}!', '<', '>'));
        $this->assertSame('<Hello> <World>!', Str::surround('{Hello} {World}!', '<', '>'));
    }

    public function testEnbrace()
    {
        $this->assertSame('<Hello> [World]!', Str::enbrace('{1|Hello} {0|World}!', ['[%s]', '<%s>']));
        $this->assertSame('<b>Hello</b> <i>World</i>!', Str::enbrace('{b|Hello} {i|World}!', ['b' => '<b>%s</b>', 'i' => '<i>%s</i>']));
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

    public function testFoldable()
    {
        $this->assertTrue(Str::foldable('asdfghæßðđŋ'));
        $this->assertTrue(Str::foldable('ASDFGHÆÐĐŊĦ'));
        $this->assertFalse(Str::foldable('`1234567890-=[];#,./`!"£$%^&*()_+{}:@~<>?|'));
    }

    public function testMCF()
    {
        $this->assertSame(
            [
                'identifier' => '5',
                'algorithm'  => 'sha256',
                'salt'       => 'wnsT7Yr92oJoP28r',
                'hash'       => 'r6gESRx/RBya4a.LFKCFY.r4BT/onHS7Qg9BiSR58.5',
                'format'     => '$5$rounds=80000$',
                'params'     => [
                    'rounds' => '80000',
                ],
                'prefix'     => '$5$rounds=80000$wnsT7Yr92oJoP28r$',
            ],
            Str::parseMCF('$5$rounds=80000$wnsT7Yr92oJoP28r$r6gESRx/RBya4a.LFKCFY.r4BT/onHS7Qg9BiSR58.5')
        );
        $this->assertSame(
            [
                'algorithm' => 'hex',
                'bytes'     => 128 / 8,
            ],
            Str::parseMCF('d41d8cd98f00b204e9800998ecf8427e')
        );
        $this->assertSame(
            [
                'algorithm' => 'hex',
                'bytes'     => 160 / 8,
            ],
            Str::parseMCF('da39a3ee5e6b4b0d3255bfef95601890afd80709')
        );
        $this->assertSame(
            [
                'algorithm' => 'hex',
                'bytes'     => 224 / 8,
            ],
            Str::parseMCF('d14a028c2a3a2bc9476102bb288234c415a2b01f828ea62ac5b3e42f')
        );
        $this->assertSame(
            [
                'algorithm' => 'hex',
                'bytes'     => 256 / 8,
            ],
            Str::parseMCF('e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855')
        );
        $this->assertSame(
            [
                'algorithm' => 'hex',
                'bytes'     => 384 / 8,
            ],
            Str::parseMCF('38b060a751ac96384cd9327eb1b1e36a21fdb71114be07434c0cc7bf63f6e1da274edebfe76f65fbd51ad2f14898b95b')
        );
        $this->assertSame(
            [
                'algorithm' => 'hex',
                'bytes'     => 512 / 8,
            ],
            Str::parseMCF('cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e')
        );
    }

    public function testTr()
    {
        $str = 'aaabbbcccddd';
        $this->assertTrue(Str::tr($str, 'bd', 'ef'));
        $this->assertFalse(Str::tr($str, 'bd', 'ef'));
        $this->assertSame('aaaeeecccfff', $str);
    }

    public function testFtime()
    {
        $this->assertSame(null, Str::ftime(null, ''));
        $this->assertSame(null, Str::ftime(null, '', null));
        $this->assertSame('a', Str::ftime(null, '', 'a'));
        $this->assertSame('b', Str::ftime(new DateTimeImmutable(), '\b'));
        $this->assertSame('c', Str::ftime(new DateTimeImmutable(), '\c', 'BAD'));
        $this->assertSame('2020-03-02T00:00:00+00:00', Str::ftime(new DateTimeImmutable('2020-02-30T24:00:00Z'), 'c', 'BAD'));
    }

    public function testFcEq()
    {
        $this->assertTrue(Str::fceq('A', 'a'));
        $this->assertFalse(Str::fceq('a', 'B'));
        $this->assertTrue(Str::fceq('ç', 'Ç'));
    }

    public function testConvertable()
    {
        $to = 'ascii';
        for ($i = 0; $i < 0x80; $i++) {
            $this->assertTrue(Str::convertable(mb_chr($i), $to, 'utf8'), sprintf('Convert codepoint U+%04X to %s', $i, $to));
        }
        $i = 0x80;
        $this->assertFalse(Str::convertable(mb_chr($i), $to, 'utf8'), sprintf('Convert codepoint U+%04X to %s', $i, $to));

        $to = 'ISO-8859-1';
        for ($i = 0; $i < 0x100; $i++) {
            $this->assertTrue(Str::convertable(mb_chr($i), $to, 'utf8'), sprintf('Convert codepoint U+%04X to %s', $i, $to));
        }
        $i = 0x100;
        $this->assertFalse(Str::convertable(mb_chr($i), $to, 'utf8'), sprintf('Convert codepoint U+%04X to %s', $i, $to));
    }

    public function testStrip()
    {
        for ($i = 0; $i < 0x1000; $i++) {
            $c = mb_chr($i);
            Str::strip($c);
            $this->assertNotSame("X", "X$c", sprintf("Str::strip(U+%04X)", $i));
            $this->assertSame(mb_ord($c), $i, sprintf("Str::strip(U+%04X)", $i));
        }
        for ($i = 0x80; $i < 0x100; $i++) {
            $c = pack('C', $i);
            Str::strip($c);
            $this->assertSame("X", "X$c", sprintf("Str::strip(U+%04X)", $i));
        }
    }

    public function testTranslit()
    {
        $a = "æåëýþÿüïöœäðèéùúĳøàáçìíñµ";
        $b = Str::translit($a, 'ISO-8859-1');
        $b = mb_convert_encoding($b, 'utf8', 'ISO-8859-1');
        $this->assertSame('æåëýþÿüïöoeäðèéùúijøàáçìíñµ', $b);

        $a = "ÆÅËÝÞŸÜÏÖŒÄ§ÐÈÉÙÚĲØÀÁÇÌÍÑ";
        $b = Str::translit($a, 'ISO-8859-1');
        $b = mb_convert_encoding($b, 'utf8', 'ISO-8859-1');
        $this->assertSame('ÆÅËÝÞ?ÜÏÖOEÄ§ÐÈÉÙÚIJØÀÁÇÌÍÑ', $b);

        $a = "ÆÅËÝÞŸÜÏÖŒÄ§ÐÈÉÙÚĲØÀÁÇÌÍÑ";
        $b = Str::translit($a, 'ISO-8859-1', '');
        $b = mb_convert_encoding($b, 'utf8', 'ISO-8859-1');
        $this->assertSame('ÆÅËÝÞÜÏÖOEÄ§ÐÈÉÙÚIJØÀÁÇÌÍÑ', $b);

        $a = "æåëýþÿüïöœäðèéùúĳøàáçìíñµ";
        $b = Str::translit($a, 'ascii');
        $b = mb_convert_encoding($b, 'utf8', 'ascii');
        $this->assertSame('?????????oe??????ij????????', $b);

        $a = "æåëýþÿüïöœäðèéùúĳøàáçìíñµ";
        $b = Str::translit($a, 'ascii', '');
        $b = mb_convert_encoding($b, 'utf8', 'ascii');
        $this->assertSame('oeij', $b);

        $a = "æåëýþÿüïöœäðèéùúĳøàáçìíñµ";
        $b = Str::translit($a, 'ascii', 'X');
        $b = mb_convert_encoding($b, 'utf8', 'ascii');
        $this->assertSame('XXXXXXXXXoeXXXXXXijXXXXXXXX', $b);
    }
}
