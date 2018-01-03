<?php
/**
 * @package     FOF
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Tests\Utils;

use FOF30\Tests\Helpers\FOFTestCase;
use FOF30\Tests\Stubs\Utils\IpStub;

require_once 'IpDataprovider.php';

/**
 * @covers  \FOF30\Utils\Ip::<protected>
 * @covers  \FOF30\Utils\Ip::<private>
 */
class IpTest extends FOFTestCase
{
	/**
	 * @group			Ip
	 * @dataProvider    IpDataprovider::getDetectAndCleanIP
	 */
	public function testDetectAndCleanIP($test, $check)
	{
		$msg = 'Ip::detectIP %s - Case: '.$check['case'];

		$ip = new IpStub();

		$ip::$fakeIP = $test['fakeIP'];
		$ip::setUseFirstIpInChain($test['useFirst']);

		$result = $ip::detectAndCleanIP();

		$this->assertEquals($check['result'], $result, $msg);
	}
}
