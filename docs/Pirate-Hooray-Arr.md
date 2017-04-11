Pirate\Hooray\Arr
===============

Arr is a class containing a bunch of public static functions

There is no object-orientated interface.


* Class name: Arr
* Namespace: Pirate\Hooray







Methods
-------


### ok

    integer Pirate\Hooray\Arr::ok(mixed $array, mixed $nvl)

Checks whether $array is an array and returns the actual size

```php
Arr::ok([]);             // returns 0
Arr::ok(['foo','bar']);  // returns 2
Arr::ok('foobar');       // returns false
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **mixed**
* $nvl **mixed**



### index

    integer Pirate\Hooray\Arr::index(array $array, integer $index)

Normalize index of an array

If index is below 0 or greater than the size of the array, the index value will be reduced/expanded.
An index value of -1 results to the last element of the the array

```php
Arr::index([1,2,3], -1);  // returns 2
Arr::index([1,2,3], -11); // returns 1
Arr::index([1,2,3], 9);   // returns 0
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $index **integer**



### get

    mixed Pirate\Hooray\Arr::get(array $array, string $key, mixed $default)

Get the value of an array by its key or return a default value

When the key does not exists, a default value will be returned

```php
$A = [
    'foo' => 123,
    'bar' => 456
];
Arr::get($A, 'foo');          // returns 123
Arr::get($A, 'bla');          // returns null, no error!
Arr::get($A, 'bla', 'blubb'); // returns 'blubb'
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $key **string**
* $default **mixed**



### has

    boolean Pirate\Hooray\Arr::has(array $array, string $key)

Wrapper for `array_key_exists()`



* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $key **string**



### getIndex

    mixed Pirate\Hooray\Arr::getIndex(array $array, integer $index, mixed $default)

Get the value of an array by its index or return a default value

```php
$A = [ 'foo', 'bar' ];
Arr::getIndex($A, -1); // returns 'bar'
```

If the array is an associative array, (or there is no numeric key in the range of 0 .. n-1) the default value will be returned,

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $index **integer**
* $default **mixed**



### init

    mixed Pirate\Hooray\Arr::init(array $array, string $key, mixed $value)

Set an array element if the key does not exists already
Returns the actual value of the element

```php
$A = [
    'foo' => 123
];
Arr::init($A, 'foo', 234); // does not override 'foo', just returns 123
Arr::init($A, 'bar', 456); // sets 'bar', returns 456
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array** - &lt;p&gt;&amp;$array&lt;/p&gt;
* $key **string**
* $value **mixed**



### consume

    mixed Pirate\Hooray\Arr::consume(array $array, string $key, mixed $default)

Delete an array element and return its value

```php
$A = [
    'foo' => 123
];
Arr::consume($A, 'foo'); // unsets $A['foo'] and returns 123
// hint: $A is empty now
Arr::consume($A, 'bar', 456); // does nothing, returns 456
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array** - &lt;p&gt;&amp;$array&lt;/p&gt;
* $key **string**
* $default **mixed**



### assert

    mixed Pirate\Hooray\Arr::assert(array $array, string $key, mixed $throw)

Check whether an array key exists or throw an exception

```php
$A = [
    'foo' => 123
];
Arr::assert($A, 'foo', 'meh'); // nothing happens
Arr::assert($A, 'bar', 'this does not exists'); // throw new DomainException('this does not exists')
$e = new Exception('...');  // just instanciating, not throwing
Arr::assert($A, 'bar', $e); // throw $e
Arr::assert($A, 'bar', function ($key) { return "bad: $key"; }); // retruns 'bad: bar'
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $key **string**
* $throw **mixed**



### in

    boolean Pirate\Hooray\Arr::in(array $haystack, mixed $needle, boolean $strict)

Wrapper for in_array but with strict comparision by default

```php
$A = [ '12', 34 ];
Arr::in($A,  12 ); // false
Arr::in($A, '12'); // true
Arr::in($A,  34 ); // true
Arr::in($A, '34'); // false
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $haystack **array**
* $needle **mixed**
* $strict **boolean**



### is

    boolean Pirate\Hooray\Arr::is(array $array, string $key, mixed $expect, boolean $strict)

Strict comparision of an array value

```php
$A = [
    'foo' =>  123,
    'bar' => '456'
];
Arr::is($A, 'foo', 123);   // returns true
Arr::is($A, 'bar', 456);   // returns false
Arr::is($A, 'xxx', 'yyy'); // returns false
Arr::is($A, 'yyy', null);  // returns true, since Arr::get returns null as default value if key does not exists
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $key **string**
* $expect **mixed**
* $strict **boolean**



### any

    boolean Pirate\Hooray\Arr::any(array $array, array<mixed,string> $keys, boolean $strict)

Checks whether any element exists in an array

```php
$A = [ 'aaa', 'bbb' ];
Arr::any($A, [ 'aaa', 'ccc' ]); // one match, returns true
Arr::any($A, [ 'ccc', 'ddd' ]); // no match, returns false
Arr::any($A, []); // no keys, returns null
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $keys **array&lt;mixed,string&gt;**
* $strict **boolean**



### all

    boolean Pirate\Hooray\Arr::all(array $array, array<mixed,string> $keys, boolean $strict)

Checks whether all elements exists in an array

```php
$A = [ 'aaa', 'bbb', 'ccc' ];
Arr::all($A, [ 'ccc', 'ddd' ]); // one does not match, returns false
Arr::all($A, [ 'ccc', 'aaa' ]); // all matches, returns true
Arr::all($A, []); // no keys, returns null
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $keys **array&lt;mixed,string&gt;**
* $strict **boolean**



