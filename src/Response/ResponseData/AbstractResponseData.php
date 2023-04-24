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

namespace Markocupic\ContaoApiBundle\Response\ResponseData;

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
