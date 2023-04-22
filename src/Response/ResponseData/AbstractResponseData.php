<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi\Response\ResponseData;

abstract class AbstractResponseData implements ResponseDataInterface
{
    protected array $arrData;

    public function __construct(array $arrData = [])
    {
        $this->setRow($arrData);
    }

    public function get(string $key): mixed
    {
        return $this->arrData[$key];
    }

    public function getAll(): array
    {
        return $this->arrData;
    }

    public function setRow(array $data): void
    {
        $this->arrData = $data;
    }

    public function flush(): void
    {
        $this->arrData = [];
    }

    public function set(string $key, mixed $value): void
    {
        $this->arrData[$key] = $value;
    }
}
