<?php
/**
 * @package     FOF
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Tests\Encrypt;


use FOF30\Encrypt\Aes;
use FOF30\Tests\Helpers\FOFTestCase;
use FOF30\Tests\Stubs\Encrypt\MockPhpfunc;

/**
 * @coversDefaultClass FOF30\Encrypt\Aes
 *
 * @package            FOF30\Tests\Encrypt
 */
class AesTest extends FOFTestCase
{
	/** @var  Aes */
	protected $aes;

	/**
	 * @return  void
	 */
	protected function setUp()
	{
		// Check if PHP has mcrypt installed
		if (function_exists('mcrypt_module_open') || function_exists('openssl_encrypt'))
		{
			$this->aes = new Aes('x123456789012345678901234567890x');
		}
	}

	/**
	 * @covers FOF30\Encrypt\Aes::IsSupported
	 *
	 * @return  void
	 */
	public function testIsSupported()
	{
		if (version_compare(PHP_VERSION, '7.0.0', 'ge'))
		{
			$this->markTestSkipped('mcrypt is deprecated in PHP 7');
		}

		$functions_enabled = array(
			'mcrypt_get_key_size',
			'mcrypt_get_iv_size',
			'mcrypt_create_iv',
			'mcrypt_encrypt',
			'mcrypt_decrypt',
			'mcrypt_list_algorithms',
			'hash',
			'hash_algos',
			'base64_encode',
			'base64_decode'
		);

		$algorithms = array(
			'rijndael-128',
			'rijndael-192',
			'rijndael-256',
		);

		$hashAlgos = array(
			'sha256'
		);

		// Create a mock php function with all prerequisites met
		$phpfunc = new MockPhpfunc();
		$phpfunc->setFunctions($functions_enabled);
		$phpfunc->setMcryptAlgorithms($algorithms);
		$phpfunc->setHashAlgorithms($hashAlgos);

		// Just for code coverage
		$this->assertNotNull(Aes::isSupported());

		// All prerequisites met = supported
		$this->assertTrue(Aes::isSupported($phpfunc), 'All prerequisites met = supported');

		// No hash algorithms = not supported
		$phpfunc->setHashAlgorithms(array());
		$this->assertFalse(Aes::isSupported($phpfunc), 'No hash algorithms = not supported');
		$phpfunc->setHashAlgorithms($hashAlgos);

		// No mcrypt algorithms = not supported
		$phpfunc->setMcryptAlgorithms(array());
		$this->assertFalse(Aes::isSupported($phpfunc), 'No mcrypt algorithms = not supported');
		$phpfunc->setMcryptAlgorithms($algorithms);

		// No required functions available = not supported
		$phpfunc->setFunctions(array());
		$this->assertFalse(Aes::isSupported($phpfunc), 'No required functions available = not supported');
		$phpfunc->setFunctions($functions_enabled);

		// Test with diminishing amounts of supported mcrypt algos (=not supported) – for code coverage
		$temp = $algorithms;

		while (!empty($temp))
		{
			array_pop($temp);
			$phpfunc->setMcryptAlgorithms($temp);
			$this->assertFalse(Aes::isSupported($phpfunc));
		}

		$phpfunc->setMcryptAlgorithms($algorithms);

		// Test with diminishing amounts of supported functions (=not supported) – for code coverage
		$temp = $functions_enabled;

		while (!empty($temp))
		{
			array_pop($temp);
			$phpfunc->setFunctions($temp);
			$this->assertFalse(Aes::isSupported($phpfunc));
		}
	}

