<?php
/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

defined('_JEXEC') or die;

if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
{
	return;
}

class JFormFieldFofencryptedtoken extends JFormFieldText
{
	protected function getInput()
	{
		$this->value = $this->getTokenForDisplay($this->value);

		return parent::getInput();
	}

	private function getTokenForDisplay($token)
	{
		$algo = $this->getAttribute('algo', 'sha256');

		try
		{
			$siteSecret = JFactory::getApplication()->get('secret');
		}
		catch (Exception $e)
		{
			$jConfig    = JFactory::getConfig();
			$siteSecret = $jConfig->get('secret');
		}

		$rawToken  = base64_decode($token);
		$tokenHash = hash_hmac($algo, $rawToken, $siteSecret);
		$userId    = $this->form->getData()->get('id');
		$message   = "$algo:$userId:$tokenHash";

		return base64_encode($message);
	}
}