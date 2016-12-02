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

Checks whether string is a string and returns its length



* Visibility: **public**
* This method is **static**.


#### Arguments
* $string **mixed**
* $nvl **mixed**



### split

    array<mixed,string> Pirate\Hooray\Str::split(string $path, integer $limit)

Split string into an array by its first character



* Visibility: **public**
* This method is **static**.


#### Arguments
* $path **string**
* $limit **integer**



### pluralize

    string Pirate\Hooray\Str::pluralize(string $text, integer $amount, string $search)

Pluralize formatted string

```php
Str::pluralize('{No|One|$} quer(y|ies) (is|are) found', 1); # 'One query is found'
```

Rule #1: `(pl)` expands when the amount is exactly one. Otherwise this expression is omitted
Rule #2: `{sl}` expands when the amount is not one. This is the opposite of rule #1.
Rule #3: `(one|two|three|four|all other)` expands to element in the list, whereas 0 expands to the last element.
Rule #4: `{zero|one|two|three|all other}` expands to element in the list, whereas 0 expands to the first element.
Rule #5: `$` expands to the numeric value of amount. This replacement can be changed with the 3rd parameter and defaults to the dollar sign.

* Visibility: **public**
* This method is **static**.


#### Arguments
* $text **string** - &lt;p&gt;Formatted string&lt;/p&gt;
* $amount **integer**
* $search **string**


