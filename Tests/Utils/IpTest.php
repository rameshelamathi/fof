<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Tests\Utils;

use FOF30\Tests\Helpers\FOFTestCase;

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
		$this->markTestIncomplete();
	}
}
