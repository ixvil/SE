<?php

namespace Application\Adapter;

class PartnerNameAdapter implements Adapter
{
	private $host;
	private $user;
	private $password;

	/**
	 * @param $host
	 * @param $user
	 * @param $password
	 */
	public function __construct($host, $user, $password)
	{
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return array
	 */
	public function get(Request $request): array
	{
		// returns a response from external service
	}
}