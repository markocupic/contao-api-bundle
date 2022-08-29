<?php

declare(strict_types=1);

/*
 * This file is part of Contao Content Api.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-content-api
 */

namespace Markocupic\ContaoContentApi;

class UrlHelper
{
    public static function getUrls($file = null)
    {
        $urls = new \stdClass();

        if ($file) {
            $urls = self::parseSitemapXml("$file.xml");
        } else {
            foreach (scandir(TL_ROOT.'/web/share/') as $file) {
                if ('.xml' === substr($file, -4)) {
                    $urls = (object) array_merge(
                        (array) $urls,
                        (array) self::parseSitemapXml($file)
                    );
                }
            }
        }

        return $urls;
    }

    private static function parseSitemapXml($file)
    {
        $urls = new \stdClass();
        $filePath = TL_ROOT."/web/share/$file";
        $xml = simplexml_load_file($filePath);
        $sitemap = json_decode(json_encode($xml));

        foreach ($sitemap->url as $item) {
            $parsedUrl = parse_url($item->loc);
            $urls->{$parsedUrl['path']} = $parsedUrl;
        }

        return $urls;
    }
}
