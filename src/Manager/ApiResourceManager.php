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

namespace Markocupic\ContaoContentApi\Manager;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FrontendUser;
use Markocupic\ContaoContentApi\Api\ApiInterface;
use Markocupic\ContaoContentApi\Model\ApiAppModel;
use Markocupic\ContaoContentApi\Util\ApiUtil;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiResourceManager
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
     * @var ApiUtil
     */
    private $apiUtil;

    private $resources = [];

    private $services = [];

    public function __construct(ContaoFramework $framework, RequestStack $requestStack, ApiUtil $apiUtil)
    {
        $this->framework = $framework;
        $this->requestStack = $requestStack;
        $this->apiUtil = $apiUtil;
    }

    /**
     * Add a resource for given alias.
     *
     * @param ResourceInterface $resource
     */
    public function add($resource, string $alias, string $id): void
    {
        $this->resources[$alias] = $resource;
        $this->services[$alias] = $id;
    }

    public function get(string $strAlias, ?FrontendUser $user): ?ApiInterface
    {
        $appAdapter = $this->framework->getAdapter(ApiAppModel::class);

        if (null !== ($apiAppModel = $appAdapter->findOneByAlias($strAlias))) {
            if (null !== ($resConfig = $this->apiUtil->getResourceConfigByName($apiAppModel->resourceType))) {
                if (null === ($resource = $this->resources[$resConfig['type']])) {
                    throw new \Exception(sprintf('Resource "%s" not found.', $resConfig['type']));
                }

                return $resource;
            }
        }

        return null;
    }
}
