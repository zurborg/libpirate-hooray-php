Pirate\Hooray\Str
===============

Str is a class containing a bunch of public static functions

There is no object-orientated interface.


* Class name: Str
* Namespace: Pirate\Hooray







Methods
-------


### ok

    integer Pirate\Hooray\Str::ok(mixed $string, mixed $nvl)

Checks whether string is a scalar and not a bool and returns its length

```php
Str::ok(''); // 0
Str::ok('Hello, World!'); // 13
Str::ok(null); // false
Str::ok(false); // false
Str::ok(true); // false
Str::ok(123); // 3
Str::ok([]); // false
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $string **mixed**
* $nvl **mixed**



### split

    array<mixed,string> Pirate\Hooray\Str::split(string $path, integer $limit)

Split string into an array by its first character

```php
Str::split('/foo/bar'); // ['foo', 'bar']
Str::split('.foo.bar'); // ['foo', 'bar']
Str::split('#foo#bar'); // ['foo', 'bar']
Str::split('/foo.bar/bar#foo'); // ['foo.bar', 'bar#foo']
Str::split('.foo#bar.bar/foo'); // ['foo#bar', 'bar/foo']
Str::split('#foo/bar#bar.foo'); // ['foo/bar', 'bar.foo']
Str::split(''); // null
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $path **string**
* $limit **integer**



### regexp

    mixed Pirate\Hooray\Str::regexp(\Pirate\Hooray\string $regexp, \Pirate\Hooray\bool $delim, \Pirate\Hooray\string $modifiers)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $regexp **Pirate\Hooray\string**
* $delim **Pirate\Hooray\bool**
* $modifiers **Pirate\Hooray\string**



### match

    array<mixed,string> Pirate\Hooray\Str::match(string $subject, string $regexp, integer $offset)

Apply regular expression and return matching results

```php
if (Str::match('Hello!', '/ll/')) {
    ...;
}

if ($match = Str::match('Hello!', '/H(a|e)llo/')) {
    ...;
}
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $subject **string**
* $regexp **string** - &lt;p&gt;regular expression&lt;/p&gt;
* $offset **integer** - &lt;p&gt;string offset&lt;/p&gt;



### matchall

    array<mixed,string> Pirate\Hooray\Str::matchall(string $subject, string $regexp, integer $offset)

Apply regular expression and return all matching results

```php
if (Str::matchall('Hello, World!', '/\w+/')) {
    ...;
}
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $subject **string**
* $regexp **string** - &lt;p&gt;regular expression&lt;/p&gt;
* $offset **integer** - &lt;p&gt;string offset&lt;/p&gt;



### fullmatch

    array<mixed,string> Pirate\Hooray\Str::fullmatch(string $subject, string $regexp, string $modifiers)

Apply full-match regular expression and return results

```php
if (Str::fullmatch('Hello!', '[A-Z][elo]{4}!')) {
    // regex is actually something like /^[A-Z][elo]{4}!$/
    ...;
}
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $subject **string**
* $regexp **string** - &lt;p&gt;regular expression&lt;/p&gt;
* $modifiers **string** - &lt;p&gt;optional modifiers&lt;/p&gt;



### replace

    void Pirate\Hooray\Str::replace(string $subject, string $regexp, mixed $replacement, integer $limit)

In-place PCRE replacement



* Visibility: **public**
* This method is **static**.


#### Arguments
* $subject **string** - &lt;p&gt;in/out string&lt;/p&gt;
* $regexp **string** - &lt;p&gt;regular expression&lt;/p&gt;
* $replacement **mixed** - &lt;p&gt;string or something callable&lt;/p&gt;
* $limit **integer**



### remove

    void Pirate\Hooray\Str::remove(string $subject, string $regexp, integer $limit)

In-place PCRE replacement with empty string



* Visibility: **public**
* This method is **static**.


#### Arguments
* $subject **string** - &lt;p&gt;in/out string&lt;/p&gt;
* $regexp **string** - &lt;p&gt;regular expression&lt;/p&gt;
* $limit **integer**



### loop

    void Pirate\Hooray\Str::loop(string $subject, string $regexp, callable $function)

Offset-based iteration of a string match by regular expression

```php
Str::loop("-abc-def", "/-(\w+)/", function ($match) {
    print_r($match);
});
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $subject **string**
* $regexp **string**
* $function **callable**



### pluralize

    string Pirate\Hooray\Str::pluralize(string $text, integer $amount, string $search)

Pluralize formatted string

```php
Str::pluralize('{No|One|$} quer(y|ies) (is|are) found', 1); // 'One query is found'
```

+ Rule #1: `(pl)` expands when the amount is not one. Otherwise this expression is omitted
+ Rule #2: `{sl}` expands when the amount is exactly one. This is the opposite of rule #1.
+ Rule #3: `(one|two|three|four|all other)` expands to element in the list, whereas 0 expands to the last element.
+ Rule #4: `{zero|one|two|three|all other}` expands to element in the list, whereas 0 expands to the first element.
+ Rule #5: `$` expands to the numeric value of amount. This replacement can be changed with the 3rd parameter and defaults to the dollar sign.

