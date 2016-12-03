<?php
/**
 * Santa's little helpers for array accessing and manipulation
 *
 * @copyright 2016 David Zurborg
 * @author    David Zurborg <post@david-zurb.org>
 * @license   https://opensource.org/licenses/ISC The ISC License
 */

namespace Pirate\Hooray;

use \Pirate\Hooray\Str;

/**
 * Arr is a class containing a bunch of public static functions
 *
 * There is no object-orientated interface.
 */
class Arr
{

    /**
     * Checks whether $array is an array and returns the actual size
     *
     * ```php
     * Arr::ok([]);             // returns 0
     * Arr::ok(['foo','bar']);  // returns 2
     * Arr::ok('foobar');       // returns false
     * ```
     *
     * @param mixed $array
     * @param mixed $nvl
     * @return int
     */
    public static function ok($array, $nvl = false)
    {
        return is_array($array) ? count($array) : $nvl;
    }

    /**
     * Normalize index of an array
     *
     * If index is below 0 or greater than the size of the array, the index value will be reduced/expanded.
     * An index value of -1 results to the last element of the the array
     *
     * ```php
     * Arr::index([1,2,3], -1);  // returns 2
     * Arr::index([1,2,3], -11); // returns 1
     * Arr::index([1,2,3], 9);   // returns 0
     * ```
     *
     * @param array $array
     * @param int $index
     * @return int
     */
    public static function index(array $array, $index)
    {
        $n = count($array);
        if (!$n) {
            return null;
        }
        while ($index < 0) {
            $index += $n;
        }
        while ($index >= $n) {
            $index -= $n;
        }
        return $index;
    }

    /**
     * Get the value of an array by its key or return a default value
     *
     * When the key does not exists, a default value will be returned
     *
     * ```php
     * $A = [
     *     'foo' => 123,
     *     'bar' => 456
     * ];
     * Arr::get($A, 'foo');          // returns 123
     * Arr::get($A, 'bla');          // returns null, no error!
     * Arr::get($A, 'bla', 'blubb'); // returns 'blubb'
     * ```
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * Get the value of an array by its index or return a default value
     *
     * ```php
     * $A = [ 'foo', 'bar' ];
     * Arr::getIndex($A, -1); // returns 'bar'
     * ```
     *
     * If the array is an associative array, (or there is no numeric key in the range of 0 .. n-1) the default value will be returned,
     *
     * @param array $array
     * @param int $index
     * @param mixed $default
     * @return mixed
     */
    public static function getIndex(array $array, $index, $default = null)
    {
        return self::get($array, self::index($array, $index), $default);
    }

    /**
     * Set an array element if the key does not exists already
     * Returns the actual value of the element
     *
     * ```php
     * $A = [
     *     'foo' => 123
     * ];
     * Arr::init($A, 'foo', 234); // does not override 'foo', just returns 123
     * Arr::init($A, 'bar', 456); // sets 'bar', returns 456
     * ```
     *
     * @param array &$array
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function init(array &$array, $key, $value = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            $array[$key] = $value;
            return $value;
        }
    }

    /**
     * Delete an array element and return its value
     *
     * ```php
     * $A = [
     *     'foo' => 123
     * ];
     * Arr::consume($A, 'foo'); // unsets $A['foo'] and returns 123
     * // hint: $A is empty now
     * Arr::consume($A, 'bar', 456); // does nothing, returns 456
     * ```
     *
     * @param array &$array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function consume(array &$array, $key, $default = null)
    {
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            unset($array[$key]);
            return $value;
        } else {
            return $default;
        }
    }

    /**
     * Check whether an array key exists or throw an exception
     *
     * ```php
     * $A = [
     *     'foo' => 123
     * ];
     * Arr::assert($A, 'foo', 'meh'); // nothing happens
     * Arr::assert($A, 'bar', 'this does not exists'); // throw new DomainException('this does not exists')
     * $e = new Exception('...');  // just instanciating, not throwing
     * Arr::assert($A, 'bar', $e); // throw $e
     * Arr::assert($A, 'bar', function ($key) { return "bad: $key"; }); // retruns 'bad: bar'
     * ```
     *
     * @param array $array
     * @param string $key
     * @param mixed $throw
     * @throws \Throwable
     * @return mixed
     */
    public static function assert(array $array, $key, $throw)
    {
        if (array_key_exists($key, $array)) {
            return;
        } elseif ($throw instanceof \Throwable) {
            throw $throw;
        } elseif (is_callable($throw)) {
            return $throw($key);
        } else {
            throw new \DomainException($throw);
        }
    }

