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
use Contao\FrontendUser;
use Contao\Module;
use Contao\ModuleModel;
use Contao\ModuleProxy;
use Contao\StringUtil;
use Markocupic\ContaoContentApi\ContaoJson;
use Markocupic\ContaoContentApi\Model\AppModel;
use Markocupic\ContaoContentApi\Util\ApiUtil;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ApiFrontendModule.
 */
class ApiFrontendModule implements ApiInterface
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

    /**
     * @var ApiUtil
     */
    private $apiUtil;

    public function __construct(ContaoFramework $framework, RequestStack $requestStack, ApiUtil $apiUtil)
    {
        $this->framework = $framework;
        $this->requestStack = $requestStack;
        $this->apiUtil = $apiUtil;
    }

    public function show($strAlias, ?FrontendUser $user): self
    {
        /** @var AppModel $appModel */
        $appAdapter = $this->framework->getAdapter(AppModel::class);
        $appModel = $appAdapter->findOneByAlias($strAlias);

        $request = $this->requestStack->getCurrentRequest();

        if ($request->query->has('id')) {
            $id = $request->query->get('id');

            // Get config data from current resource defined in config.yml
            $configData = $this->apiUtil->getResourceConfigByName($appModel->resourceType);

            if (null !== ($this->model = $configData['modelClass']::findByPk($id))) {
                if (null !== $appModel) {
                    if (!$this->isAllowed($appModel, (int) $id)) {
                        $this->model->message = 'Access to this resource is not allowed!';
                        $this->model->compiledHTML = null;
                    } else {
                        /** @var Module $moduleAdapter */
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

    public function isAllowed(AppModel $appModel, int $id): bool
    {
        $adapter = $this->framework->getAdapter(StringUtil::class);
        $arrAllowed = $adapter->deserialize($appModel->allowedModules, true);

        return \in_array($id, $arrAllowed, false);
    }

    public function toJson(): ContaoJson
    {
        if (!$this->model) {
            return new ContaoJson(null);
        }

        return new ContaoJson($this->model);
    }
}