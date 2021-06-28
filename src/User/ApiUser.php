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

namespace Markocupic\ContaoContentApi\User;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\FrontendUser;
use Contao\MemberModel;
use Markocupic\ContaoContentApi\ContaoJson;
use Markocupic\ContaoContentApi\ContaoJsonSerializable;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

/**
 * ApiUser will output the frontend user (member) that is currently logged in.
 * Will return 'null' in case of error.
 */
class ApiUser implements ContaoJsonSerializable
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ScopeMatcher
     */
    private $scopeMatcher;

    /**
     * @var TokenChecker
     */
    private $tokenChecker;

    /**
     * @var Security
     */
    private $securityHelper;

    /**
     * @var FrontendUser
     */
    private $user;

    public function __construct(RequestStack $requestStack, ScopeMatcher $scopeMatcher, TokenChecker $tokenChecker, Security $securityHelper)
    {
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
        $this->tokenChecker = $tokenChecker;
        $this->securityHelper = $securityHelper;
    }

    public function initialize(): void
    {
        // Define the login status constants (see #4099, #5279)
        $request = $this->requestStack->getCurrentRequest();

        if ($request && $this->scopeMatcher->isFrontendRequest($request) && ($session = $this->getSession()) && $request->hasPreviousSession()) {
            $session->start();

            \define('FE_USER_LOGGED_IN', $this->tokenChecker->hasFrontendUser());

            // Get logged in member object
            if (($objUser = $this->securityHelper->getUser()) instanceof FrontendUser) {
                $this->user = $objUser;
            }
        } else {
            \define('FE_USER_LOGGED_IN', false);
        }
    }

    public function toJson(): ContaoJson
    {
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

    private function getSession(): ?SessionInterface
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request || !$request->hasSession()) {
            return null;
        }

        return $request->getSession();
    }
}
