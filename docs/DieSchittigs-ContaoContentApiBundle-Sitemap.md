Markocupic\ContaoContentApi\Sitemap
===============

Sitemap represents the actual site structure as an object tree.

The resulting instance can be iterated and used like an array.


* Class name: Sitemap
* Namespace: Markocupic\ContaoContentApi
* This class implements: IteratorAggregate, ArrayAccess, Countable, [Markocupic\ContaoContentApi\ContaoJsonSerializable](DieSchittigs-ContaoContentApiBundle-ContaoJsonSerializable.md)




Properties
----------


### $sitemap

    protected mixed $sitemap = array()





* Visibility: **protected**


### $sitemapFlat

    public mixed $sitemapFlat





* Visibility: **public**


Methods
-------


### __construct

    mixed Markocupic\ContaoContentApi\Sitemap::__construct(string $language, integer $pid)

constructor.



* Visibility: **public**


#### Arguments
* $language **string** - &lt;p&gt;If set, ignores other languages&lt;/p&gt;
* $pid **integer** - &lt;p&gt;Parent ID (for recursive calls)&lt;/p&gt;



### getIterator

    mixed Markocupic\ContaoContentApi\Sitemap::getIterator()





* Visibility: **public**




### offsetExists

    mixed Markocupic\ContaoContentApi\Sitemap::offsetExists($offset)





* Visibility: **public**


#### Arguments
* $offset **mixed**



### offsetGet

    mixed Markocupic\ContaoContentApi\Sitemap::offsetGet($offset)





* Visibility: **public**


#### Arguments
* $offset **mixed**



### offsetSet

    mixed Markocupic\ContaoContentApi\Sitemap::offsetSet($offset, $value)





* Visibility: **public**


#### Arguments
* $offset **mixed**
* $value **mixed**



### offsetUnset

    mixed Markocupic\ContaoContentApi\Sitemap::offsetUnset($offset)





* Visibility: **public**


#### Arguments
* $offset **mixed**



### count

    mixed Markocupic\ContaoContentApi\Sitemap::count()





* Visibility: **public**




### toJson

    mixed Markocupic\ContaoContentApi\ContaoJsonSerializable::toJson()





* Visibility: **public**
* This method is defined by [Markocupic\ContaoContentApi\ContaoJsonSerializable](DieSchittigs-ContaoContentApiBundle-ContaoJsonSerializable.md)



