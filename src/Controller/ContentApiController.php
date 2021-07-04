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

namespace Markocupic\ContaoContentApi\Controller;

use Contao\Config;
use Contao\System;
use Markocupic\ContaoContentApi\ContentApiResponse;
use Markocupic\ContaoContentApi\Manager\ApiResourceManager;
use Markocupic\ContaoContentApi\Sitemap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ContentApiController provides all routes.
 *
 * @Route("/_mc_cc_api", defaults={"_scope" = "frontend", "_token_check" = false})
 */
class ContentApiController extends AbstractController
{
    

    /**
     * @Route("/show/{strKey}", name="markocupic_contao_content_api_show")
     */
    public function showAction(string $strKey, Request $request): Response
    {
        $this->init($request);

        $user = $this->container->get('security.helper')->getUser();

        /** @var ApiResourceManager $manager */
        $manager = $this->container->get('markocupic_contao_content_api.manager.resource');

        if (!$manager->hasValidKey($strKey)) {
            return $this->json(['message' => 'Access denied due to invalid key.']);
        }

        if (!$manager->isUserAllowed($strKey, $user)) {
            return $this->json(['message' => 'Access denied due protected resource.']);
        }

        if (null === $resource = $manager->get($strKey, $user)) {
            return $this->json(['message' => sprintf('Could not find any service that match to %s key.', $strKey)]);
        }

        return new ContentApiResponse($resource->show($strKey, $user));
    }

    /**
     * Called at the begin of every request.
     */
    private function init(Request $request): Request
    {
        // Commit die if disabled
        $config = $this->container->getParameter('markocupic_contao_content_api');

        if (!$config['enabled']) {
            die('Content API is disabled');
        }

        if (isset($GLOBALS['TL_HOOKS']['apiBeforeInit']) && \is_array($GLOBALS['TL_HOOKS']['apiBeforeInit'])) {
            foreach ($GLOBALS['TL_HOOKS']['apiBeforeInit'] as $callback) {
                $request = $callback[0]::{$callback[1]}($request);
            }
        }

        // Override $_SERVER['REQUEST_URI']
        $_SERVER['REQUEST_URI'] = $request->query->get('url', $_SERVER['REQUEST_URI']);

        // Set the language
        if ($request->query->has('_locale')) {
            $lang = $request->query->get('_locale');
        } elseif ($request->query->has('lang')) {
            $lang = $request->query->get('lang');
        } elseif (Config::get('addLanguageToUrl') && $request->query->has('url')) {
            $url = $request->query->get('url');

            if ('/' !== substr($url, 0, 1)) {
                $url = "/$url";
            }
            $urlParts = explode('/', $url);
            $lang = \count($urlParts) > 1 && 2 === \strlen($urlParts[1]) ? $urlParts[1] : null;
        }

        if (!$lang) {
            $sitemap = new Sitemap();

            foreach ($sitemap as $rootPage) {
                if ($rootPage->fallback) {
                    $lang = $rootPage->language;
                    break;
                }
            }
        }

        if ($lang) {
            System::loadLanguageFile('default', $lang);
        } else {
            // Use Contao fallback language
            System::loadLanguageFile('default', 'undefined');
        }

        // Initialize Contao
        $this->container->get('contao.framework')->initialize();

        // Define the login status constants 'FE_USER_LOGGED_IN'
        $this->container
            ->get('markocupic_contao_content_api.user.contao.frontend')
            ->defineLoginStatusConstants()
        ;

        if (!\defined('BE_USER_LOGGED_IN')) {
            \define('BE_USER_LOGGED_IN', false);
        }

        if (isset($GLOBALS['TL_HOOKS']['apiAfterInit']) && \is_array($GLOBALS['TL_HOOKS']['apiAfterInit'])) {
            foreach ($GLOBALS['TL_HOOKS']['apiAfterInit'] as $callback) {
                $request = $callback[0]::$callback[1]($request);
            }
        }

        return $request;
    }
}
