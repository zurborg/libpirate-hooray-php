<?php
/**
 * Santa's little helpers for array accessing and manipulation
 *
 * @copyright 2017 David Zurborg
 * @author    David Zurborg <post@david-zurb.org>
 * @license   https://opensource.org/licenses/ISC The ISC License
 */

namespace Pirate\Hooray;

use OutOfBoundsException;
use Throwable;

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
     * @see is_array()
     * @param mixed $array
     * @param mixed $nvl
     * @return int|false
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
     * @return int|null
     */
    public static function index(array $array, int $index)
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
    public static function get(array $array, string $key, $default = null)
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * Wrapper for `array_key_exists()`
     *
     * @see array_key_exists()
     * @param array $array
     * @param string $key
     * @return boolean
     */
    public static function has(array $array, string $key)
    {
        return array_key_exists($key, $array) ? true : false;
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
     * @see Arr::get()
     * @param array $array
     * @param int $index
     * @param mixed $default
     * @return mixed
     */
    public static function getIndex(array $array, int $index, $default = null)
    {
        return self::get($array, self::index($array, $index), $default);
    }

    /**
     * Assume the key exists and return its value. Otherwise throw an out-of-bounds exception.
     *
     * @param array $array
     * @param string $key
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @throws OutOfBoundsException
     * @return mixed
     */
    public static function load(array $array, string $key, string $message, int $code = 0, Throwable $previous = null)
    {
        if (!array_key_exists($key, $array)) {
            throw new OutOfBoundsException($message, $code, $previous);
        }
        return $array[$key];
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
    public static function init(array &$array, string $key, $value = null)
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
    public static function consume(array &$array, string $key, $default = null)
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
     * Arr::assert($A, 'bar', 'this does not exists'); // throw new OutOfBoundsException('this does not exists')
     * $e = new Exception('...');  // just instanciating, not throwing
     * Arr::assert($A, 'bar', $e); // throw $e
     * Arr::assert($A, 'bar', function ($key) { return "bad: $key"; }); // retruns 'bad: bar'
     * ```
     *
     * @param array $array
     * @param string $key
     * @param mixed $throw
     * @throws OutOfBoundsException|Throwable
     * @return mixed
     */
    public static function assert(array $array, string $key, $throw)
    {
        if (array_key_exists($key, $array)) {
            return null;
        } elseif ($throw instanceof Throwable) {
            throw $throw;
        } elseif (!is_scalar($throw) && is_callable($throw)) {
            return $throw($key);
        } else {
            throw new OutOfBoundsException($throw);
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
     * @see in_array()
     * @param array $haystack
     * @param mixed $needle
     * @param bool $strict
     * @return bool
     */
    public static function in(array $haystack, $needle, bool $strict = true)
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
     * @see Arr::get()
     * @param array $array
     * @param string $key
     * @param mixed $expect
     * @param bool $strict
     * @return bool
     */
    public static function is(array $array, string $key, $expect, bool $strict = true)
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
    public static function any(array $array, array $keys, bool $strict = true)
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
    public static function all(array $array, array $keys, bool $strict = true)
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
        return is_array($array) && !self::assoc($array) ? $array : [$array];
    }

    /**
     * Set a new value in an array and return old value
     *
     * @param $array
     * @param $key
     * @param $new_value
     * @return mixed
     */
    public static function set(&$array, $key, $new_value)
    {
        $old_value = array_key_exists($key, $array) ? $array[$key] : null;
        $array[$key] = $new_value;
        return $old_value;
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
     * @see Arr::get()
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
     * @see Arr::getDeep()
     * @param array $array
     * @param string[] $keys
     * @param mixed $expect
     * @param bool $strict
     * @return bool
     */
    public static function isDeep(array $array, array $keys, $expect, bool $strict = true)
    {
        $value = self::getDeep($array, $keys);
        return $strict ? $value === $expect : $value == $expect;
    }

    /**
     * Set a deep value and return old value
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
     * @see Arr::set()
     * @param array &$array
     * @param string[] $keys
     * @param mixed $value
     * @return mixed old vlaue
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
        $old = array_key_exists($last, $array) ? $array[$last] : null;
        $array[$last] = $value;
        return $old;
    }

    /**
     * Unset a deep value and return old value
     *
     * ```php
     * $A = [
     *     'foo' => [
     *         'bar' => 123
     *     ]
     * ];
     * Arr::unsetDeep($A, [ 'foo', 'bar' ]); // $A['foo'] is now empty ([])
     * Arr::unsetDeep($A, [ 'foo' ]); // $A is now empty ([])
     * ```
     *
     * @see Arr::consume()
     * @param array &$array
     * @param string[] $keys
     * @return mixed old value
     */
    public static function unsetDeep(array &$array, array $keys)
    {
        $last = array_pop($keys);
        foreach ($keys as $key) {
            if (!is_array(self::get($array, $key))) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        if (array_key_exists($last, $array)) {
            $value = $array[$last];
            unset($array[$last]);
            return $value;
        } else {
            return null;
        }
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
     * @see Arr::getDeep()
     * @see Str::split()
     * @param array $array
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public static function getPath(array $array, string $path, $default = null)
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
     * @see Arr::getPath()
     * @param array $array
     * @param string $path
     * @param mixed $expect
     * @param bool $strict
     * @return bool
     */
    public static function isPath(array $array, string $path, $expect, bool $strict = true)
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
     * @see Arr::setDeep()
     * @see Str::split()
     * @param array &$array
     * @param string $path
     * @param mixed $value
     * @return mixed old value
     */
    public static function setPath(array &$array, string $path, $value)
    {
        return self::setDeep($array, Str::split($path), $value);
    }

    /**
     * Path variant of unsetDeep()
     *
     * ```php
     * $A = [
     *     'foo' => [
     *         'bar' => 123
     *     ]
     * ];
     * Arr::unsetPath($A, '/foo/bar'); // $A['foo'] is now empty ([])
     * ```
     *
     * @see Arr::unsetDeep()
     * @see Str::split()
     * @param array &$array
     * @param string $path
     * @return mixed old value
     */
    public static function unsetPath(array &$array, string $path)
    {
        return self::unsetDeep($array, Str::split($path));
    }

################################################################################

    /**
     * In-place recursive merge of another array
     *
     * ```php
     * $A = [
     *    'foo' => 123,
     * ];
     * Arr::merge($A, [ 'foo' => 456 ]);
     * ```
     *
     * @see array_merge()
     * @param array &$array1
     * @param array $array2
     * @return void
     */
    public static function merge(array &$array1, array $array2)
    {
        $array1 = array_merge($array1, $array2);
        return;
    }

    /**
     * Set default values in an array
     *
     * ```php
     * $A = [
     *    'foo' => 123,
     * ];
     * Arr::defaults($A, [ 'bar' => 123, 'foo' => 456 ]);
     * // $A now contains [ 'bar' => 123, 'foo' => 123 ]
     * ```
     *
     * @see array_merge()
     * @param array &$array
     * @param array $defaults
     * @return void
     */
    public static function defaults(array &$array, array $defaults)
    {
        $array = array_merge($defaults, $array);
        return;
    }

    /**
     * Remove first item of array and return it
     *
     * @see array_shift()
     * @param array &$array
     * @param mixed $default Default value if array is empty
     * @return mixed
     */
    public static function shift(array &$array, $default = null)
    {
        return count($array) ? array_shift($array) : $default;
    }

    /**
     * Remove last item of array and return it
     *
     * @see array_pop()
     * @param array &$array
     * @param mixed $default Default value if array is empty
     * @return mixed
     */
    public static function pop(array &$array, $default = null)
    {
        return count($array) ? array_pop($array) : $default;
    }

    /**
     * Reverse the key-order of an array
     *
     * @see array_reverse()
     * @param array &$array
     * @return void
     */
    public static function reverse(array &$array)
    {
        $array = array_reverse($array);
        return;
    }
}
