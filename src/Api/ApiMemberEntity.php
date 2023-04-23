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

namespace Markocupic\ContaoContentApi\Api;

use Contao\MemberModel;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('markocupic_contao_content_api.resource', ['alias' => self::ALIAS, 'type' => self::TYPE, 'modelClass' => self::MODEL_CLASS, 'verboseName' => self::VERBOSE_NAME])]
class ApiMemberEntity extends AbstractApiEntityResource
{
    public const ALIAS = 'member';
    public const TYPE = 'contao_entity';
    public const MODEL_CLASS = MemberModel::class;
    public const VERBOSE_NAME = 'Get the content of a Contao entity.';
}
