<?php
/**
 * @package     FOF
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Tests\Stubs\Encrypt;


use FOF30\Utils\Phpfunc;

class MockPhpfunc extends Phpfunc
{
	protected $extensions = array();

	protected $functions_enabled = null;

	protected $mcrypt_algorithms = null;

	protected $hash_algorithms = null;

	protected $openssl_algorithms = null;

	public function __construct()
	{
		$this->setExtensions(get_loaded_extensions());
	}

	public function setExtensions(array $extensions)
	{
		$this->extensions = $extensions;
	}

	public function setFunctions($functions)
	{
		$this->functions_enabled = $functions;
	}

	public function setMcryptAlgorithms($algos)
	{
		$this->mcrypt_algorithms = $algos;
	}

	public function setOpenSSLAlgorithms($algos)
	{
		$this->openssl_algorithms = $algos;
	}

	public function setHashAlgorithms($algos)
	{
		$this->hash_algorithms = $algos;
	}

	public function extension_loaded($name)
	{
		// for parent coverage
		$this->__call('extension_loaded', array($name));

		// for testing
		return in_array($name, $this->extensions);
	}

	public function function_exists($name)
	{
		// for parent coverage
		$result = $this->__call('function_exists', array($name));

		if (is_null($this->functions_enabled))
		{
			return $result;
		}

		// for testing
		return in_array($name, $this->functions_enabled);
	}

	public function mcrypt_list_algorithms()
	{
		// for parent coverage
		$result = $this->__call('mcrypt_list_algorithms', array());

		if (is_null($this->mcrypt_algorithms))
		{
			return $result;
		}

		// for testing
		return $this->mcrypt_algorithms;
	}

	public function openssl_get_cipher_methods()
	{
		// for parent coverage
		$result = $this->__call('openssl_get_cipher_methods', array());

		if (is_null($this->openssl_algorithms))
		{
			return $result;
		}

		// for testing
		return $this->openssl_algorithms;
	}

	public function hash_algos()
	{
		// for parent coverage
		$result = $this->__call('hash_algos', array());

		if (is_null($this->hash_algorithms))
		{
			return $result;
		}

		// for testing
		return $this->hash_algorithms;
	}
} 
