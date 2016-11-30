<?php
/**
 * Santa's little helpers for string manipulation
 *
 * @copyright 2016 David Zurborg
 * @author    David Zurborg <post@david-zurb.org>
 * @license   https://opensource.org/licenses/ISC The ISC License
 */

namespace Pirate\Hooray;

/**
 * Str is a class containing a bunch of public static functions
 *
 * There is no object-orientated interface.
 */
class Str
{

    /**
     * Checks whether string is a string and returns its length
     *
     * @param mixed $string
     * @param mixed $nvl
     * @return int
     */
    public static function ok($string, $nvl = false)
    {
        return is_string($string) ? strlen($string) : $nvl;
    }

    /**
     * Split string into an array by its first character
     *
     * @param string $path
     * @param int $limit
     * @return string[]
     */
    public static function split(string $path, int $limit = PHP_INT_MAX)
    {
        if (!self::ok($path)) {
            return null;
        }
        $delim = substr($path, 0, 1);
        return explode($delim, substr($path, 1), $limit);
    }
}
