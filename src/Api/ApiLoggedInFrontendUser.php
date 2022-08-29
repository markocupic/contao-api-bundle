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
use Contao\MemberModel;
use Markocupic\ContaoContentApi\ContaoJson;
use Markocupic\ContaoContentApi\Model\ApiAppModel;

/**
 * ApiLoggedInFrontendUser::toJson() will output the frontend user (member) that is currently logged in.
 * Will return 'null' in case of error.
 */
class ApiLoggedInFrontendUser
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var FrontendUser
     */
    private $user;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    public function get(string $strKey, FrontendUser|null $user): self
    {
        $id = (int) $this->user->id;

        return $this->getFromId($id);
    }

    public function getFromId(int $id): self
    {
        if ((int) $this->user->id !== $id) {
            throw new Exception('Access to this user denied.');
        }

        return $this;
    }

    public function toJson(): ContaoJson
    {
        if (!$this->user) {
            return new ContaoJson(null);
        }

        $memberModel = $this->framework->getAdapter(MemberModel::class);

        $model = $memberModel->findById($this->user->id);
        $model->groups = $this->user->groups;
        $model->roles = $this->user->getRoles();
        $model->password = null;
        $model->session = null;

        return new ContaoJson($model);
    }

    public function isAllowed(ApiAppModel $apiModel, int $id): bool
    {
        return true;
    }
}
