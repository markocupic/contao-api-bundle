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

use Contao\MemberModel;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('markocupic_contao_api.resource', ['alias' => self::ALIAS, 'type' => self::TYPE, 'modelClass' => self::MODEL_CLASS, 'verboseName' => self::VERBOSE_NAME])]
class ApiMemberEntity extends AbstractApiEntity
{
    public const ALIAS = 'member';
    public const TYPE = 'contao_entity';
    public const MODEL_CLASS = MemberModel::class;
    public const VERBOSE_NAME = 'Get the content of a Contao entity.';
}
