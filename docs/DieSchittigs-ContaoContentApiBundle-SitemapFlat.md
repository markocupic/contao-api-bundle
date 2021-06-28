Markocupic\ContaoContentApi\SitemapFlat
===============

SitemapFlat represents the actual site structure as a key value object.

Key: URL of the page
Value: PageModel.


* Class name: SitemapFlat
* Namespace: Markocupic\ContaoContentApi
* This class implements: [Markocupic\ContaoContentApi\ContaoJsonSerializable](DieSchittigs-ContaoContentApiBundle-ContaoJsonSerializable.md)




Properties
----------


### $sitemap

    public mixed $sitemap





* Visibility: **public**


Methods
-------


### __construct

    mixed Markocupic\ContaoContentApi\SitemapFlat::__construct(string $language)

constructor.



* Visibility: **public**


#### Arguments
* $language **string** - &lt;p&gt;If set, ignores other languages&lt;/p&gt;



### findUrl

    mixed Markocupic\ContaoContentApi\SitemapFlat::findUrl($url, $exactMatch)





* Visibility: **public**


#### Arguments
* $url **mixed**
* $exactMatch **mixed**



### toJson

    mixed Markocupic\ContaoContentApi\ContaoJsonSerializable::toJson()





* Visibility: **public**
* This method is defined by [Markocupic\ContaoContentApi\ContaoJsonSerializable](DieSchittigs-ContaoContentApiBundle-ContaoJsonSerializable.md)



