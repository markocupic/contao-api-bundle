<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi\Api;

use Contao\Controller;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FrontendUser;
use Contao\Module;
use Contao\ModuleModel;
use Contao\StringUtil;
use Markocupic\ContaoContentApi\Json\ContaoJson;
use Markocupic\ContaoContentApi\Model\ApiAppModel;
use Markocupic\ContaoContentApi\Response\ResponseData\DefaultResponseData;
use Markocupic\ContaoContentApi\Util\ApiUtil;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\RequestStack;

#[AutoconfigureTag('markocupic_contao_content_api.resource', ['name' => self::NAME, 'type' => self::TYPE, 'model_class' => self::MODEL_CLASS, 'verbose_name' => self::VERBOSE_NAME])]
class ApiFrontendModule extends AbstractApi
{
    public const NAME = 'contao_frontend_module';
    public const TYPE = 'contao_frontend_module';
    public const MODEL_CLASS = ModuleModel::class;
    public const VERBOSE_NAME = 'Get the content of a Contao frontend module.';

    private string|null $strModuleClass = null;

    // Adapters
    private readonly Adapter $apiAppModel;
    private readonly Adapter $stringUtil;
    private readonly Adapter $moduleModel;
    private readonly Adapter $module;
    private readonly Adapter $controller;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly RequestStack $requestStack,
        private readonly ApiUtil $apiUtil,
    ) {
        $this->apiAppModel = $this->framework->getAdapter(ApiAppModel::class);
        $this->stringUtil = $this->framework->getAdapter(StringUtil::class);
        $this->moduleModel = $this->framework->getAdapter(ModuleModel::class);
        $this->module = $this->framework->getAdapter(Module::class);
        $this->controller = $this->framework->getAdapter(Controller::class);

        $this->initializeResponseData(new DefaultResponseData());
    }

    public function getFromId(int $id): ApiInterface
    {
        if (null === ($this->model = $this->moduleModel->findByPk($id))) {
            return $this->returnError(sprintf('No entity found for ID %s.', $id));
        }

        $this->strModuleClass = $this->module->findClass($this->model->type);

        $content = $this->controller->getFrontendModule($id);

        $this->responseData->setRow(
            [
                'id' => $id,
                'type' => $this->model->type,
                'compiledHTML' => false === $content ? null : $content,
            ]
        );

        $this->triggerApiModuleGeneratedHook();

        return $this;
    }

    public function get(string $strKey, int $id, FrontendUser|null $user): ApiInterface
    {
        if (null === ($model = $this->apiAppModel->findOneByKey($strKey))) {
            return $this->returnError('No Api App entity found.');
        }

        $resConfig = $this->apiUtil->getResourceConfigByName($model->resourceType);

        if (!$this->isAllowed($model, $id)) {
            return $this->returnError(sprintf('Access to resource with ID %s denied.', $id));
        }

        if (null === ($this->model = $resConfig['model_class']::findByPk($id))) {
            return $this->returnError(sprintf('No entity found for ID %s.', $id));
        }

        return $this->getFromId($id);
    }

    public function isAllowed(ApiAppModel $apiAppModel, int $id): bool
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
                $callback[0]::{$callback[1]}($this, $this->strModuleClass);
            }
        }
    }
}
