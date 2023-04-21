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
use Contao\UserModel;
use Markocupic\ContaoContentApi\ContaoJson;
use Markocupic\ContaoContentApi\Model\ApiAppModel;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * ApiContentElement augments ArticleModel for the API.
 */
class ApiAuthor
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    private $requestStack;

    public function __construct(ContaoFramework $framework, RequestStack $requestStack)
    {
        $this->framework = $framework;
        $this->requestStack = $requestStack;
    }

    public function getFromId(int $id): self
    {
        $this->model = UserModel::findById($id, ['disable'], ['']);

        if (!$this->model) {
            $this->model = null;
        }
    }

    public function get(string $stringAlias, FrontendUser|null $user): self
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request->query->has('id')) {
            throw new Exception('No id detected in the request query.');
        }

        $id = (int) $request->query->get('id');

        return $this->getFromId($id);
    }

    public function toJson(): ContaoJson
    {
        if (!$this->model) {
            return parent::toJson();
        }
        $author = $this->model->row();
        unset($author['id'], $author['tstamp'], $author['backendTheme'], $author['themes'], $author['imageSizes'], $author['fullscreen'], $author['uploader'], $author['showHelp'], $author['thumbnails'], $author['useRTE'], $author['useCE'], $author['password'], $author['pwChange'], $author['admin'], $author['groups'], $author['inherit'], $author['modules'], $author['pagemounts'], $author['alpty'], $author['filemounts'], $author['fop'], $author['forms'], $author['formp'], $author['amg'], $author['disable'], $author['start'], $author['stop'], $author['session'], $author['new_records'], $author['tl_page_tree'], $author['tl_page_node'], $author['fieldset_states'], $author['tl_image_size'], $author['tl_page'], $author['tl_user'], $author['tl_settings'], $author['tl_news_archive'], $author['tl_article_tl_page_tree'], $author['filetree'], $author['dateAdded'], $author['secret'], $author['useTwoFactor'], $author['lastLogin'], $author['currentLogin'], $author['locked'], $author['news'], $author['newp'], $author['newsfeeds'], $author['newsfeedp'], $author['trustedTokenVersion'], $author['backupCodes'], $author['loginAttempts'], $author['fields'], $author['elements']);

        return new ContaoJson($author);
    }

    public function isAllowed(ApiAppModel $apiModel, int $id): bool
    {
        return true;
    }
}
