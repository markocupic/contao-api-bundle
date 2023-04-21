<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi;

/**
 * SitemapFlat represents the actual site structure as a key value object.
 * Key: URL of the page
 * Value: PageModel.
 */
class SitemapFlat implements ContaoJsonSerializable
{
    public $sitemap;

    /**
     * constructor.
     *
     * @param string $language If set, ignores other languages
     */
    public function __construct(string $language = null)
    {
        $sitemap = new Sitemap($language);
        $this->sitemap = $sitemap->sitemapFlat;
    }

    public function findUrl($url, $exactMatch = true)
    {
        if ('/' === substr($url, 0, 1)) {
            $url = substr($url, 1);
        }
        $url = mb_strtolower($url);
        $page = $this->sitemap->{$url} ?? null;

        if ($page) {
            $page['exactUrlMatch'] = true;

            return $page;
        }

        if ($exactMatch) {
            return null;
        }
        $matches = [];

        foreach (array_keys($this->sitemap) as $_url) {
            if (substr($url, 0, \strlen($_url)) === $_url) {
                $matches[$_url] = \strlen($_url);
            }
        }

        if ($matches) {
            arsort($matches);

            return $this->sitemap->{key($matches)};
        }

        return null;
    }

    public function toJson(): ContaoJson
    {
        return new ContaoJson($this->sitemap);
    }
}
