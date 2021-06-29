<?php

namespace Markocupic\ContaoContentApi;

use Contao\Controller;
use Contao\Config;
use Markocupic\ContaoContentApi\Api\ApiContentElement;

/**
 * Reader augments reader model classes for the API.
 */
class Reader extends AugmentedContaoModel
{
    /**
     * constructor.
     *
     * @param string $strModelClass Reader Model class (e.g. NewsModel)
     * @param string $url   Current URL
     */
    public function __construct(string $strModelClass, string $url)
    {
        $alias = $this->urlToAlias($url);
        $this->model = $strModelClass::findOneByAlias($alias);

        if (!$this->model || !Controller::isVisibleElement($this->model)) {
            return null;
        }

        $this->content = ApiContentElement::findByPidAndTable($this->id, $strModelClass::getTable());
    }

    /**
     * Gets the alias from a URL.
     *
     * @param string $url URL to get the alias from
     */
    private function urlToAlias($url)
    {
        while (substr($url, -1, 1) === '/') {
            $url = substr($url, 0, -1);
        }
        $arrUrlParts = explode('/', $url);
        $alias = end($arrUrlParts);

        if ($suffix = Config::get('urlSuffix')) {
            $alias = str_replace($suffix, '', $alias);
        }

        return $alias;
    }
}
