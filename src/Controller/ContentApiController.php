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
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\System;
use Markocupic\ContaoContentApi\ContentApiResponse;
use Markocupic\ContaoContentApi\Manager\ApiResourceManager;
use Markocupic\ContaoContentApi\Sitemap;
use Markocupic\ContaoContentApi\User\Contao\ContaoFrontendUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ContentApiController provides all routes.
 *
 * @Route("/_mc_cc_api", defaults={"_scope" = "frontend", "_token_check" = false})
 */
class ContentApiController extends Controller
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var ContaoFrontendUser
     */
    private $contaoFrontendUser;

    /**
     * @var ApiResource
     */
    private $apiSelector;

    /**
     * @var null
     */
    private $lang;

    /**
     * @var string
     */
    private $headers;

    public function __construct(ContaoFramework $framework, ContaoFrontendUser $contaoFrontendUser, ApiResourceManager $apiSelector)
    {
        $this->framework = $framework;
        $this->contaoFrontendUser = $contaoFrontendUser;
        $this->apiSelector = $apiSelector;
    }

    /**
     * @param Request $request Current request
     *
     * @return Response
     *
     * @Route("/test", name="markocupic_content_api_test")
     */
    public function testAction(Request $request)
    {
        $arr = [];
        $objDB = Database::getInstance()
            ->prepare('SELECT * FROM tl_files WHERE id > 0')
            ->limit(2)
            ->execute()
        ;

        while($objDB->next())
        {
            $arr[] = ['path' => $objDB->path, 'uuid' => $objDB->uuid];
        }

        return $this->json($arr);

    }

    /**
     * @param Request $request Current request
     *
     * @return Response
     *
     * @Route("/{alias}", name="markocupic_content_api_resource")
     */
    public function resourceAction(string $alias, Request $request)
    {
        $this->init($request);

        if (null === $resource = $this->get('markocupic.content_api.manager.resource')->get($alias)) {
            return $this->json(
                ['message' => sprintf(
                    'Could not find any service that match to %s alias.',
                    $alias
                ),
                ]
            );
        }

        return new ContentApiResponse($resource->show(), 200, $this->headers);
    }

    /**
     * Called at the begin of every request.
     *
     * @param Request $request Current request
     */
    private function init(Request $request): Request
    {
        // Commit die if disabled
        if (!$this->getParameter('content_api_enabled')) {
            die('Content API is disabled');
        }

        $this->headers = $this->getParameter('content_api_headers');

        if (isset($GLOBALS['TL_HOOKS']['apiBeforeInit']) && \is_array($GLOBALS['TL_HOOKS']['apiBeforeInit'])) {
            foreach ($GLOBALS['TL_HOOKS']['apiBeforeInit'] as $callback) {
                $request = $callback[0]::{$callback[1]}($request);
            }
        }

        // Override $_SERVER['REQUEST_URI']
        $_SERVER['REQUEST_URI'] = $request->query->get('url', $_SERVER['REQUEST_URI']);

        // Set the language
        if ($request->query->has('_locale')) {
            $this->lang = $request->query->get('_locale');
        } elseif ($request->query->has('lang')) {
            $this->lang = $request->query->get('lang');
        } elseif (Config::get('addLanguageToUrl') && $request->query->has('url')) {
            $url = $request->query->get('url');

            if ('/' !== substr($url, 0, 1)) {
                $url = "/$url";
            }
            $urlParts = explode('/', $url);
            $this->lang = \count($urlParts) > 1 && 2 === \strlen($urlParts[1]) ? $urlParts[1] : null;
        }

        if (!$this->lang) {
            $sitemap = new Sitemap();

            foreach ($sitemap as $rootPage) {
                if ($rootPage->fallback) {
                    $this->lang = $rootPage->language;
                    break;
                }
            }
        }

        if ($this->lang) {
            System::loadLanguageFile('default', $this->lang);
        } else {
            // Use Contao fallback language
            System::loadLanguageFile('default', 'undefined');
        }

        // Initialize Contao
        $this->framework->initialize();

        // Define the login status constants 'FE_USER_LOGGED_IN'
        $this->contaoFrontendUser->defineLoginStatusConstants();

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
