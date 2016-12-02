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

    /**
     * In-place PCRE replacement
     *
     * @param string $str in/out string
     * @param string $regexp regular expression
     * @param mixed $replacement string or something callable
     * @return void
     */
    public static function replace(string &$str, string $regexp, $replacement)
    {
        if (is_callable($replacement)) {
            $str = preg_replace_callback($regexp, $replacement, $str);
        } else {
            $str = preg_replace($regexp, $replacement, $str);
        }
        return;
    }

    /**
     * Pluralize formatted string
     *
     * ```php
     * Str::pluralize('{No|One|$} quer(y|ies) (is|are) found', 1); # 'One query is found'
     * ```
     *
     * Rule #1: `(pl)` expands when the amount is exactly one. Otherwise this expression is omitted
     * Rule #2: `{sl}` expands when the amount is not one. This is the opposite of rule #1.
     * Rule #3: `(one|two|three|four|all other)` expands to element in the list, whereas 0 expands to the last element.
     * Rule #4: `{zero|one|two|three|all other}` expands to element in the list, whereas 0 expands to the first element.
     * Rule #5: `$` expands to the numeric value of amount. This replacement can be changed with the 3rd parameter and defaults to the dollar sign.
     *
     * @param string $text Formatted string
     * @param int $amount
     * @param string $search
     * @return string
     */
    public static function pluralize(string $text, int $amount, string $search = '$')
    {
        self::replace(
            $text,
            '/ \( ( [^|]+? ) \) /x',
            function ($match) use ($amount) {
                return ($amount === 1 ? '' : $match[1]);
            }
        );
        self::replace(
            $text,
            '/ \{ ( [^|]+? ) \} /x',
            function ($match) use ($amount) {
                return ($amount === 1 ? $match[1] : '');
            }
        );
        self::replace(
            $text,
            '/ \( ( [^|]*? ( \| [^|]*? )+ ) \) /x',
            function ($match) use ($amount) {
                $parts = explode('|', $match[1]);
                $last = array_pop($parts);
                return ($amount === 0 ? $last : Arr::get($parts, $amount - 1, $last));
            }
        );
        self::replace(
            $text,
            '/ \{ ( [^|]*? ( \| [^|]*? )+ ) \} /x',
            function ($match) use ($amount) {
                $parts = explode('|', $match[1]);
                $last = array_pop($parts);
                return Arr::get($parts, $amount, $last);
            }
        );
        $text = str_replace($search, $amount, $text);
        return $text;
    }

    /**
     * @internal
     */
    const DURATION_VAL = [
        'C' => 3155760000,
        'D' => 315576000,
        'Y' => 31557600,
        'm' => 2629800,
        'w' => 604800,
        'd' => 86400,
        'H' => 3600,
        /// => ###,
        'M' => 60,
        'S' => 1,
        'f' => 1/1000,
    ];

    /**
     * Split seconds into precise chunks
     *
     * The resulting array consists of following indicies:
     * `C` - centuries
     * `D` - decades
     * `Y` - years
     * `m` - months
     * `w` - weeks
     * `d` - days
     * `H` - hours
     * `M` - minutes
     * `S` - seconds
     * `f` - milliseconds
     * `!` - rest as devident of `PHP_INT_MAX` devisor
     *
     * @param float $seconds
     * @return int[]
     */
    public static function timechunks(float $seconds)
    {
        if ($seconds < 0) {
            $seconds = 0 - $seconds;
        }

        $times = [];

        foreach (self::DURATION_VAL as $key => $value) {
            $amount = (int) floor($seconds / $value);
            $times[$key] = $amount;
            $seconds -= $amount * $value;
        }

        $times['!'] = $seconds * PHP_INT_MAX;

        return $times;
    }
}
