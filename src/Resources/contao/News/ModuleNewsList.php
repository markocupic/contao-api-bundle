<?php

namespace Markocupic\ContaoContentApi\News;

class ModuleNewsList extends \Contao\ModuleNewsList
{
    public function pFetchItems($newsArchives, $blnFeatured, $limit, $offset)
    {
        return $this->fetchItems($newsArchives, $blnFeatured, $limit, $offset);
    }
}
