<?php

namespace Markocupic\ContaoContentApi\Exceptions;

use Markocupic\ContaoContentApi\ContaoJsonSerializable;
use Markocupic\ContaoContentApi\ContaoJson;

/**
 * ContentApiNotFoundException is thrown whenever something is simply not there.
 * It indicates an Error 404.
 */
class ContentApiNotFoundException extends \Exception implements ContaoJsonSerializable
{
    public function toJson(): ContaoJson
    {
        return new ContaoJson([
            'error' => 'ContentApiNotFoundException',
            'message' => $this->getMessage(),
        ]);
    }
}
