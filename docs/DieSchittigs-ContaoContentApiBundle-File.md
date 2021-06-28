Markocupic\ContaoContentApi\File
===============

File augments FilesModel for the API.




* Class name: File
* Namespace: Markocupic\ContaoContentApi
* Parent class: [Markocupic\ContaoContentApi\AugmentedContaoModel](DieSchittigs-ContaoContentApiBundle-AugmentedContaoModel.md)





Properties
----------


### $fileObj

    private mixed $fileObj = array('id', 'uuid', 'name', 'extension', 'singleSRC', 'meta', 'size', 'filesModel')





* Visibility: **private**
* This property is **static**.


### $model

    public mixed $model = null





* Visibility: **public**


Methods
-------


### __construct

    mixed Markocupic\ContaoContentApi\File::__construct(string $uuid, mixed $size)

constructor.



* Visibility: **public**


#### Arguments
* $uuid **string** - &lt;p&gt;uuid of the FilesModel&lt;/p&gt;
* $size **mixed** - &lt;p&gt;Object or serialized string representing the (image) size&lt;/p&gt;



### children

    mixed Markocupic\ContaoContentApi\File::children(string $uuid, integer $depth)

Recursively load file (directory) children.



* Visibility: **private**
* This method is **static**.


#### Arguments
* $uuid **string** - &lt;p&gt;uuid of the FilesModel&lt;/p&gt;
* $depth **integer** - &lt;p&gt;How deep do you want to fetch children?&lt;/p&gt;



### get

    mixed Markocupic\ContaoContentApi\File::get(string $path, integer $depth)

Recursively load file by path.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $path **string** - &lt;p&gt;Path of the FilesModel&lt;/p&gt;
* $depth **integer** - &lt;p&gt;How deep do you want to fetch children?&lt;/p&gt;



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


