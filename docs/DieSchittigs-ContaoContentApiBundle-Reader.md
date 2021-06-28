Markocupic\ContaoContentApi\Reader
===============

Reader augments reader model classes for the API.




* Class name: Reader
* Namespace: Markocupic\ContaoContentApi
* Parent class: [Markocupic\ContaoContentApi\AugmentedContaoModel](DieSchittigs-ContaoContentApiBundle-AugmentedContaoModel.md)





Properties
----------


### $model

    public mixed $model = null





* Visibility: **public**


Methods
-------


### __construct

    mixed Markocupic\ContaoContentApi\Reader::__construct(string $model, string $url)

constructor.



* Visibility: **public**


#### Arguments
* $model **string** - &lt;p&gt;Reader Model class (e.g. NewsModel)&lt;/p&gt;
* $url **string** - &lt;p&gt;Current URL&lt;/p&gt;



### urlToAlias

    mixed Markocupic\ContaoContentApi\Reader::urlToAlias(string $url)

Gets the alias from a URL.



* Visibility: **private**


#### Arguments
* $url **string** - &lt;p&gt;URL to get the alias from&lt;/p&gt;



### toJson

    mixed Markocupic\ContaoContentApi\ContaoJsonSerializable::toJson()





* Visibility: **public**
* This method is defined by [Markocupic\ContaoContentApi\ContaoJsonSerializable](DieSchittigs-ContaoContentApiBundle-ContaoJsonSerializable.md)




### __get

    mixed Markocupic\ContaoContentApi\AugmentedContaoModel::__get(string $property)

Get the value from the attached model.



* Visibility: **public**
* This method is defined by [Markocupic\ContaoContentApi\AugmentedContaoModel](DieSchittigs-ContaoContentApiBundle-AugmentedContaoModel.md)


#### Arguments
* $property **string** - &lt;p&gt;key&lt;/p&gt;



### __set

    mixed Markocupic\ContaoContentApi\AugmentedContaoModel::__set(string $property, mixed $value)

Set the value in the attached model.



* Visibility: **public**
* This method is defined by [Markocupic\ContaoContentApi\AugmentedContaoModel](DieSchittigs-ContaoContentApiBundle-AugmentedContaoModel.md)


#### Arguments
* $property **string** - &lt;p&gt;key&lt;/p&gt;
* $value **mixed** - &lt;p&gt;value&lt;/p&gt;


