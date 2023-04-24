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

namespace Markocupic\ContaoApiBundle\Api;

use Markocupic\ContaoApiBundle\Response\ResponseData\ResponseDataInterface;

abstract class AbstractApi implements ApiInterface
{
    protected ResponseDataInterface|null $responseData;

    public function initializeResponseData(ResponseDataInterface $responseData): void
    {
        $this->responseData = $responseData;
    }

    public function getResponseData(): ResponseDataInterface|null
    {
        return $this->responseData;
    }

    protected function returnError(string $error): ApiInterface
    {
        $this->responseData->setRow(
            [
                'message' => $error,
                'compiledHTML' => null,
            ]
        );

        return $this;
    }
}