	/**
	 * @covers FOF30\Encrypt\Aes::IsSupported
	 *
	 * @return  void
	 */
	public function testIsSupportedOpenSSL()
	{
		$functions_enabled = array(
			'openssl_get_cipher_methods',
			'openssl_random_pseudo_bytes',
			'openssl_cipher_iv_length',
			'openssl_encrypt',
			'openssl_decrypt',
			'hash',
			'hash_algos',
			'base64_encode',
			'base64_decode'
		);

		$algorithms = array(
			'aes-128-cbc',
		);

		$hashAlgos = array(
			'sha256'
		);

		// Create a mock php function with all prerequisites met
		$phpfunc = new MockPhpfunc();
		$phpfunc->setFunctions($functions_enabled);
		$phpfunc->setOpenSSLAlgorithms($algorithms);
		$phpfunc->setHashAlgorithms($hashAlgos);

		// Just for code coverage
		$this->assertNotNull(Aes::isSupported());

		// All prerequisites met = supported
		$this->assertTrue(Aes::isSupported($phpfunc), 'All prerequisites met = supported');

		// No hash algorithms = not supported
		$phpfunc->setHashAlgorithms(array());
		$this->assertFalse(Aes::isSupported($phpfunc), 'No hash algorithms = not supported');
		$phpfunc->setHashAlgorithms($hashAlgos);

		// No OpenSSL algorithms = not supported
		$phpfunc->setOpenSSLAlgorithms(array());
		$this->assertFalse(Aes::isSupported($phpfunc), 'No OpenSSL algorithms = not supported');
		$phpfunc->setOpenSSLAlgorithms($algorithms);

		// No required functions available = not supported
		$phpfunc->setFunctions(array());
		$this->assertFalse(Aes::isSupported($phpfunc), 'No required functions available = not supported');
		$phpfunc->setFunctions($functions_enabled);

		// Test with diminishing amounts of supported OpenSSL algos (=not supported) – for code coverage
		$temp = $algorithms;

		while (!empty($temp))
		{
			array_pop($temp);
			$phpfunc->setOpenSSLAlgorithms($temp);
			$this->assertFalse(Aes::isSupported($phpfunc));
		}

		$phpfunc->setOpenSSLAlgorithms($algorithms);

		// Test with diminishing amounts of supported functions (=not supported) – for code coverage
		$temp = $functions_enabled;

		while (!empty($temp))
		{
			array_pop($temp);
			$phpfunc->setFunctions($temp);
			$this->assertFalse(Aes::isSupported($phpfunc));
		}
	}

	/**
	 * @covers FOF30\Encrypt\Aes
	 *
	 * @return  void
	 */
	public function testCryptProcessOpenSSL()
	{
		if (function_exists('openssl_encrypt'))
		{
			$phpfunc = new MockPhpfunc();
			$phpfunc->setFunctions(array(
				'openssl_get_cipher_methods',
				'openssl_random_pseudo_bytes',
				'openssl_cipher_iv_length',
				'openssl_encrypt',
				'openssl_decrypt',
				'hash',
				'hash_algos',
				'base64_encode',
				'base64_decode'
			));

			// Regular string
			$str = 'THATISINSANE';

			$aes = new Aes('x123456789012345678901234567890x', 128, 'cbc', $phpfunc);
			$es  = $aes->encryptString($str, true);
			$ds  = $aes->decryptString($es, true);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

			// UTF-8 data
			$str = 'Χρησιμοποιώντας μη λατινικούς χαρακτήρες';
			$es  = $aes->encryptString($str, false);
			$ds  = $aes->decryptString($es, false);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

			// Using an odd sized keystring (using sha256 to convert it to a key)
			$aes = new Aes('The quick brown fox jumped over the lazy dog');
			$str = 'This is some very secret stuff that you are not supposed to transmit in clear text';
			$es  = $aes->encryptString($str, true);
			$ds  = $aes->decryptString($es, true);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

		}
		else
		{
			$this->markTestSkipped('OpenSSL is not supported on this system');
		}
	}

	/**
	 * @covers FOF30\Encrypt\Aes
	 *
	 * @return  void
	 */
	public function testCryptCrossCompatibility()
	{
		if (version_compare(PHP_VERSION, '7.0.0', 'ge'))
		{
			$this->markTestSkipped('mcrypt is deprecated in PHP 7');
		}

		if (function_exists('mcrypt_module_open') && function_exists('openssl_encrypt'))
		{
			$phpfunc = new MockPhpfunc();
			$phpfunc->setFunctions(array(
				'openssl_get_cipher_methods',
				'openssl_random_pseudo_bytes',
				'openssl_cipher_iv_length',
				'openssl_encrypt',
				'openssl_decrypt',
				'hash',
				'hash_algos',
				'base64_encode',
				'base64_decode'
			));

			$aes   = new Aes('x123456789012345678901234567890x', 128, 'cbc', $phpfunc);
			$mcAes = new Aes('The quick brown fox jumped over the lazy dog', 128, 'cbc');
			$osAes = new Aes('The quick brown fox jumped over the lazy dog', 128, 'cbc', $phpfunc);

			// Regular string
			$str = 'THATISINSANE';
			$es  = $this->aes->encryptString($str, true);
			$ds  = $aes->decryptString($es, true);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds, 'Regular string, encrypt mcrypt, decrypt OpenSSL');

			// UTF-8 data
			$str = 'Χρησιμοποιώντας μη λατινικούς χαρακτήρες';
			$es  = $this->aes->encryptString($str, false);
			$ds  = $aes->decryptString($es, false);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds, 'Unicode string, encrypt mcrypt, decrypt OpenSSL');

