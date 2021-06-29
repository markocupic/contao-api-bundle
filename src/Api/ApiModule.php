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

use Contao\Module;
use Contao\ModuleModel;
use Contao\ModuleProxy;
use Markocupic\ContaoContentApi\AugmentedContaoModel;

/**
 * Class ApiModule.
 */
class ApiModule extends AugmentedContaoModel
{
    /**
     * @var ModuleModel|null
     */
    public $model;

    /**
     * ApiModule constructor.
     */
    public function __construct(int $id)
    {
        $this->model = ModuleModel::findByPk($id);

        $moduleClass = Module::findClass($this->model->type);

        try {
            $strColumn = null;

            // Add compatibility to new front end module fragments
            if (ModuleProxy::class === $moduleClass) {
                $strColumn = 'main';
            }

            $module = new $moduleClass($this->model, $strColumn);
            $this->compiledHTML = @$module->generate() ?? null;
        } catch (\Exception $e) {
            $this->compiledHTML = null;
        }

        if (isset($GLOBALS['TL_HOOKS']['apiModuleGenerated']) && \is_array($GLOBALS['TL_HOOKS']['apiModuleGenerated'])) {
            foreach ($GLOBALS['TL_HOOKS']['apiModuleGenerated'] as $callback) {
                $callback[0]::{$callback[1]}($this, $moduleClass);
            }
        }
    }
}
