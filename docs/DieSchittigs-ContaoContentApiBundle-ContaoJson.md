Markocupic\ContaoContentApi\ContaoJson
===============

ContaoJson tries to pack &quot;everything Contao&quot; into a JSON-serializable package.

It works with:
 - Contao Collections
 - Contao Models
 - Arrays (of Models or anything else)
 - Objects
 - Strings and numbers
The main features are
 - File objects (e.g. singleSRC) are resolved automatically
 - Serialized arrays are resolved automatically
 - HTML will be unescaped automatically
 - Contao Insert-Tags are resolved automatically
ContaoJson will recursively call itself until all fields are resolved.


* Class name: ContaoJson
* Namespace: Markocupic\ContaoContentApi
* This class implements: JsonSerializable




Properties
----------


### $data

    public mixed $data = null





* Visibility: **public**


### $allowedFields

    private mixed $allowedFields





* Visibility: **private**


Methods
-------


### __construct

    mixed Markocupic\ContaoContentApi\ContaoJson::__construct(mixed $data, array $allowedFields)

constructor.



* Visibility: **public**


#### Arguments
* $data **mixed** - &lt;p&gt;any data you want resolved and serialized&lt;/p&gt;
* $allowedFields **array** - &lt;p&gt;an array of whitelisted keys (non-matching values will be purged)&lt;/p&gt;



### handleCollection

    mixed Markocupic\ContaoContentApi\ContaoJson::handleCollection(\Contao\Model\Collection $collection)





* Visibility: **private**


#### Arguments
* $collection **Contao\Model\Collection**



### handleArray

    mixed Markocupic\ContaoContentApi\ContaoJson::handleArray(array $array)





* Visibility: **private**


#### Arguments
* $array **array**



### handleObject

    mixed Markocupic\ContaoContentApi\ContaoJson::handleObject(\Markocupic\ContaoContentApi\object $object)





* Visibility: **private**


#### Arguments
* $object **Markocupic\ContaoContentApi\object**



### handleNumber

    mixed Markocupic\ContaoContentApi\ContaoJson::handleNumber($number)





* Visibility: **private**


#### Arguments
* $number **mixed**



### handleString

    mixed Markocupic\ContaoContentApi\ContaoJson::handleString(\Markocupic\ContaoContentApi\string $string)





* Visibility: **private**


#### Arguments
* $string **Markocupic\ContaoContentApi\string**



### isAssoc

    mixed Markocupic\ContaoContentApi\ContaoJson::isAssoc(array $arr)





* Visibility: **private**


#### Arguments
* $arr **array**



### unserialize

    mixed Markocupic\ContaoContentApi\ContaoJson::unserialize(\Markocupic\ContaoContentApi\string $string)





* Visibility: **private**


#### Arguments
* $string **Markocupic\ContaoContentApi\string**



### jsonSerialize

    mixed Markocupic\ContaoContentApi\ContaoJson::jsonSerialize()





* Visibility: **public**



