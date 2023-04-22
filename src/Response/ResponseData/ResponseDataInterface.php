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

interface ResponseDataInterface
{
    public function set(string $key, mixed $value): void;

    public function get(string $key): mixed;

    public function getAll(): array;

    public function setRow(array $data): void;

    public function flush(): void;
}
