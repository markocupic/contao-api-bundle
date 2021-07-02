<?php

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi;

use Contao\PageModel;

/**
 * Sitemap represents the actual site structure as an object tree.
 * The resulting instance can be iterated and used like an array.
 */
class Sitemap implements \IteratorAggregate, \ArrayAccess, \Countable, ContaoJsonSerializable
{
	protected $sitemap = array();
	public $sitemapFlat;

	/**
	 * constructor.
	 *
	 * @param string $language If set, ignores other languages
	 * @param int    $pid      Parent ID (for recursive calls)
	 */
	public function __construct(string $language = null, $pid = null)
	{
		$this->sitemapFlat = new \stdClass();
		$pages = array();

		if (!$pid)
		{
			$pages = PageModel::findPublishedRootPages(array('order' => 'sorting ASC'));
		}
		else
		{
			$pages = PageModel::findPublishedByPid($pid, array('order' => 'sorting ASC'));
		}

		if (!$pages)
		{
			return;
		}

		foreach ($pages as $page)
		{
			$page->loadDetails();

			// Catch error:
			// Parameter "parameters" for route "cmf_routing_object" must match "/.+" ("" given) to generate a corresponding URL.
			try
			{
				$page->url = $page->getFrontendUrl();
			}
			catch (\Exception $e)
			{
				return;
			}
			$page->urlAbsolute = $page->getAbsoluteUrl();
			$subSitemap = new self($language, $page->id);

			if ($language && $page->language != $language)
			{
				continue;
			}

			if ($page->type == 'regular')
			{
				$this->sitemapFlat->{mb_strtolower($page->url)} = $page->row();
			}

			foreach ($subSitemap->sitemapFlat as $_url => $_page)
			{
				$this->sitemapFlat->{mb_strtolower($_url)} = $_page;
			}
			$page->subPages = $subSitemap;
			$this[] = $page;
		}
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->sitemap);
	}

	public function offsetExists($offset): bool
	{
		return isset($this->sitemap[$offset]);
	}

	public function offsetGet($offset): PageModel
	{
		return $this->sitemap[$offset];
	}

	public function offsetSet($offset, $value): void
	{
		if (!$offset)
		{
			$this->sitemap[] = $value;
		}
		else
		{
			$this->sitemap[$offset] = $value;
		}
	}

	public function offsetUnset($offset)
	{
		unset($this->sitemap[$offset]);
	}

	public function count(): integer
	{
		return \count($this->sitemap);
	}

	public function toJson(): ContaoJson
	{
		return new ContaoJson($this->sitemap);
	}
}
