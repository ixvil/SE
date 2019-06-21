<?php

namespace Application\Service;

use Exception;
use RuntimeException;

class FileLocksBasedProtector implements RaceConditionProtector
{
	private const LOCK_LIMIT = 1;
	private const TMP_RC_DIR = '/tmp/rc/';

	public function __construct()
	{
		if (
			!is_dir(self::TMP_RC_DIR) &&
			!mkdir(self::TMP_RC_DIR, 0755, true) &&
			!is_dir(self::TMP_RC_DIR)) {
			throw new RuntimeException(sprintf('Directory "%s" was not created', self::TMP_RC_DIR));
		}
	}

	/**
	 * @param string   $lockKey
	 * @param callable $protectableCode
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function execute(string $lockKey, callable $protectableCode)
	{
		$lockFile = fopen(self::TMP_RC_DIR . sha1($lockKey), 'arb+');
		try {
			$startMicroTime = microtime(true);
			while (!flock($lockFile, LOCK_EX)) {
				usleep(100000);
				if ($startMicroTime + self::LOCK_LIMIT < microtime(true)) {
					break;
					//Тут может быть разное поведение в зависимости от бизнес задачи.
					//В нашем случае если ресурс заблокирован дольше, чем х мы просто выполняем обычный код и снимаем блокировку
				}
			}

			return $protectableCode();
		} catch (Exception $e) {
			throw $e;
		}
		finally {
			flock($lockFile, LOCK_UN);
			fclose($lockFile);
		}

	}

}