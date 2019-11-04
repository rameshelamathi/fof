<?php
/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

use FOF30\Encrypt\Aes;
use FOF30\Utils\Phpfunc;

defined('_JEXEC') or die;

if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
{
	return;
}

class JFormFieldFofencryptedtoken extends JFormFieldText
{
	protected function getInput()
	{
		$this->value = $this->decrypt($this->value);

		return parent::getInput();
	}

	private function decrypt($value)
	{
		if (empty($value) || substr($value, 0, 12) !== '###AES128###')
		{
			return $value;
		}

		$token = substr($value, 12);

		try
		{
			$siteSecret = JFactory::getApplication()->get('secret');
		}
		catch (Exception $e)
		{
			$jConfig    = JFactory::getConfig();
			$siteSecret = $jConfig->get('secret');
		}

		$phpFunc  = new Phpfunc();
		$aes      = new Aes($siteSecret, 128, 'cbc', $phpFunc);
		$rawToken = $aes->decryptString($token, true);
		$rawToken = trim($rawToken);

		return base64_encode($rawToken);
	}
}