    /**
     * Wrapper for in_array but with strict comparision by default
     *
     * ```php
     * $A = [ '12', 34 ];
     * Arr::in($A,  12 ); // false
     * Arr::in($A, '12'); // true
     * Arr::in($A,  34 ); // true
     * Arr::in($A, '34'); // false
     * ```
     *
     * @param array $haystack
     * @param mixed $needle
     * @param bool $strict
     * @return bool
     */
    public static function in(array $haystack, $needle, $strict = true)
    {
        return in_array($needle, $haystack, $strict);
    }

    /**
     * Strict comparision of an array value
     *
     * ```php
     * $A = [
     *     'foo' =>  123,
     *     'bar' => '456'
     * ];
     * Arr::is($A, 'foo', 123);   // returns true
     * Arr::is($A, 'bar', 456);   // returns false
     * Arr::is($A, 'xxx', 'yyy'); // returns false
     * Arr::is($A, 'yyy', null);  // returns true, since Arr::get returns null as default value if key does not exists
     * ```
     *
     * @param array $array
     * @param string $key
     * @param mixed $expect
     * @param bool $strict
     * @return bool
     */
    public static function is(array $array, $key, $expect, $strict = true)
    {
        $value = self::get($array, $key);
        return $strict ? $value === $expect : $value == $expect;
    }

