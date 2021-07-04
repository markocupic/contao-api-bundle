<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace Markocupic\ContaoContentApi\Backend;

use Contao\BackendUser;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Database;
use Contao\DataContainer;
use Contao\Image;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\ApiBundle\Model\ApiAppModel;
use Markocupic\ContaoContentApi\Model\AppModel;

class TlApp
{
    /**
     * @var ContaoFramework
     */
    protected $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }


    public function generateApiToken($value, DataContainer $dc)
    {
        if ('' !== $value) {
            return $value;
        }

        /** @var ApiAppModel $adapter */
        $adapter = $this->framework->getAdapter(AppModel::class);

        if (null === ($model = $adapter->findByPk($dc->id))) {
            return $value;
        }

        $model->key = md5(uniqid('', true));
        $model->save();

        return $model->key;
    }

}