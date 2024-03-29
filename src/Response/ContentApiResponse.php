<?php

declare(strict_types=1);

/*
 * This file is part of Contao Api Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-api-bundle
 */

namespace Markocupic\ContaoApiBundle\Response;

use Markocupic\ContaoApiBundle\Api\ApiInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ContentApiResponse represents all responses from the API.
 */
class ContentApiResponse extends JsonResponse
{
    /**
     * constructor.
     *
     * @param mixed $data    any data (object, array, or ContaoJson)
     * @param int   $status  Status code
     * @param array $headers Additional headers
     */
    public function __construct($data, int $status = 200, array $headers = [])
    {
        if (isset($GLOBALS['TL_HOOKS']['apiResponse']) && \is_array($GLOBALS['TL_HOOKS']['apiResponse'])) {
            foreach ($GLOBALS['TL_HOOKS']['apiResponse'] as $callback) {
                $data = $callback[0]::{$callback[1]}($data);
            }
        }

        if (\is_string($data)) {
            $data = ['message' => $data];
        }
        $data = $data instanceof ApiInterface ? $data->toJson() : $data;
        parent::__construct($data, $status, $headers);
        $this->setEncodingOptions(JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
