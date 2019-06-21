<?php

namespace Application\Service;

interface RaceConditionProtector
{
	/**
	 * @param string   $key
	 * @param callable $protectableCode
	 *
	 * @return mixed
	 */
	public function execute(string $key, callable $protectableCode);

}