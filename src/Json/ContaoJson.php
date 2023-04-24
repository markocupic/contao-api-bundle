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

namespace Markocupic\ContaoApiBundle\Json;

use Contao\Model;
use Contao\Model\Collection;
use Contao\StringUtil;
use Contao\System;
use Contao\UserModel;
use Contao\Validator;
use Markocupic\ContaoApiBundle\Api\ApiInterface;

/**
 * ContaoJson tries to pack "everything Contao" into a JSON-serializable package.
 *
 * It works with:
 *  - Contao Collections
 *  - Contao Models
 *  - Arrays (of Models or anything else)
 *  - Objects
 *  - Strings and numbers
 * The main features are
 *  - File objects (e.g. singleSRC) are resolved automatically
 *  - Serialized arrays are resolved automatically
 *  - HTML will be unescaped automatically
 *  - Contao Insert-Tags are resolved automatically
 * ContaoJson will recursively call itself until all fields are resolved.
 */
class ContaoJson implements \JsonSerializable
{
    public mixed $data;
    private array|null $allowedFields;

    public function __construct(mixed $data, array $allowedFields = null)
    {
        $this->allowedFields = $allowedFields;
        $doHandle = true;

        if (isset($GLOBALS['TL_HOOKS']['apiContaoJson']) && \is_array($GLOBALS['TL_HOOKS']['apiContaoJson'])) {
            foreach ($GLOBALS['TL_HOOKS']['apiContaoJson'] as $callback) {
                $doHandle = $callback[0]::{$callback[1]}($this, $data);
            }
        }

        if (!$doHandle) {
            return;
        }

        if ($data instanceof ApiInterface) {
            $data = new self($data->getResponseData()->getAll());
        }

        if ($data instanceof self) {
            return $this->data = $data;
        }

        if ($data instanceof Collection) {
            $data = $this->handleCollection($data);
        }

        if ($data instanceof Model) {
            $data = $data->row();
        }

        if (\is_array($data)) {
            if ($this->isAssoc($data)) {
                $data = (object) $data;
            } else {
                $data = $this->handleArray($data);
            }
        }

        if (\is_object($data)) {
            $data = $this->handleObject($data);
        }

        if (is_numeric($data)) {
            $data = $this->handleNumber($data);
        }

        if (\is_string($data)) {
            $data = $this->handleString($data);
        }

        $this->data = $data;
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    private function handleCollection(Collection $collection): array
    {
        $data = [];

        foreach ($collection->getModels() as $model) {
            $data[] = $model->row();
        }

        return $data;
    }

    private function handleArray(array $array): array
    {
        $data = [];

        foreach ($array as $item) {
            $data[] = new self($item, $this->allowedFields);
        }

        return $data;
    }

    private function handleObject(object $object): \stdClass
    {
        $data = new \stdClass();

        foreach ($object as $key => $value) {
            if ($this->allowedFields && !\in_array($key, $this->allowedFields, true)) {
                unset($object->{$key});
                continue;
            }

            if ((str_contains($key, 'SRC') || 'pageImage' === $key) && $value) {
                $src = $this->deserialize($value);

                if (\is_array($src)) {
                    $files = [];

                    foreach ($src as $_val) {
                        $files[] = Validator::isBinaryUuid($_val) ? StringUtil::binToUuid($_val) : $_val;
                    }
                    $data->{$key} = $files;
                } else {
                    $data->{$key} = Validator::isBinaryUuid($src) ? StringUtil::binToUuid($src) : $src;
                }
            } elseif ('author' === $key && is_numeric($value)) {
                if (null !== ($_user = UserModel::findByPk($value))) {
                    $data->{$key} = new self($_user->row());
                } else {
                    $data->{$key} = $value;
                }
            } else {
                $data->{$key} = new self($value);
            }
        }

        return $data;
    }

    private function handleNumber($number): mixed
    {
        return $number ?? 0;
    }

    private function handleString(string $string): self|string
    {
        if (Validator::isBinaryUuid($string)) {
            $string = StringUtil::binToUuid($string);
        }

        // Fix binary or otherwise "broken" strings
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        $deserialized = $this->deserialize($string);

        if (!\is_string($deserialized)) {
            return new self($deserialized);
        }

        $string = System::getContainer()->get('contao.insert_tag.parser')->replaceInline($string);
        $string = trim($string);
        $string = preg_replace('/[[:blank:]]+/', ' ', $string);

        return StringUtil::decodeEntities($string, ENT_HTML5, 'UTF-8');
    }

    private function isAssoc(array $arr): array|bool
    {
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, \count($arr) - 1);
    }

    private function deserialize(string $string): mixed
    {
        $deserialized = @unserialize($string);

        if (false !== $deserialized) {
            if ($this->isAssoc($deserialized)) {
                return (object) $deserialized;
            }

            return $deserialized;
        }

        return $string;
    }
}
