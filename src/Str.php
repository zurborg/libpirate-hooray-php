<?php
/**
 * Santa's little helpers for string manipulation
 *
 * @copyright 2017 David Zurborg
 * @author    David Zurborg <post@david-zurb.org>
 * @license   https://opensource.org/licenses/ISC The ISC License
 */

namespace Pirate\Hooray;

use DateTimeInterface;
use InvalidArgumentException;
use Locale;
use RuntimeException;

/**
 * Str is a class containing a bunch of public static functions
 *
 * There is no object-orientated interface.
 */
class Str
{

    /**
     * Checks whether string is a scalar and not a bool and returns its length
     *
     * ```php
     * Str::ok(''); // 0
     * Str::ok('Hello, World!'); // 13
     * Str::ok(null); // false
     * Str::ok(false); // false
     * Str::ok(true); // false
     * Str::ok(123); // 3
     * Str::ok([]); // false
     * ```
     *
     * @param mixed $string
     * @param mixed $nvl
     * @return int|false|mixed
     */
    public static function ok($string, $nvl = false)
    {
        return (is_scalar($string) and !is_bool($string)) ? mb_strlen($string) : $nvl;
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
     * @return string[]|null
     */
    public static function split(string $path, int $limit = PHP_INT_MAX): ?array
    {
        if (!self::ok($path)) {
            return null;
        }
        $delim = mb_substr($path, 0, 1);
        return explode($delim, mb_substr($path, 1), $limit);
    }

    /**
     * @param string $regexp
     * @param bool $fence
     * @param string $modifiers
     * @return string
     */
    public static function regexp(string $regexp, bool $fence = false, string $modifiers = ''): string
    {
        $delim = chr(1);
        $prefix = $fence ? '^' : '';
        $suffix = $fence ? '$' : '';
        return $delim . $prefix . $regexp . $suffix . $delim . $modifiers;
    }

    /**
     * Apply regular expression and return matching results
     *
     * ```php
     * if (Str::match('Hello!', '/ll/')) {
     *     ...;
     * }
     *
     * if ($match = Str::match('Hello!', '/H(a|e)llo/')) {
     *     ...;
     * }
     * ```
     *
     * @param string $subject
     * @param string $regexp regular expression
     * @param int $offset string offset
     * @return string[]|null
     */
    public static function match(string $subject, string $regexp, int $offset = 0): ?array
    {
        return preg_match($regexp, $subject, $match, 0, $offset) ? $match : null;
    }

    /**
     * Apply regular expression and return all matching results
     *
     * ```php
     * if (Str::matchall('Hello, World!', '/\w+/')) {
     *     ...;
     * }
     * ```
     *
     * @param string $subject
     * @param string $regexp regular expression
     * @param int $offset string offset
     * @return string[][]|null
     */
    public static function matchall(string $subject, string $regexp, int $offset = 0): ?array
    {
        return preg_match_all($regexp, $subject, $match, 0, $offset) ? $match : null;
    }

    /**
     * Apply full-match regular expression and return results
     *
     * ```php
     * if (Str::fullmatch('Hello!', '[A-Z][elo]{4}!')) {
     *     // regex is actually something like /^[A-Z][elo]{4}!$/
     *     ...;
     * }
     * ```
     *
     * @param string $subject
     * @param string $regexp regular expression
     * @param string $modifiers optional modifiers
     * @return string[]|null
     */
    public static function fullmatch(string $subject, string $regexp, string $modifiers = ''): ?array
    {
        $regexp = self::regexp($regexp, true, $modifiers);
        return preg_match($regexp, $subject, $match, 0, 0) ? $match : null;
    }

    /**
     * In-place PCRE replacement
     *
     * @param string $subject in/out string
     * @param string $regexp regular expression
     * @param mixed $replacement string or something callable
     * @param int $limit
     * @return void
     */
    public static function replace(string &$subject, string $regexp, $replacement, int $limit = -1): void
    {
        if (!is_scalar($replacement) && is_callable($replacement)) {
            $subject = preg_replace_callback($regexp, $replacement, $subject, $limit);
        } else {
            $subject = preg_replace($regexp, $replacement, $subject, $limit);
        }
    }

    /**
     * In-place PCRE replacement with empty string
     *
     * @param string $subject in/out string
     * @param string $regexp regular expression
     * @param int $limit
     * @return void
     */
    public static function remove(string &$subject, string $regexp, int $limit = -1): void
    {
        self::replace($subject, $regexp, '', $limit);
    }

    /**
     * Offset-based iteration of a string match by regular expression
     *
     * ```php
     * Str::loop("-abc-def", "/-(\w+)/", function ($match) {
     *     print_r($match);
     * });
     * ```
     *
     * @param string $subject
     * @param string $regexp
     * @param callable $function
     * @return void
     */
    public static function loop(string $subject, string $regexp, callable $function): void
    {
        $offset = 0;
        while ($match = self::match($subject, $regexp, $offset)) {
            $offset += mb_strlen($match[0]);
            $function($match);
        }
    }

    /**
     * Pluralize formatted string
     *
     * ```php
     * Str::pluralize('{No|One|$} quer(y|ies) (is|are) found', 1); // 'One query is found'
     * ```
     *
     * + Rule #1: `(pl)` expands when the amount is not one. Otherwise this expression is omitted
     * + Rule #2: `{sl}` expands when the amount is exactly one. This is the opposite of rule #1.
     * + Rule #3: `(one|two|three|four|all other)` expands to element in the list, whereas 0 expands to the last element.
     * + Rule #4: `{zero|one|two|three|all other}` expands to element in the list, whereas 0 expands to the first element.
     * + Rule #5: `$` expands to the numeric value of amount. This replacement can be changed with the 3rd parameter and defaults to the dollar sign.
     *
     * @param string $text Formatted string
     * @param int $amount
     * @param string $search
     * @return string
     */
    public static function pluralize(string $text, int $amount, string $search = '$'): string
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
        return str_replace($search, $amount, $text);
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
        'f' => 1 / 1000,
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
    public static function timechunks(float $seconds): array
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
     * @param ?string $locale
     * @return string
     */
    public static function duration(float $seconds, int $precision = 2, string $locale = null): string
    {
        if (!$seconds) {
            return '';
        }

        if (is_null($locale)) {
            $locale = Locale::getDefault();
        }

        $texts_l10n = self::DURATION_L10N;
        $languages = array_keys($texts_l10n);
        $locale = Locale::lookup($languages, $locale);
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
            return implode($separator, $strings) . $and . $last;
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
     * @param bool $use_strong
     * @return string
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function salt2y(int $rounds = 4, bool $use_strong = false): string
    {
        $bytes = 16;
        $was_strong = false;
        $rand = base64_encode(openssl_random_pseudo_bytes($bytes, $was_strong));
        if ($use_strong && !$was_strong) {
            throw new RuntimeException("Insufficient cryptographically strong random data");
        }
        $rand = str_replace('+', '.', substr($rand, 0, 22));
        return sprintf('$2y$%02d$%22s', $rounds, $rand);
    }

    /**
     * Parses a password in the _modular crypt format_ and returns some information about it
     *
     * ```php
     * $info = parseMCF('$5$rounds=80000$wnsT7Yr92oJoP28r$r6gESRx/RBya4a.LFKCFY.r4BT/onHS7Qg9BiSR58.5');
     * $info = [
     *     'identifier' => 5,
     *     'algorithm' => 'sha256',
     *     'salt' => 'wnsT7Yr92oJoP28r',
     *     'hash' => 'r6gESRx/RBya4a.LFKCFY.r4BT/onHS7Qg9BiSR58.5',
     *     'format' => '$5$rounds=80000$',
     *     'prefix' => '$5$rounds=80000$wnsT7Yr92oJoP28r$',
     *     'params' => [
     *         'rounds' => 80000
     *     ]
     * ];
     * ```
     *
     * The following keyswords are recognized:
     *
     * + `identifier` - Bare identifier of the crypted string, this is the part between the first two dollar signs
     * + `algorithm` - Short name of the algorithm, derived from the identifier
     * + `salt` - Salt of the hash
     * + `hash` - The bare hashed password itself
     * + `format` - Identifier plus params or just everything without crypted parts (salt and hash)
     * + `prefifx` - Identifier plus params plus salt, or just everything without the bare hash
     * + `params` - A key-value based array with all parameters found in the string
     *
     * Currently, only format 2 (plus all sub-formats), 5 and 6 are supported. More to come.
     * @param string $password
     * @return array
     */
    public static function parseMCF(string $password): array
    {
        if ($match = self::fullmatch(
            $password,
            '
            \$
            (?<identifier> [^\$]+ )

            (?:
                \$
                (?<params>
                    [^=,\$]+
                    =
                    [^=,\$]+
                    (?:
                        ,
                        [^=,\$]+
                        =
                        [^=,\$]+
                    )*
                )
            )?
            (?:
                \$
                (?<salt> [^\$]+ )
            )?
            \$
            (?<hash> [^\$]+ )
        ',
            'x'
        )) {
            $id = Arr::consume($match, 'identifier');
            $salt = Arr::consume($match, 'salt');
            $hash = Arr::consume($match, 'hash');
            $prefix = null;
            $paramstr = null;
            $format = null;
            $params = [];
            if (Arr::get($match, 'params')) {
                $paramstr = Arr::consume($match, 'params');
                self::loop(
                    ",$paramstr",
                    '/,(?<key>[^=,\$]+)=(?<val>[^=,\$]+)/',
                    function ($pair) use (&$params) {
                        $params[$pair['key']] = $pair['val'];
                    }
                );
            }
            $algo = 'unknown';
            if (substr($id, 0, 1) === '2') {
                $algo = 'bcrypt';
                $params['rounds'] = intval($salt);
                $salt = substr($hash, 0, 22);
                $hash = substr($hash, 22);
                $format = sprintf('$%s$%02d$', $id, $params['rounds']);
                $prefix = $format . $salt;
            } elseif ($id === '5') {
                $algo = 'sha256';
                $format = '$5';
                if (!is_null($paramstr)) {
                    $format .= '$' . $paramstr . '$';
                }
                $prefix = $format . $salt . '$';
            } elseif ($id === '6') {
                $algo = 'sha512';
                $format = '$6';
                if (!is_null($paramstr)) {
                    $format .= '$' . $paramstr . '$';
                }
                $prefix = $format . $salt . '$';
            } else {
                $prefix = $password;
            }
            return [
                'identifier' => $id,
                'algorithm'  => $algo,
                'salt'       => $salt,
                'hash'       => $hash,
                'format'     => $format,
                'params'     => $params,
                'prefix'     => $prefix,
            ];
        } elseif (self::fullmatch($password, '(?:[0-9a-f]{2})+', 'i')) {
            $bytes = strlen($password) / 2;
            return [
                'algorithm' => 'hex',
                'bytes'     => intval($bytes),
            ];
        } else {
            throw new InvalidArgumentException("Unrecognized password format");
        }
    }

    /**
     * Generate pseudo-random V4 universal unique identifier
     *
     * @param bool $binary return binary representation instead of string representation
     * @return string
     */
    public static function uuidV4(bool $binary = false): string
    {
        $len = 16;
        $bin = openssl_random_pseudo_bytes($len);
        $bin &= hex2bin('ffffffff' . 'ffff' . '0fff' . 'bfff' . 'ffffffffffff');
        $bin |= hex2bin('00000000' . '0000' . '4000' . '8000' . '000000000000');
        if ($binary) {
            return $bin;
        }
        $hex = bin2hex($bin);
        $uuid = [];
        $i = 0;
        foreach ([8, 4, 4, 4, 12] as $l) {
            $uuid[] = substr($hex, $i, $l);
            $i += $l;
        }
        return implode('-', $uuid);
    }

    /**
     * In-place str-to-upper, unicode-aware
     *
     * @param string &$subject
     * @return bool true if subject is changed
     */
    public static function upper(string &$subject): bool
    {
        $newsubject = mb_convert_case($subject, MB_CASE_UPPER);
        $changed = $newsubject !== $subject;
        $subject = $newsubject;
        return $changed;
    }

    /**
     * In-place str-to-lower, unicode-aware
     *
     * @param string &$subject
     * @return bool true if subject is changed
     */
    public static function lower(string &$subject): bool
    {
        $newsubject = mb_convert_case($subject, MB_CASE_LOWER);
        $changed = $newsubject !== $subject;
        $subject = $newsubject;
        return $changed;
    }

    /**
     * In-place tr-operation
     * @param string $subject
     * @param string $from
     * @param string $to
     * @return bool true if subject is changed
     */
    public static function tr(string &$subject, string $from, string $to): bool
    {
        $newsubject = strtr($subject, $from, $to);
        $changed = $newsubject !== $subject;
        $subject = $newsubject;
        return $changed;
    }

    /**
     * Checks whether a string is foldable, i.e. at least one letter can be converted to upper or to lower case, unicode-aware
     *
     * @param string $subject
     * @return bool
     */
    public static function foldable(string $subject): bool
    {
        return mb_convert_case($subject, MB_CASE_LOWER) !== mb_convert_case($subject, MB_CASE_UPPER);
    }

    /**
     * Compare two strings regardless of its case, unicode-aware
     *
     * @param string $a
     * @param string $b
     * @return bool
     */
    public static function fceq(string $a, string $b): bool
    {
        return mb_convert_case($a, MB_CASE_FOLD) === mb_convert_case($b, MB_CASE_FOLD);
    }

    /**
     * Tests if a string is convertable without loss between to encodings
     *
     * Returns true if there-and-back-again convertion results to the original string
     *
     * @param string $subject
     * @param string $to target encoding
     * @param string $from original encoding, defaults to UTF-8
     * @return bool
     */
    public static function convertable(string $subject, string $to, string $from = 'utf8'): bool
    {
        $a = mb_convert_encoding($subject, $to, $from);
        $b = mb_convert_encoding($a, $from, $to);
        return $subject === $b;
    }

    /**
     * Reverse-apply of formatted string
     *
     * ```php
     * $str = Str::surround('Hello {World}!', '<b>', '</b>');
     * ```
     *
     * @param string $subject
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    public static function surround(string $subject, string $prefix, string $suffix): string
    {
        Str::replace($subject, '/(?<!\\\\)\{/', $prefix);
        Str::replace($subject, '/\\\{/', '{');
        Str::replace($subject, '/(?<!\\\\)\}/', $suffix);
        Str::replace($subject, '/\\\}/', '}');
        return $subject;
    }

    /**
     * @param string $subject
     * @param array $formats
     * @return string
     */
    public static function enbrace(string $subject, array $formats): string
    {
        Str::replace(
            $subject,
            '/(?<!\\\\)\{(\w+)\|(.*?)(?<!\\\\)\}/',
            function ($match) use ($formats) {
                $format = Arr::get($formats, $match[1]);
                return sprintf($format, $match[2]);
            }
        );
        Str::replace($subject, '/\\\{/', '{');
        Str::replace($subject, '/\\\}/', '}');
        return $subject;
    }

    /**
     * Wrapper for date_format
     *
     * @param DateTimeInterface|null $dt
     * @param string $format
     * @param string|null $default
     * @return string|null
     * @see date_format
     */
    public static function ftime(?DateTimeInterface $dt, string $format, ?string $default = null): ?string
    {
        return is_null($dt) ? $default : $dt->format($format);
    }

    /**
     * Strip undecodable characters from a string (in-place)
     *
     * @param string $subject
     * @param string $encoding defaults to UTF-8
     * @return bool true if $subject is changed
     */
    public static function strip(string &$subject, string $encoding = 'utf8'): bool
    {
        $orig = mb_substitute_character();
        try {
            mb_substitute_character('none');
            $converted = mb_convert_encoding($subject, $encoding, $encoding);
            $changed = strlen($subject) !== strlen($converted);
            $subject = $converted;
            return $changed;
        } finally {
            mb_substitute_character($orig);
        }
    }

    /**
     * Convert string to another encoding with transliteration
     *
     * Non-covertable characters can be replaced by $replacement. It defaults to null, so the replacement character is left untouched. An empty string means non-covertable characters are ignored.
     *
     * @param string $in UTF-8 encoded string
     * @param string $to Target encoding
     * @param ?string $replacement Replacement character
     * @return string
     */
    public static function translit(string $in, string $to, string $replacement = null): string
    {
        // This function only accepts UTF-8 strings
        $from = 'utf8';

        // Remove all non-representable characters from input string...
        self::strip($in, $from);

        if (!is_null($replacement)) {
            // ...and also for replacement character, but for target encoding
            self::strip($replacement, $to);
        }

        // Determine replacement character in target encoding (mostly a question mark)
        $mark = mb_convert_encoding(mb_chr(0xFFFD), $to, $from);

        // Split whole string into characters
        $buf = mb_str_split($in, 1);
        $out = '';

        $iconv = extension_loaded('iconv');

        foreach ($buf as $old) {
            $cp = mb_ord($old);

            // Pass-through lowest 7 bits
            if ($cp < 0x80) {
                $out .= $old;
                continue;
            }

            if ($cp < 0x100 or !$iconv) {
                // characters with 1 byte length can be encoded directly
                $new = mb_convert_encoding($old, $to, $from);
            } else {
                // all other multi-byte characters are safely encoded by iconv
                // (with translitertion)
                assert(extension_loaded('iconv'));
                $new = @iconv($from, "$to//TRANSLIT", $old);
                if ($new === false) {
                    $new = $mark;
                }
            }

            // Compare with replacement character
            if (!is_null($replacement) and $new === $mark) {
                $out .= $replacement;
            } else {
                $out .= $new;
            }
        }
        return $out;
    }

    /**
     * Remove all non-printable unicode characters, in-place
     *
     * @param string &$subject
     * @param int $limit
     */
    public static function vacuum(string &$subject, int $limit = -1): void
    {
        if (mb_strlen($subject) === 0) {
            return;
        }
        self::remove($subject, '/[[:^print:]]+/u', $limit);
    }

    /**
     * Test if a string contains only printable unicode characters
     *
     * @param string $subject
     * @return bool
     */
    public static function printable(string $subject): bool
    {
        $origlen = mb_strlen($subject);
        if ($origlen === 0) {
            return true;
        }
        /*
         * its not possible to match non-printable unicode character
        if (!is_null(self::match($subject, '/[[^:print:]]/u'))) {
            return false;
        }
         */
        Str::vacuum($subject, 1);
        return mb_strlen($subject) === $origlen;
    }
}
