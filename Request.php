<?php


namespace Application\Adapter;

class Request
{
	/**
	 * @var int
	 */
	private $field1;
	/**
	 * @var string
	 */
	private $field2;

	/**
	 * @param mixed $field1
	 *
	 * @return Request
	 */
	public function setField1(int $field1): Request
	{
		$this->field1 = $field1;

		return $this;
	}

	/**
	 * @param mixed $field2
	 *
	 * @return Request
	 */
	public function setField2(string $field2): Request
	{
		$this->field2 = $field2;

		return $this;
	}

}