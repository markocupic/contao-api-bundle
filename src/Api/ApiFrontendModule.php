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
use Contao\Module;
use Contao\ModuleModel;
use Contao\ModuleProxy;
use Contao\StringUtil;
use Markocupic\ContaoContentApi\ContaoJson;
use Markocupic\ContaoContentApi\ContaoJsonSerializable;
use Markocupic\ContaoContentApi\Manager\ApiResourceManager;
use Markocupic\ContaoContentApi\Model\ApiModel;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ApiFrontendModule.
 */
class ApiFrontendModule implements ContaoJsonSerializable
{
    /**
     * @var ModuleModel|null
     */
    public $model;

    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(ContaoFramework $framework, RequestStack $requestStack)
    {
        $this->framework = $framework;
        $this->requestStack = $requestStack;
    }

    public function get(ApiResourceManager $apiResource): self
    {
        /** @var ApiModel $apiModel */
        $apiModel = $apiResource->getApiModel();

        $request = $this->requestStack->getCurrentRequest();

        if ($request->query->has('id')) {
            $id = $request->query->get('id');

            if (null !== $apiModel) {
                $stringUtilAdapter = $this->framework->getAdapter(StringUtil::class);
                $arrAllowedModules = $stringUtilAdapter->deserialize($apiModel->allowedModules, true);

                if (\in_array($id, $arrAllowedModules, false)) {
                    if (null !== ($this->model = ModuleModel::findByPk($id))) {
                        $moduleAdapter = $this->framework->getAdapter(Module::class);
                        $moduleClass = $moduleAdapter->findClass($this->model->type);

                        try {
                            $strColumn = null;

                            // Add compatibility to new front end module fragments
                            if (ModuleProxy::class === $moduleClass) {
                                $strColumn = 'main';
                            }

                            $module = new $moduleClass($this->model, $strColumn);
                            $this->model->compiledHTML = @$module->generate() ?? null;

                        } catch (\Exception $e) {
                            $this->model->compiledHTML = null;
                        }
                    }
                }
            }
        }

        if (isset($GLOBALS['TL_HOOKS']['apiModuleGenerated']) && \is_array($GLOBALS['TL_HOOKS']['apiModuleGenerated'])) {
            foreach ($GLOBALS['TL_HOOKS']['apiModuleGenerated'] as $callback) {
                $callback[0]::{$callback[1]}($this, $moduleClass);
            }
        }

        return $this;
    }

    public function toJson(): ContaoJson
    {
        if (!$this->model) {
            return new ContaoJson(null);
        }

        return new ContaoJson($this->model);
    }
}
