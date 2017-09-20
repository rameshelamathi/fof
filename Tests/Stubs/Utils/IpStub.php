<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Tests\Stubs\Utils;

use FOF30\Utils\Ip;

class IpStub extends Ip
{
	public static $fakeIP = null;

    protected static function detectIP()
	{
		if (!is_null(static::$fakeIP))
		{
			return static::$fakeIP;
		}

		return parent::detectIP();
	}

	public static function detectAndCleanIP()
	{
		return parent::detectAndCleanIP();
	}
}