<?php

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
				'fakeIP'   => '0:0:0:0:0:ffff:d1ad:35a7',
				'useFirst' => true
			),
			// check
			array(
				'case'   => 'Single IPv6, using the first one',
				'result' => '0:0:0:0:0:ffff:d1ad:35a7'
			)
		);

		$data['IPv4 and IPv6, using the first one'] = array(
			// test
			array(
				'fakeIP'   => '127.0.0.1,0:0:0:0:0:ffff:d1ad:35a7',
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
				'fakeIP'   => '127.0.0.1,0:0:0:0:0:ffff:d1ad:35a7',
				'useFirst' => false
			),
			// check
			array(
				'case'   => 'IPv4 and IPv6, using the last one',
				'result' => '0:0:0:0:0:ffff:d1ad:35a7'
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

		return $data;
	}
}