			// Using an odd sized keystring (using sha256 to convert it to a key)
			$str = 'This is some very secret stuff that you are not supposed to transmit in clear text';
			$es  = $mcAes->encryptString($str, true);
			$ds  = $osAes->decryptString($es, true);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds, 'Legacy mode, encrypt mcrypt, decrypt OpenSSL');


			// Regular string
			$str = 'THATISINSANE';
			$es  = $aes->encryptString($str, true);
			$ds  = $this->aes->decryptString($es, true);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds, 'Regular string, encrypt OpenSSL, decrypt mcrypt');

			// UTF-8 data
			$str = 'Χρησιμοποιώντας μη λατινικούς χαρακτήρες';
			$es  = $aes->encryptString($str, false);
			$ds  = $this->aes->decryptString($es, false);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds, 'Unicode string, encrypt OpenSSL, decrypt mcrypt');

			// Using an odd sized keystring (using sha256 to convert it to a key)
			$str = 'This is some very secret stuff that you are not supposed to transmit in clear text';
			$es  = $osAes->encryptString($str, true);
			$ds  = $mcAes->decryptString($es, true);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds, 'Legacy mode, encrypt OpenSSL, decrypt mcrypt');

		}
		else
		{
			$this->markTestSkipped('mcrypt and OpenSSL are not supported on this system');
		}
	}


	/**
	 * @covers FOF30\Encrypt\Aes
	 *
	 * @return  void
	 */
	public function testCryptProcess()
	{
		if (function_exists('mcrypt_module_open') || function_exists('openssl_encrypt'))
		{
			// Regular string
			$str = 'THATISINSANE';

			$es = $this->aes->encryptString($str, true);
			$ds = $this->aes->decryptString($es, true);
			$ds = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

			// UTF-8 data
			$str = 'Χρησιμοποιώντας μη λατινικούς χαρακτήρες';
			$es  = $this->aes->encryptString($str, false);
			$ds  = $this->aes->decryptString($es, false);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

			// Using an odd sized keystring (using sha256 to convert it to a key)
			$this->aes = new Aes('The quick brown fox jumped over the lazy dog');
			$str       = 'This is some very secret stuff that you are not supposed to transmit in clear text';
			$es        = $this->aes->encryptString($str, true);
			$ds        = $this->aes->decryptString($es, true);
			$ds        = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

		}
		else
		{
			$this->markTestSkipped('mcrypt and OpenSSL are not supported on this system');
		}
	}

	/**
	 * @covers FOF30\Encrypt\Aes
	 *
	 * @return  void
	 */
	public function testCryptProcess192()
	{
		if (function_exists('mcrypt_module_open'))
		{
			$this->aes = new Aes('The quick brown fox jumped over the lazy dog', 192);

			// Regular string
			$str = 'THATISINSANE';

			$es = $this->aes->encryptString($str, true);
			$ds = $this->aes->decryptString($es, true);
			$ds = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

			// UTF-8 data
			$str = 'Χρησιμοποιώντας μη λατινικούς χαρακτήρες';
			$es  = $this->aes->encryptString($str, false);
			$ds  = $this->aes->decryptString($es, false);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

			// Using an odd sized keystring (using sha256 to convert it to a key)
			$this->aes = new Aes('The quick brown fox jumped over the lazy dog');
			$str       = 'This is some very secret stuff that you are not supposed to transmit in clear text';
			$es        = $this->aes->encryptString($str, true);
			$ds        = $this->aes->decryptString($es, true);
			$ds        = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

		}
		else
		{
			$this->markTestSkipped('mcrypt is not supported on this system');
		}
	}

	/**
	 * @covers FOF30\Encrypt\Aes
	 *
	 * @return  void
	 */
	public function testCryptProcess128()
	{
		if (function_exists('mcrypt_module_open') || function_exists('openssl_encrypt'))
		{
			$this->aes = new Aes('The quick brown fox jumped over the lazy dog', 128);

			// Regular string
			$str = 'THATISINSANE';

			$es = $this->aes->encryptString($str, true);
			$ds = $this->aes->decryptString($es, true);
			$ds = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

			// UTF-8 data
			$str = 'Χρησιμοποιώντας μη λατινικούς χαρακτήρες';
			$es  = $this->aes->encryptString($str, false);
			$ds  = $this->aes->decryptString($es, false);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

			// Using an odd sized keystring (using sha256 to convert it to a key)
			$this->aes = new Aes('The quick brown fox jumped over the lazy dog');
			$str       = 'This is some very secret stuff that you are not supposed to transmit in clear text';
			$es        = $this->aes->encryptString($str, true);
			$ds        = $this->aes->decryptString($es, true);
			$ds        = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

		}
		else
		{
			$this->markTestSkipped('mcrypt and OpenSSL are not supported on this system');
		}
	}

	/**
	 * @covers FOF30\Encrypt\Aes
	 *
	 * @return  void
	 */
	public function testCryptProcessEcb()
	{
		if (function_exists('mcrypt_module_open') || function_exists('openssl_encrypt'))
		{
			$this->aes = new Aes('The quick brown fox jumped over the lazy dog', 256, 'ecb');

			// Regular string
			$str = 'THATISINSANE';

			$es = $this->aes->encryptString($str, true);
			$ds = $this->aes->decryptString($es, true);
			$ds = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

			// UTF-8 data
			$str = 'Χρησιμοποιώντας μη λατινικούς χαρακτήρες';
			$es  = $this->aes->encryptString($str, false);
			$ds  = $this->aes->decryptString($es, false);
			$ds  = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

			// Using an odd sized keystring (using sha256 to convert it to a key)
			$this->aes = new Aes('The quick brown fox jumped over the lazy dog');
			$str       = 'This is some very secret stuff that you are not supposed to transmit in clear text';
			$es        = $this->aes->encryptString($str, true);
			$ds        = $this->aes->decryptString($es, true);
			$ds        = rtrim($ds, "\000");
			$this->assertNotEquals($str, $es);
			$this->assertEquals($str, $ds);

		}
		else
		{
			$this->markTestSkipped('mcrypt and OpenSSL are not supported on this system');
		}
	}

	/**
	 * @covers FOF30\Encrypt\Aes
	 *
	 * @return  void
	 */
	public function testCryptWithProperKeyExpansion()
	{
		if (function_exists('mcrypt_module_open') || function_exists('openssl_encrypt'))
		{
			$aes   = new Aes('x123456789012345678901234567890x', 128, 'cbc');

			// Yeah, a terrible password.
			$aes->setPassword('p@$$w0rd');

			$clearText = 'The quick brown fox jumped over the lazy dog';
			$encrypted = $aes->encryptString($clearText);

			$sameDecrypted = $aes->decryptString($encrypted);
			// Remember, the decrypted result is zero-padded!
			$sameDecrypted = rtrim($sameDecrypted, "\0");
			$this->assertTrue($sameDecrypted == $clearText, 'Same object must be able to decrypt the original message');

			$wrongAes = new Aes('p@$$w0rd', 128, 'cbc');
			$wrongDecrypted = $wrongAes->decryptString($encrypted);
			// Remember, the decrypted result is zero-padded!
			$wrongDecrypted = rtrim($wrongDecrypted, "\0");
			$this->assertFalse($wrongDecrypted == $clearText, 'Legacy key expansion must not be able to decrypt new message');

			$rightAes = new Aes('changeme', 128, 'cbc');
			$rightAes->setPassword('p@$$w0rd');
			$rightDecrypted = $rightAes->decryptString($encrypted);
			// Remember, the decrypted result is zero-padded!
			$rightDecrypted = rtrim($rightDecrypted, "\0");
			$this->assertTrue($rightDecrypted == $clearText, 'New key expansion must be able to decrypt new message');
		}
		else
		{
			$this->markTestSkipped('mcrypt or OpenSSL is not supported on this system');
		}
	}
}
