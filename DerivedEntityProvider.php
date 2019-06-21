<?php

namespace Application\Provider;

use Application\Adapter\Adapter;
use Application\Adapter\RequestFactory;
use Application\Service\RaceConditionProtector;
use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class DerivedEntityProvider
{
	/**
	 * @var CacheItemPoolInterface
	 */
	public $cache;
	/**
	 * @var LoggerInterface
	 */
	public $logger;
	/**
	 * @var Adapter
	 */
	private $adapter;
	/**
	 * @var RequestFactory
	 */
	private $requestFactory;
	/**
	 * @var RaceConditionProtector
	 */
	private $raceConditionProtector;

	public function __construct(
		Adapter $adapter,
		CacheItemPoolInterface $cache,
		LoggerInterface $logger,
		RequestFactory $requestFactory,
		RaceConditionProtector $raceConditionProtector
	) {
		$this->cache = $cache;
		$this->adapter = $adapter;
		$this->logger = $logger;
		$this->requestFactory = $requestFactory;
		$this->raceConditionProtector = $raceConditionProtector;
	}

	/**
	 * @param array $input
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getResponse(array $input): array
	{
		try {
			$cacheKey = $this->getCacheKey($input);
			return $this->raceConditionProtector->execute($cacheKey, function () use ($cacheKey, $input) {

				$cacheItem = $this->cache->getItem($cacheKey);
				if ($cacheItem->isHit()) {
					return $cacheItem->get();
				}

				$request = $this->requestFactory->createRequest($input);
				$result = $this->adapter->get($request);

				if ($result !== []) {
					$cacheItem
						->set($result)
						->expiresAt(
							(new DateTime())->modify('+1 day')
						);

					$this->cache->save($cacheItem);
				}

				return $result;

			});

		} catch (Exception $e) {
			$this->logger->critical($e->getMessage(), ['input' => $input]);
			throw $e;
		}
	}

	public function getCacheKey(array $input): string
	{
		return md5(json_encode($input));
	}
}