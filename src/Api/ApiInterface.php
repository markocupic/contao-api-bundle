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

namespace Markocupic\ContaoContentApi\Api;

use Markocupic\ContaoContentApi\ContaoJson;
use Markocupic\ContaoContentApi\Manager\ApiResourceManager;
use Markocupic\ContaoContentApi\Model\AppModel;

/**
 * Interface ApiInterface.
 */
interface ApiInterface
{
    public function toJson(): ContaoJson;

    public function isAllowed(AppModel $apiModel, int $id): bool;

    public function show(string $stringAlias): self;
}
