<?php

declare(strict_types=1);

/*
 * This file is part of Contao Api Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-api-bundle
 */

namespace Markocupic\ContaoApiBundle\Exceptions;

use Markocupic\ContaoApiBundle\ContaoJsonSerializable;
use Markocupic\ContaoApiBundle\Json\ContaoJson;

/**
 * ContentApiNotFoundException is thrown whenever something is simply not there.
 * It indicates an Error 404.
 */
class ContentApiNotFoundException extends \Exception
{
    public function toJson(): ContaoJson
    {
        return new ContaoJson([
            'error' => 'ContentApiNotFoundException',
            'message' => $this->getMessage(),
        ]);
    }
}
