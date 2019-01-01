<?php
/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

class IpDataprovider
{
	public static function getDetectAndCleanIP()
	{
		$data['Single IPv4, using the first one'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'Single IPv4, using the first one',
				'result' => '127.0.0.1'
			)
		);

		$data['Single IPv6, using the first one'] = array(
			// test
			array(
				'fakeIP'   => '2607:f0d0:1002:0051:0000:0000:0000:0004',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'Single IPv6, using the first one',
				'result' => '2607:F0D0:1002:0051:0000:0000:0000:0004'
			)
		);

		$data['IPv4 and IPv6, using the first one'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1,2607:f0d0:1002:0051:0000:0000:0000:0004',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'IPv4 and IPv6, using the first one',
				'result' => '127.0.0.1'
			)
		);

		$data['IPv4 and IPv6, using the last one'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1,2607:f0d0:1002:0051:0000:0000:0000:0004',
				'useFirst' => false
			),
			// check
			array(
				'case'   => 'IPv4 and IPv6, using the last one',
				'result' => '2607:F0D0:1002:0051:0000:0000:0000:0004'
			)
		);

		$data['IPv6 through proxy (SHOULD NEVER HAPPEN)'] = array(
			// test
			array(
				'fakeIP'   => 'dead:beef:bad0:0bad:0000:0000:0000:0001,2607:f0d0:1002:0051:0000:0000:0000:0004',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'IPv4 and IPv6, using the last one',
				'result' => 'DEAD:BEEF:BAD0:0BAD:0000:0000:0000:0001'
			)
		);

		$data['Two IPv4, using the first one'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1,1.1.1.1',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'Two IPv4, using the first one',
				'result' => '127.0.0.1'
			)
		);

		$data['Two IPv4, using the last one'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1,1.1.1.1',
				'useFirst' => false
			),
			// check
			array(
				'case'   => 'Two IPv4, using the last one',
				'result' => '1.1.1.1'
			)
		);

		$data['Three IPv4, using the first one'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1,1.1.1.1,2.2.2.2',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'Three IPv4, using the first one',
				'result' => '127.0.0.1'
			)
		);

		$data['Three IPv4, using the last one'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1,1.1.1.1,2.2.2.2',
				'useFirst' => false
			),
			// check
			array(
				'case'   => 'Three IPv4, using the last one',
				'result' => '2.2.2.2'
			)
		);

		$data['Malformed IPs (1)'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1, 1.1.1.1, 2.2.2.2',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'Malformed IPs (1)',
				'result' => '127.0.0.1'
			)
		);

		$data['Malformed IPs (2)'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1,  1.1.1.1,  2.2.2.2',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'Malformed IPs (2)',
				'result' => '127.0.0.1'
			)
		);

		$data['Malformed IPs (3)'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1 1.1.1.1 2.2.2.2',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'Malformed IPs (3)',
				'result' => '127.0.0.1'
			)
		);

		$data['Malformed IPs (4)'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1  1.1.1.1  2.2.2.2',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'Malformed IPs (4)',
				'result' => '127.0.0.1'
			)
		);

		$data['IPv4 wrapped in IPv6, compressed zeroes'] = array(
			// test
			array(
				'fakeIP'   => '::ffff:192.168.1.2',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'IPv4 wrapped in IPv6, compressed zeroes',
				'result' => '192.168.1.2'
			)
		);

		$data['IPv4 wrapped in IPv6, expanded zeroes'] = array(
			// test
			array(
				'fakeIP'   => '0:0:0:0:0:ffff:192.168.1.2',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'IPv4 wrapped in IPv6, expanded zeroes',
				'result' => '192.168.1.2'
			)
		);

		$data['IPv4 wrapped in IPv6, all hex, collapsed zeroes'] = array(
			// test
			array(
				'fakeIP'   => '::FFFF:C0A8:0101',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'IPv4 wrapped in IPv6, all hex, collapsed zeroes',
				'result' => '192.168.1.1'
			)
		);

		$data['IPv4 wrapped in IPv6, all hex, expanded zeroes'] = array(
			// test
			array(
				'fakeIP'   => '0:0:0:0:0:FFFF:C0A8:0101',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'IPv4 wrapped in IPv6, all hex, expanded zeroes',
				'result' => '192.168.1.1'
			)
		);

		$data['IPv4 wrapped in IPv6, through proxy, IPv4 returned'] = array(
			// test
			array(
				'fakeIP'   => '0:0:0:0:0:FFFF:C0A8:0101,2607:f0d0:1002:0051:0000:0000:0000:0004',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'IPv4 wrapped in IPv6, through proxy, IPv4 returned',
				'result' => '192.168.1.1'
			)
		);

		return $data;
	}
}