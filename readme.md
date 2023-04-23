# Contao Content API
## Forked from [DieSchittigs/contao-content-api](https://github.com/DieSchittigs/contao-content-api-bundle) and then adapted
## This extension is still under construction. Please don't use in production.


## Requirements
You'll need an up-and-running **Contao 4.13.x** installation.

## Installation
Install [composer](https://getcomposer.org) if you haven't already,
enter this command in the main directory of your Contao installation:

    composer require markocupic/contao-content-api

Contao Content API is now installed and ready to use.

## Usage

Once installed, the following routes are available:

##### /_mc_cc_api/{key}/content/{moduleId}

Gets the content of a module by id.

## Hooks

We provide some basic hooks:

```
class Hooks{

    // $GLOBALS['TL_HOOKS']['apiBeforeInit']
    public static apiBeforeInit(Request $request){
        return $request
    }

    // $GLOBALS['TL_HOOKS']['apiAfterInit']
    public static apiAfterInit(Request $request){
        return $request
    }

    // $GLOBALS['TL_HOOKS']['apiContaoJson']
    public static apiContaoJson(ContaoJson $contaoJson, mixed $data){
        if($data instanceof ContentModel){
            $contaoJson->data = null;
            // End of the line
            return false;
        }
        // Do your thing, ContaoJson
        return true;

    }

    // $GLOBALS['TL_HOOKS']['apiResponse']
    public static apiResponse(mixed $data){
        $data->tamperedWith = true;
        return $data;

    }

    // $GLOBALS['TL_HOOKS']['apiModuleGenerated']
    public static function apiModuleGenerated(ApiModule $module, string $moduleClass)
    {
        // Override the way certain modules are handled
        if ($moduleClass != 'Contao\ModuleBlogList') {
            return;
        }
        $_module = new ModuleBlogList($module->model, null);
        $module->items = $_module->fetchItems(
            $module->category
        );
    }
}
```

