<?php

declare(strict_types=1);

/*
 * This file is part of Contao Api Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-api-bundle
 */

namespace Markocupic\ContaoApiBundle\Api;

use Contao\FrontendUser;
use Markocupic\ContaoApiBundle\Json\ContaoJson;
use Markocupic\ContaoApiBundle\Model\ApiAppModel;
use Markocupic\ContaoApiBundle\Response\ResponseData\ResponseDataInterface;
use Symfony\Component\HttpFoundation\Request;

interface ApiInterface
{
    public function toJson(): ContaoJson;

    public function isAllowed(ApiAppModel $apiAppModel, int $id, Request $request): bool;

    public function get(string $apiKey, int $id, Request $request, FrontendUser|null $user): self;

    public function getFromId(int $id, Request $request): self;

    public function initializeResponseData(ResponseDataInterface $responseData): void;

    public function getResponseData(): ResponseDataInterface|null;
}
