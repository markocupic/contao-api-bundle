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

interface ResponseDataInterface
{
    public function set(string $key, mixed $value): void;

    public function get(string $key): mixed;

    public function getAll(): array;

    public function setRow(array $data): void;

    public function flush(): void;
}
