<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-api-bundle
 */

namespace Markocupic\ContaoApiBundle\Controller;

use Contao\CoreBundle\Exception\ResponseException;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\System;
use Markocupic\ContaoApiBundle\Manager\ApiResourceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

abstract class AbstractApiController extends AbstractController
{
    protected Adapter $system;

    public function __construct(
        protected readonly ContaoFramework $framework,
        protected readonly RequestStack $requestStack,
        protected readonly ScopeMatcher $scopeMatcher,
        protected readonly Security $security,
        protected readonly ApiResourceManager $apiResourceManager,
        protected readonly bool $contaoApiEnabled,
    ) {
        $this->system = $this->framework->getAdapter(System::class);
    }

    protected function initialize(): void
    {
        if (!$this->contaoApiEnabled) {
            $response = new Response('Content API is disabled!');

            throw new ResponseException($response);
        }

        $request = $this->requestStack->getCurrentRequest();

        if (isset($GLOBALS['TL_HOOKS']['apiBeforeInit']) && \is_array($GLOBALS['TL_HOOKS']['apiBeforeInit'])) {
            foreach ($GLOBALS['TL_HOOKS']['apiBeforeInit'] as $callback) {
                $callback[0]::{$callback[1]}($request);
            }
        }

        $this->framework->initialize($this->scopeMatcher->isFrontendRequest($request));

        $this->loadLanguageFiles();

        if (isset($GLOBALS['TL_HOOKS']['apiAfterInit']) && \is_array($GLOBALS['TL_HOOKS']['apiAfterInit'])) {
            foreach ($GLOBALS['TL_HOOKS']['apiAfterInit'] as $callback) {
                $callback[0]::$callback[1]($request);
            }
        }
    }

    private function loadLanguageFiles(): void
    {
        $this->system->loadLanguageFile('default');
        $this->system->loadLanguageFile('modules');
        $this->system->loadLanguageFile('explain');
        $this->system->loadLanguageFile('exception');
        $this->system->loadLanguageFile('tl_content');
        $this->system->loadLanguageFile('tl_form');
        $this->system->loadLanguageFile('tl_form_field');
    }
}