### assoc

    boolean Pirate\Hooray\Arr::assoc(array $array)

Checks whether an array is an associative array

```php
$A = [ 4, 9, 1 ];
$B = [ 'foo' => 'bar' ];
Arr::assoc($A); // returns false
Arr::assoc($B); // returns true
Arr::assoc([]); // returns null
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**



### flist

    array Pirate\Hooray\Arr::flist(mixed $array, mixed $nvl)

Force an array to be an non-associative array

```php
Arr::flist('foo'); // returns [ 'foo' ]
Arr::flist([ 'foo' ,  'bar' ]); // returns   [ 'foo' ,  'bar' ]
Arr::flist([ 'foo' => 'bar' ]); // returns [ [ 'foo' => 'bar' ] ]
Arr::flist(null); // returns []
Arr::flist(null, false); // returns false
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **mixed**
* $nvl **mixed**



### getDeep

    mixed Pirate\Hooray\Arr::getDeep(array $array, array<mixed,string> $keys, mixed $default)

Get a deep value of an array of array(s) or return default value

```php
$A = [
    'foo' => [
        'bar' => 123
    ]
];
Arr::getDeep($A, [ 'foo', 'bar' ]);      // returns 123
Arr::getDeep($A, [ 'bar', 'foo' ], 456); // returns 456
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $keys **array&lt;mixed,string&gt;**
* $default **mixed**



### isDeep

    boolean Pirate\Hooray\Arr::isDeep(array $array, array<mixed,string> $keys, mixed $expect, boolean $strict)

Deep variant of is()

```php
$A = [
    'foo' => [
        'bar' => 123
    ]
];
Arr::isDeep($A, [ 'foo', 'bar' ], 123); // returns true
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $keys **array&lt;mixed,string&gt;**
* $expect **mixed**
* $strict **boolean**



### setDeep

    void Pirate\Hooray\Arr::setDeep(array $array, array<mixed,string> $keys, mixed $value)

Set a deep value

```php
$A = [
    'foo' => [
        'bar' => 123
    ]
];
Arr::setDeep($A, [ 'foo', 'bar' ], 456); // $A['foo']['bar'] is now 456
Arr::setDeep($A, [ 'foo' ], 789); // $A['foo'] is now 789
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array** - &lt;p&gt;&amp;$array&lt;/p&gt;
* $keys **array&lt;mixed,string&gt;**
* $value **mixed**



### getPath

    mixed Pirate\Hooray\Arr::getPath(array $array, string $path, mixed $default)

Get array element by path

```php
$A = [
    'foo' => [
        'bar' => 123
    ]
];
Arr::getPath($A, '/foo/bar'); // retuns 123
```

See also `\Pirate\Hooray\Str::split()`

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $path **string**
* $default **mixed**



### isPath

    boolean Pirate\Hooray\Arr::isPath(array $array, string $path, mixed $expect, boolean $strict)

Path variant of isDeep()

```php
$A = [
    'foo' => [
        'bar' => 123
    ]
];
Arr::isPath($A, '/foo/bar', 123); // returns true
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array**
* $path **string**
* $expect **mixed**
* $strict **boolean**



### setPath

    void Pirate\Hooray\Arr::setPath(array $array, string $path, mixed $value)

Path variant of setDeep()

```php
$A = [
    'foo' => [
        'bar' => 123
    ]
];
Arr::setPath($A, '/foo/bar', 456); // retuns $A['foo']['bar'] is now 456
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array** - &lt;p&gt;&amp;$array&lt;/p&gt;
* $path **string**
* $value **mixed**



### merge

    void Pirate\Hooray\Arr::merge(array $array1, array $array2)

In-place recursive merge of another array

```php
$A = [
   'foo' => 123,
];
Arr::merge($A, [ 'foo' => 456 ]);
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array1 **array** - &lt;p&gt;&amp;$array1&lt;/p&gt;
* $array2 **array**



### defaults

    void Pirate\Hooray\Arr::defaults(array $array, array $defaults)

Set default values in an array

```php
$A = [
   'foo' => 123,
];
Arr::defaults($A, [ 'bar' => 123, 'foo' => 456 ]);
// $A now contains [ 'bar' => 123, 'foo' => 123 ]
```

* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array** - &lt;p&gt;&amp;$array&lt;/p&gt;
* $defaults **array**



### shift

    mixed Pirate\Hooray\Arr::shift(array $array, mixed $default)

Remove first item of array and return it



* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array** - &lt;p&gt;&amp;$array&lt;/p&gt;
* $default **mixed** - &lt;p&gt;Default value if array is empty&lt;/p&gt;



### pop

    mixed Pirate\Hooray\Arr::pop(array $array, mixed $default)

Remove last item of array and return it



* Visibility: **public**
* This method is **static**.


#### Arguments
* $array **array** - &lt;p&gt;&amp;$array&lt;/p&gt;
* $default **mixed** - &lt;p&gt;Default value if array is empty&lt;/p&gt;


