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

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FrontendUser;
use Contao\StringUtil;
use Markocupic\ContaoContentApi\Model\ApiAppModel;

/**
 * Trait ApiTrait.
 */
trait ApiTrait
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    public function isMemberAllowed(ApiAppModel $apiAppModel, ?FrontendUser $user): bool
    {
        if ($apiAppModel->mProtect) {
            if (!$user) {
                return false;
            }

            $arrMemberGroups = StringUtil::deserialize($user->groups, true);
            $arrAppGroups = StringUtil::deserialize($apiAppModel->mGroups, true);

            return array_intersect($arrAppGroups, $arrMemberGroups) ? true : false;

        }

        return true;
    }
}
