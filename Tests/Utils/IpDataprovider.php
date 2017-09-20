<?php

class IpDataprovider
{
	public static function getDetectAndCleanIP()
	{
		$data[] = array(
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

		$data[] = array(
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

		$data[] = array(
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

		$data[] = array(
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

		$data[] = array(
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

		$data[] = array(
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

		$data[] = array(
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

		$data[] = array(
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

		$data[] = array(
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

		$data[] = array(
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

		$data[] = array(
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

		$data[] = array(
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

		return $data;
	}
}