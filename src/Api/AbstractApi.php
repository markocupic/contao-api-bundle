<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi\Api;

use Contao\Model;

abstract class AbstractApi implements ApiInterface
{
    /**
     * @var Model
     */
    protected $model;

    protected function returnError(string $error): ApiInterface
    {
        $this->model = new \stdClass();
        $this->model->message = $error;
        $this->model->compiledHTML = null;

        return $this;
    }
}
