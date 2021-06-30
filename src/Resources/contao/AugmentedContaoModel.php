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

/**
 * AugmentedContaoModel is a wrapper class to make handling Contao Models consistent.
 */
abstract class AugmentedContaoModel implements ContaoJsonSerializable
{
	public $model;

	public function toJson(): ContaoJson
	{
		if (!$this->model)
		{
			return new ContaoJson(null);
		}

		return new ContaoJson($this->model);
	}

	/**
	 * Get the value from the attached model.
	 *
	 * @param string $property key
	 */
	public function __get($property)
	{
		return $this->model->{$property} ?? null;
	}

	/**
	 * Set the value in the attached model.
	 *
	 * @param string $property key
	 * @param mixed  $value    value
	 */
	public function __set($property, $value)
	{
		if (property_exists($this, $property))
		{
			return $this;
		}
		$this->model->{$property} = $value;

		return $this;
	}
}
