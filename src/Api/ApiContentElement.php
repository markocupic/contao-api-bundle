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

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\Controller;
use Contao\FormFieldModel;
use Contao\FormModel;
use Markocupic\ContaoContentApi\AugmentedContaoModel;

/**
 * ApiContentElement augments ContentModel for the API.
 */
class ApiContentElement extends AugmentedContaoModel
{
    /**
     * @param int    $id       content model id
     * @param string $inColumn Column the content element resides in
     */
    public function __construct(ApiResource $apiResource, string $inColumn = 'main')
    {
        //$this->model = ContentModel::findById($id, ['published'], ['1']);

        if (!$this->model || !Controller::isVisibleElement($this->model)) {
            return $this->model = null;
        }

        $this->compiledHtml = null;

        $ceClass = 'Contao\Content'.ucfirst($this->model->type);

        if (class_exists($ceClass)) {
            try {
                $compiled = new $ceClass($this->model, $inColumn);
                $this->compiledHtml = $compiled->generate();
            } catch (\Exception $e) {
            }
        }

        if ('module' === $this->type) {
            $contentModuleClass = ContentElement::findClass($this->type);
            $element = new $contentModuleClass($this->model, $inColumn);
            $this->subModule = new ApiModule((int) $element->module);
        }

        if ('form' === $this->type) {
            $formModel = FormModel::findById($this->form);

            if ($formModel) {
                $formModel->fields = FormFieldModel::findPublishedByPid($formModel->id);
            }
            $this->subForm = $formModel;
        }
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
}
