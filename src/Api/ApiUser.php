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

use Contao\FrontendUser;
use Contao\MemberModel;
use Markocupic\ContaoContentApi\ContaoJson;
use Markocupic\ContaoContentApi\ContaoJsonSerializable;
use Markocupic\ContaoContentApi\User\Contao\ContaoFrontendUser;

/**
 * ApiUser::toJson() will output the frontend user (member) that is currently logged in.
 * Will return 'null' in case of error.
 */
class ApiUser implements ContaoJsonSerializable
{
    /**
     * @var ContaoFrontendUser
     */
    private $contaoFrontendUser;

    /**
     * @var FrontendUser
     */
    private $user;

    public function __construct(ContaoFrontendUser $contaoFrontendUser)
    {
        $this->contaoFrontendUser = $contaoFrontendUser;
    }

    public function toJson(): ContaoJson
    {
        $this->initialize();

        if (!$this->user || !$this->user->authenticate()) {
            return new ContaoJson(null);
        }
        $model = MemberModel::findById($this->user->id);
        $model->groups = $this->user->groups;
        $model->roles = $this->user->getRoles();
        $model->password = null;
        $model->session = null;

        return new ContaoJson($model);
    }

    private function initialize(): void
    {
        if (null !== ($this->user = $this->contaoFrontendUser->getContaoFrontendUser())) {
            \define('FE_USER_LOGGED_IN', true);
        } else {
            \define('FE_USER_LOGGED_IN', false);
        }
    }
}