    /**
     * Checks whether any element exists in an array
     *
     * ```php
     * $A = [ 'aaa', 'bbb' ];
     * Arr::any($A, [ 'aaa', 'ccc' ]); // one match, returns true
     * Arr::any($A, [ 'ccc', 'ddd' ]); // no match, returns false
     * Arr::any($A, []); // no keys, returns null
     * ```
     *
     * @param array $array
     * @param string[] $keys
     * @param bool $strict
     * @return bool
     */
    public static function any(array $array, array $keys, $strict = true)
    {
        if (!count($keys)) {
            return null;
        }
        foreach ($keys as $key) {
            if (in_array($key, $array, $strict)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks whether all elements exists in an array
     *
     * ```php
     * $A = [ 'aaa', 'bbb', 'ccc' ];
     * Arr::all($A, [ 'ccc', 'ddd' ]); // one does not match, returns false
     * Arr::all($A, [ 'ccc', 'aaa' ]); // all matches, returns true
     * Arr::all($A, []); // no keys, returns null
     * ```
     *
     * @param array $array
     * @param string[] $keys
     * @param bool $strict
     * @return bool
     */
    public static function all(array $array, array $keys, $strict = true)
    {
        if (!count($keys)) {
            return null;
        }
        if (!count($array)) {
            return false;
        }
        foreach ($keys as $key) {
            if (!in_array($key, $array, $strict)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks whether an array is an associative array
     *
     * ```php
     * $A = [ 4, 9, 1 ];
     * $B = [ 'foo' => 'bar' ];
     * Arr::assoc($A); // returns false
     * Arr::assoc($B); // returns true
     * Arr::assoc([]); // returns null
     * ```
     *
     * @param array $array
     * @return bool
     */
    public static function assoc(array $array)
    {
        if (!count($array)) {
            return null;
        }
        foreach (array_keys($array) as $key => $value) {
            if ($key !== $value) {
                return true;
            }
        }
        return false;
    }

    /**
     * Force an array to be an non-associative array
     *
     * ```php
     * Arr::flist('foo'); // returns [ 'foo' ]
     * Arr::flist([ 'foo' ,  'bar' ]); // returns   [ 'foo' ,  'bar' ]
     * Arr::flist([ 'foo' => 'bar' ]); // returns [ [ 'foo' => 'bar' ] ]
     * Arr::flist(null); // returns []
     * Arr::flist(null, false); // returns false
     * ```
     *
     * @param mixed $array
     * @param mixed $nvl
     * @return array
     */
    public static function flist($array, $nvl = [])
    {
        if (is_null($array)) {
            return $nvl;
        }
        return is_array($array) && !self::assoc($array) ? $array : [ $array ];
    }

################################################################################

    /**
     * Get a deep value of an array of array(s) or return default value
     *
     * ```php
     * $A = [
     *     'foo' => [
     *         'bar' => 123
     *     ]
     * ];
     * Arr::getDeep($A, [ 'foo', 'bar' ]);      // returns 123
     * Arr::getDeep($A, [ 'bar', 'foo' ], 456); // returns 456
     * ```
     *
     * @param array $array
     * @param string[] $keys
     * @param mixed $default
     * @return mixed
     */
    public static function getDeep(array $array, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (is_array($array)) {
                $array = self::get($array, $key, $default);
            } else {
                return $default;
            }
        }
        return $array;
    }

    /**
     * Deep variant of is()
     *
     * ```php
     * $A = [
     *     'foo' => [
     *         'bar' => 123
     *     ]
     * ];
     * Arr::isDeep($A, [ 'foo', 'bar' ], 123); // returns true
     * ```
     *
     * @param array $array
     * @param string[] $keys
     * @param mixed $expect
     * @param bool $strict
     * @return bool
     */
    public static function isDeep(array $array, array $keys, $expect, $strict = true)
    {
        $value = self::getDeep($array, $keys);
        return $strict ? $value === $expect : $value == $expect;
    }

    /**
     * Set a deep value
     *
     * ```php
     * $A = [
     *     'foo' => [
     *         'bar' => 123
     *     ]
     * ];
     * Arr::setDeep($A, [ 'foo', 'bar' ], 456); // $A['foo']['bar'] is now 456
     * Arr::setDeep($A, [ 'foo' ], 789); // $A['foo'] is now 789
     * ```
     *
     * @param array &$array
     * @param string[] $keys
     * @param mixed $value
     * @return void
     */
    public static function setDeep(array &$array, array $keys, $value)
    {
        $last = array_pop($keys);
        foreach ($keys as $key) {
            if (!is_array(self::get($array, $key))) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[$last] = $value;
        return;
    }

################################################################################

    /**
     * Get array element by path
     *
     * ```php
     * $A = [
     *     'foo' => [
     *         'bar' => 123
     *     ]
     * ];
     * Arr::getPath($A, '/foo/bar'); // retuns 123
     * ```
     *
     * See also `\Pirate\Hooray\Str::split()`
     *
     * @param array $array
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public static function getPath(array $array, $path, $default = null)
    {
        return self::getDeep($array, Str::split($path), $default);
    }

    /**
     * Path variant of isDeep()
     *
     * ```php
     * $A = [
     *     'foo' => [
     *         'bar' => 123
     *     ]
     * ];
     * Arr::isPath($A, '/foo/bar', 123); // returns true
     * ```
     *
     * @param array $array
     * @param string $path
     * @param mixed $expect
     * @param bool $strict
     * @return bool
     */
    public static function isPath(array $array, $path, $expect, $strict = true)
    {
        $value = self::getPath($array, $path);
        return $strict ? $value === $expect : $value == $expect;
    }

    /**
     * Path variant of setDeep()
     *
     * ```php
     * $A = [
     *     'foo' => [
     *         'bar' => 123
     *     ]
     * ];
     * Arr::setPath($A, '/foo/bar', 456); // retuns $A['foo']['bar'] is now 456
     * ```
     *
     * @param array &$array
     * @param string $path
     * @param mixed $value
     * @return void
     */
    public static function setPath(array &$array, $path, $value)
    {
        self::setDeep($array, Str::split($path), $value);
        return;
    }
}