* Visibility: **public**
* This method is **static**.


#### Arguments
* $text **string** - &lt;p&gt;Formatted string&lt;/p&gt;
* $amount **integer**
* $search **string**



### timechunks

    array<mixed,integer> Pirate\Hooray\Str::timechunks(float $seconds)

Split seconds into precise chunks

The resulting array consists of following indicies:
+ `C` - centuries
+ `D` - decades
+ `Y` - years
+ `m` - months
+ `w` - weeks
+ `d` - days
+ `H` - hours
+ `M` - minutes
+ `S` - seconds
+ `f` - milliseconds
+ `!` - rest as devident of `PHP_INT_MAX` devisor

* Visibility: **public**
* This method is **static**.


#### Arguments
* $seconds **float**



### duration

    string Pirate\Hooray\Str::duration(float $seconds, integer $precision, string $locale)

Pretty print seconds in human-readable format

```php
Str::duration(3666, 2); // 'one hour and one minute'
Str::duration(3666, 3); // 'one hour, one minute and 6 seconds'
```

Currently available locals: `en` and `de`.

* Visibility: **public**
* This method is **static**.


#### Arguments
* $seconds **float**
* $precision **integer**
* $locale **string**



### salt2y

    string Pirate\Hooray\Str::salt2y(integer $rounds)

Generates a pseudo random salt for blowfish password encryption

```php
$salt = Str::salt2y(10); // 10 rounds
$password = crypt('testtest', $salt);
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $rounds **integer**



### parseMCF

    mixed Pirate\Hooray\Str::parseMCF(\Pirate\Hooray\string $password)

Parses a password in the _modular crypt format_ and returns some information about it

```php
$info = parseMCF('$5$rounds=80000$wnsT7Yr92oJoP28r$r6gESRx/RBya4a.LFKCFY.r4BT/onHS7Qg9BiSR58.5');
$info = [
    'identifier' => 5,
    'algorithm' => 'sha256',
    'salt' => 'wnsT7Yr92oJoP28r',
    'hash' => 'r6gESRx/RBya4a.LFKCFY.r4BT/onHS7Qg9BiSR58.5',
    'format' => '$5$rounds=80000$',
    'prefix' => '$5$rounds=80000$wnsT7Yr92oJoP28r$',
    'params' => [
        'rounds' => 80000
    ]
];
```

The following keyswords are recognized:

+ `identifier` - Bare identifier of the crypted string, this is the part between the first two dollar signs
+ `algorithm` - Short name of the algorithm, derived from the identifier
+ `salt` - Salt of the hash
+ `hash` - The bare hashed password itself
+ `format` - Identifier plus params or just everything without crypted parts (salt and hash)
+ `prefifx` - Identifier plus params plus salt, or just everything without the bare hash
+ `params` - A key-value based array with all parameters found in the string

Currently, only format 2 (plus all sub-formats), 5 and 6 are supported. More to come.

* Visibility: **public**
* This method is **static**.


#### Arguments
* $password **Pirate\Hooray\string**



### uuidV4

    string Pirate\Hooray\Str::uuidV4(boolean $binary)

Generate pseudo-random V4 universal unique identifier



* Visibility: **public**
* This method is **static**.


#### Arguments
* $binary **boolean** - &lt;p&gt;return binary representation instead of string representation&lt;/p&gt;



### upper

    boolean Pirate\Hooray\Str::upper(string $subject)

In-place str-to-upper, unicode-aware



* Visibility: **public**
* This method is **static**.


#### Arguments
* $subject **string** - &lt;p&gt;&amp;$subject&lt;/p&gt;



### lower

    boolean Pirate\Hooray\Str::lower(string $subject)

In-place str-to-lower, unicode-aware



* Visibility: **public**
* This method is **static**.


#### Arguments
* $subject **string** - &lt;p&gt;&amp;$subject&lt;/p&gt;



### foldable

    boolean Pirate\Hooray\Str::foldable(string $subject)

Checks whether a string is foldable, i.e. at least one letter can be converted to upper or to lower case, unicode-aware



* Visibility: **public**
* This method is **static**.


#### Arguments
* $subject **string**



### fceq

    boolean Pirate\Hooray\Str::fceq(string $a, string $b)

Compare two strings regardless of its case, unicode-aware



* Visibility: **public**
* This method is **static**.


#### Arguments
* $a **string**
* $b **string**



### surround

    string Pirate\Hooray\Str::surround(string $subject, string $prefix, string $suffix)

Reverse-apply of formatted string

```php
$str = Str::surround('Hello {World}!', '<b>', '</b>');
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $subject **string**
* $prefix **string**
* $suffix **string**



### enbrace

    mixed Pirate\Hooray\Str::enbrace(\Pirate\Hooray\string $subject, array $formats)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $subject **Pirate\Hooray\string**
* $formats **array**


