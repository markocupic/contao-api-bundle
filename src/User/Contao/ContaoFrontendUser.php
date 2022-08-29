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

namespace Markocupic\ContaoContentApi\User\Contao;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\FrontendUser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class ContaoFrontendUser.
 */
class ContaoFrontendUser
{
    /**
     * @var ContaoFramework
     */
    private $framework;

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

    public function __construct(ContaoFramework $framework, RequestStack $requestStack, ScopeMatcher $scopeMatcher, TokenChecker $tokenChecker, Security $securityHelper)
    {
        $this->framework = $framework;
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
        $this->tokenChecker = $tokenChecker;
        $this->securityHelper = $securityHelper;
    }

    public function getContaoFrontendUser(): FrontendUser|null
    {
        $this->framework->initialize();

        // Define the login status constants (see #4099, #5279)
        $request = $this->requestStack->getCurrentRequest();

        if ($request && $this->scopeMatcher->isFrontendRequest($request) && ($session = $this->getSession()) && $request->hasPreviousSession()) {
            $session->start();

            // Get logged in member object
            if (($objUser = $this->securityHelper->getUser()) instanceof FrontendUser) {
                $this->user = $objUser;
            }
        }

        return $this->user;
    }

    public function defineLoginStatusConstants(): void
    {
        if (null !== $this->getContaoFrontendUser()) {
            if (!\defined('FE_USER_LOGGED_IN')) {
                \define('FE_USER_LOGGED_IN', true);
            }
        } else {
            if (!\defined('FE_USER_LOGGED_IN')) {
                \define('FE_USER_LOGGED_IN', false);
            }
        }
    }

    private function getSession(): SessionInterface|null
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request || !$request->hasSession()) {
            return null;
        }

        return $request->getSession();
    }
}
