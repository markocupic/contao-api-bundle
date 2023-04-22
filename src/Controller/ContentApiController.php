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

namespace Markocupic\ContaoContentApi\Controller;

use Markocupic\ContaoContentApi\Response\ContentApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/_mc_cc_api', defaults: ['_scope' => 'frontend', '_token_check' => false])]
class ContentApiController extends AbstractApiController
{
    #[Route('/{strKey}/show/{entityId}', name: 'markocupic_contao_content_api_show', methods: ['GET'])]
    public function showAction(string $strKey, string $entityId, Request $request): Response
    {
        // Initialize Contao framework
        $this->initialize();

        $entityId = (int) $entityId;

        $user = $this->security->getUser();

        if (!$this->apiResourceManager->hasValidKey($strKey)) {
            return $this->json(['message' => 'Access denied due to invalid key.']);
        }

        if (!$this->apiResourceManager->isUserAllowed($strKey, $user)) {
            return $this->json(['message' => 'Access denied due to protected resource.']);
        }

        if (null === $apiResource = $this->apiResourceManager->get($strKey)) {
            return $this->json(['message' => sprintf('Could not find any service that matches to %s key.', $strKey)]);
        }

        return new ContentApiResponse($apiResource->get($strKey, $entityId, $user));
    }
}
