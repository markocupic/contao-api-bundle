<?php

declare(strict_types=1);

/*
 * This file is part of Contao Api Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-api-bundle
 */

namespace Markocupic\ContaoApiBundle\Api;

use Contao\Controller;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\StringUtil;
use Markocupic\ContaoApiBundle\Json\ContaoJson;
use Markocupic\ContaoApiBundle\Manager\ApiResourceManager;
use Markocupic\ContaoApiBundle\Model\ApiAppModel;
use Markocupic\ContaoApiBundle\Response\ResponseData\DefaultResponseData;
use Markocupic\ContaoApiBundle\Util\ApiUtil;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;

#[AutoconfigureTag('markocupic_contao_api.resource', ['alias' => self::ALIAS, 'type' => self::TYPE, 'modelClass' => self::MODEL_CLASS, 'verboseName' => self::VERBOSE_NAME])]
class ApiContaoFrontendModule extends AbstractApi
{
    public const ALIAS = 'contao_frontend_module';
    public const TYPE = 'contao_frontend_module';
    public const MODEL_CLASS = ModuleModel::class;
    public const VERBOSE_NAME = 'Get the html content of a Contao frontend module.';

    // Adapters
    private readonly Adapter $apiAppModel;
    private readonly Adapter $stringUtil;
    private readonly Adapter $controller;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ApiUtil $apiUtil,
        private readonly ApiResourceManager $apiResourceManager,
    ) {
        $this->apiAppModel = $this->framework->getAdapter(ApiAppModel::class);
        $this->stringUtil = $this->framework->getAdapter(StringUtil::class);
        $this->controller = $this->framework->getAdapter(Controller::class);

        $this->initializeResponseData(new DefaultResponseData());
    }

    public function getFromId(int $id, Request $request): ApiInterface
    {
        $apiKey = $request->attributes->get('apiKey');

        $resConfig = $this->apiUtil->getApiResourceConfigurationFromApiKey($apiKey);

        if (null === $resConfig) {
            return $this->returnError(sprintf('No Api found for api key "%s".', $apiKey));
        }

        $content = $this->controller->getFrontendModule($id);

        $this->responseData->setRow(
            [
                'id' => $id,
                'type' => $resConfig['type'],
                'compiledHTML' => false === $content ? null : $content,
            ]
        );

        $this->triggerApiModuleGeneratedHook();

        return $this;
    }

    public function get(string $apiKey, int $id, Request $request, FrontendUser|null $user): ApiInterface
    {
        if (null === ($model = $this->apiAppModel->findOneByKey($apiKey))) {
            return $this->returnError(sprintf('No App configuration found for key "%s".', $apiKey));
        }

        if (null === $this->apiResourceManager->getResourceConfigByAlias($model->resourceAlias)) {
            return $this->returnError(sprintf('No Api found for alias "%s".', $model->resourceAlias));
        }

        if (!$this->isAllowed($model, $id, $request)) {
            return $this->returnError(sprintf('Access to resource with ID %s denied.', $id));
        }

        return $this->getFromId($id, $request);
    }

    public function isAllowed(ApiAppModel $apiAppModel, int $id, Request $request): bool
    {
        $arrAllowed = $this->stringUtil->deserialize($apiAppModel->allowedModules, true);

        return \in_array($id, $arrAllowed, false);
    }

    public function toJson(): ContaoJson
    {
        if (!$this->responseData) {
            return new ContaoJson(null);
        }

        return new ContaoJson($this);
    }

    private function triggerApiModuleGeneratedHook(): void
    {
        if (isset($GLOBALS['TL_HOOKS']['apiModuleGenerated']) && \is_array($GLOBALS['TL_HOOKS']['apiModuleGenerated'])) {
            foreach ($GLOBALS['TL_HOOKS']['apiModuleGenerated'] as $callback) {
                $callback[0]::{$callback[1]}($this);
            }
        }
    }
}
