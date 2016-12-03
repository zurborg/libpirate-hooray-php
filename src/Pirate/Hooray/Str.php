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
     * ```php
     * Str::ok(''); // 0
     * Str::ok('Hello, World!'); // 13
     * Str::ok(null); // false
     * Str::ok(false); // false
     * Str::ok(true); // false
     * Str::ok([]); // false
     * ```
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
     * ```php
     * Str::split('/foo/bar'); // ['foo', 'bar']
     * Str::split('.foo.bar'); // ['foo', 'bar']
     * Str::split('#foo#bar'); // ['foo', 'bar']
     * Str::split('/foo.bar/bar#foo'); // ['foo.bar', 'bar#foo']
     * Str::split('.foo#bar.bar/foo'); // ['foo#bar', 'bar/foo']
     * Str::split('#foo/bar#bar.foo'); // ['foo/bar', 'bar.foo']
     * Str::split(''); // null
     * ```
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
     * Str::pluralize('{No|One|$} quer(y|ies) (is|are) found', 1); // 'One query is found'
     * ```
     *
     * + Rule #1: `(pl)` expands when the amount is exactly one. Otherwise this expression is omitted
     * + Rule #2: `{sl}` expands when the amount is not one. This is the opposite of rule #1.
     * + Rule #3: `(one|two|three|four|all other)` expands to element in the list, whereas 0 expands to the last element.
     * + Rule #4: `{zero|one|two|three|all other}` expands to element in the list, whereas 0 expands to the first element.
     * + Rule #5: `$` expands to the numeric value of amount. This replacement can be changed with the 3rd parameter and defaults to the dollar sign.
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
     * + `C` - centuries
     * + `D` - decades
     * + `Y` - years
     * + `m` - months
     * + `w` - weeks
     * + `d` - days
     * + `H` - hours
     * + `M` - minutes
     * + `S` - seconds
     * + `f` - milliseconds
     * + `!` - rest as devident of `PHP_INT_MAX` devisor
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

    /**
     * @internal
     */
    const DURATION_L10N = [
        'en' => [
            'C' => '(one|$) centur(y|ies)',
            'D' => '(one|$) decade(s)',
            'Y' => '(one|$) year(s)',
            'm' => '(one|$) month(s)',
            'w' => '(one|$) week(s)',
            'd' => '(one|$) day(s)',
            'H' => '(one|$) hour(s)',
            'M' => '(one|$) minute(s)',
            'S' => '(one|$) second(s)',
            'f' => '(one|$) millisecond(s)',
            '&' => ' and ',
            ',' => ', ',
        ],
        'de' => [
            'C' => '(ein|$) Jahrhundert(e)',
            'D' => '(eine|$) Dekade(n)',
            'Y' => '(ein|$) Jahr(e)',
            'm' => '(ein|$) Monat(e)',
            'w' => '(eine|$) Woche(n)',
            'd' => '(ein|$) Tag(e)',
            'H' => '(eine|$) Stunde(n)',
            'M' => '(eine|$) Minute(n)',
            'S' => '(eine|$) Sekunde(n)',
            'f' => '(eine|$) Millisekunde(n)',
            '&' => ' und ',
            ',' => ', ',
        ],
    ];

    /**
     * Pretty print seconds in human-readable format
     *
     * ```php
     * Str::duration(3666, 2); // 'one hour and one minute'
     * Str::duration(3666, 3); // 'one hour, one minute and 6 seconds'
     * ```
     *
     * Currently available locals: `en` and `de`.
     *
     * @param float $seconds
     * @param int $precision
     * @param string $locale
     * @return string
     */
    public static function duration(float $seconds, int $precision = 2, string $locale = null)
    {
        if (!$seconds) {
            return '';
        }

        if (is_null($locale)) {
            $locale = \Locale::getDefault();
        }

        $texts_l10n = self::DURATION_L10N;
        $languages = array_keys($texts_l10n);
        $locale  = \Locale::lookup($languages, $locale);
        $locales = Arr::get($texts_l10n, $locale, []);

        $strings = [];

        $times = self::timechunks($seconds);

        $precision--;
        foreach ($times as $key => $amount) {
            $string = Arr::get($locales, $key);
            if ($string and $amount) {
                $strings[] = self::pluralize($string, $amount);
            }
            if (count($strings) > 0) {
                if ($precision > 0) {
                    $precision--;
                } else {
                    break;
                }
            }
        }

        if (!Arr::ok($strings)) {
            return '';
        }

        $separator = Arr::get($locales, ',', ',');

        $last = array_pop($strings);
        if (Arr::ok($strings)) {
            $and = Arr::get($locales, '&', $separator);
            return implode($separator, $strings) . $and. $last;
        } else {
            return $last;
        }
    }

    /**
     * Generates a pseudo random salt for blowfish password encryption
     *
     * ```php
     * $salt = Str::salt2y(10); // 10 rounds
     * $password = crypt('testtest', $salt);
     * ```
     *
     * @param int $rounds
     * @return string
     */
    public static function salt2y(int $rounds = 4)
    {
        $bytes = 20;
        $false = false;
        $rand = substr(base64_encode(openssl_random_pseudo_bytes($bytes, $false)), 2, 22);
        $rand = str_replace('+', '.', $rand);
        return sprintf('$2y$%02d$%22s', $rounds, $rand);
    }
}
