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

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FrontendUser;
use Contao\Module;
use Contao\ModuleModel;
use Contao\ModuleProxy;
use Contao\StringUtil;
use Markocupic\ContaoContentApi\ContaoJson;
use Markocupic\ContaoContentApi\Model\ApiAppModel;
use Markocupic\ContaoContentApi\Util\ApiUtil;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ApiFrontendModule.
 */
class ApiFrontendModule extends AbstractApi
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

    /**
     * @var string
     */
    private $strModuleClass;

    public function __construct(ContaoFramework $framework, RequestStack $requestStack, ApiUtil $apiUtil)
    {
        $this->framework = $framework;
        $this->requestStack = $requestStack;
        $this->apiUtil = $apiUtil;
    }

    public function getFromId(int $id): ApiInterface
    {
        if (null === ($this->model = ModuleModel::findByPk($id))) {
            return $this->returnError(sprintf('Not entity found for ID %s.', $id));
        }

        /** @var Module $moduleAdapter */
        $moduleAdapter = $this->framework->getAdapter(Module::class);
        $this->strModuleClass = $moduleAdapter->findClass($this->model->type);

        try {
            $strColumn = null;

            // Add compatibility to new front end module fragments
            if (ModuleProxy::class === $this->strModuleClass) {
                $strColumn = 'main';
            }

            // Clean module object
            $module = new $this->strModuleClass($this->model, $strColumn);
            $arrData = [
                'id' => $id,
                'type' => $this->model->type,
                'compiledHTML' => @$module->generate() ?? null,
            ];
            $this->model->setRow($arrData);
        } catch (\Exception $e) {
            return $this->returnError('Compiling error.');
        }

        $this->triggerApiModuleGeneratedHook();

        return $this;
    }

    public function get($strKey, FrontendUser|null $user): ApiInterface
    {
        /** @var ApiAppModel $apiAppModel */
        $appAdapter = $this->framework->getAdapter(ApiAppModel::class);

        if (null === ($apiAppModel = $appAdapter->findOneByKey($strKey))) {
            return $this->returnError('No Api App entity found.');
        }

        $resConfig = $this->apiUtil->getResourceConfigByName($apiAppModel->resourceType);

        $request = $this->requestStack->getCurrentRequest();

        if (!$request->query->has('id')) {
            return $this->returnError('No id detected in the request query.');
        }

        $id = (int) $request->query->get('id');

        if (!$this->isAllowed($apiAppModel, $id)) {
            return $this->returnError(sprintf('Access to resource with ID %s denied.', $id));
        }

        if (null === ($this->model = $resConfig['modelClass']::findByPk($id))) {
            return $this->returnError(sprintf('Not entity found for ID %s.', $id));
        }

        return $this->getFromId($id);
    }

    public function isAllowed(ApiAppModel $apiAppModel, int $id): bool
    {
        $adapter = $this->framework->getAdapter(StringUtil::class);
        $arrAllowed = $adapter->deserialize($apiAppModel->allowedModules, true);

        return \in_array($id, $arrAllowed, false);
    }

    public function toJson(): ContaoJson
    {
        if (!$this->model) {
            return new ContaoJson(null);
        }

        return new ContaoJson($this->model);
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
