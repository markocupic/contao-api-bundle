<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi\Backend;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Markocupic\ContaoContentApi\Model\ApiAppModel;

class TlApiApp
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
        $adapter = $this->framework->getAdapter(ApiAppModel::class);

        if (null === ($model = $adapter->findByPk($dc->id))) {
            return $value;
        }

        $model->key = md5(uniqid('', true));
        $model->save();

        return $model->key;
    }
}
