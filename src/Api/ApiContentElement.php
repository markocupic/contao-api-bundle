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

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\FrontendUser;
use Markocupic\ContaoContentApi\ContaoJson;
use Markocupic\ContaoContentApi\Model\ApiAppModel;
use Markocupic\ContaoContentApi\Util\ApiUtil;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ApiContentElement.
 */
class ApiContentElement extends AbstractApi
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
     * @var ApiFrontendModule
     */
    private $apiFrontendModule;

    private $subModule;

    /**
     * @var FormModel
     */
    private $subForm;

    public function __construct(ContaoFramework $framework, RequestStack $requestStack, ApiUtil $apiUtil, ApiFrontendModule $apiFrontendModule)
    {
        $this->framework = $framework;
        $this->requestStack = $requestStack;
        $this->apiUtil = $apiUtil;
        $this->apiFrontendModule = $apiFrontendModule;
    }

    public function getFromId(int $id): ApiInterface
    {
        $inColumn = 'main';

        $this->model = ContentModel::findById($id, ['published'], ['1']);

        if (!$this->model || !Controller::isVisibleElement($this->model)) {
            $this->returnError(sprintf('Content element with ID %s not found or not published', $id));

            return $this;
        }

        $ceClass = 'Contao\Content'.ucfirst($this->model->type);

        if (class_exists($ceClass)) {
            try {
                $compiled = new $ceClass($this->model, $inColumn);
                $this->model->compiledHTML = $compiled->generate();
            } catch (\Exception $e) {
                $this->returnError('Compiling error.');
            }
        }

        if ('module' === $this->model->type) {
            die(print_r($this->model, true));
            $contentModuleClass = ContentElement::findClass($this->type);
            $element = new $contentModuleClass($this->model->module, $inColumn);
            $this->model->compiledHTML = $this->apiFrontendModule->getFromId($this->model->module);
            die($this->model->compiledHTML);
        }

        if ('form' === $this->type) {
            $formModel = FormModel::findById($this->form);

            if ($formModel) {
                $formModel->fields = FormFieldModel::findPublishedByPid($formModel->id);
            }
            $this->subForm = $formModel;
        }

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

    /**
     * Select by parent id and table.
     *
     * @param $pid
     * @param string $inColumn Column the Content Elements resides in
     *
     * @return array
     */
    public static function findByPidAndTable($pid, string $table = 'tl_article', string $inColumn = 'main')
    {
        $contents = [];
        $contentModels = ContentModel::findPublishedByPidAndTable($pid, $table, ['order' => 'sorting ASC']);

        if (!$contentModels) {
            return $contents;
        }

        foreach ($contentModels as $content) {
            if (!Controller::isVisibleElement($content)) {
                continue;
            }
            $contents[] = new self((int) $content->id, $inColumn);
        }

        return $contents;
    }

    /**
     * Does this content element has a reader module?
     *
     * @param string $readerType What kind of reader? e.g. 'newsreader'
     */
    public function hasReader($readerType): bool
    {
        return $this->subModule && $this->subModule->type === $readerType;
    }

    public function isAllowed(ApiAppModel $apiAppModel, int $id): bool
    {
        $arrAllowed = explode(',', $apiAppModel->allowedContentElements);

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
