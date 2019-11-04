<?php
/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

defined('_JEXEC') or die;

use FOF30\Container\Container;
use FOF30\Encrypt\Aes;
use FOF30\Encrypt\Randval;
use FOF30\Utils\ArrayHelper;
use FOF30\Utils\Phpfunc;
use Joomla\CMS\Authentication\Authentication;
use Joomla\CMS\Authentication\AuthenticationResponse;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Form\Form as JForm;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin as JPlugin;

if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
{
	return;
}

/**
 * FOF Authentication Token management
 *
 * Allows users to manage their API access tokens for FOF-powered extensions. The token can be used with FOF's
 * Transparent Authentication.
 */
class PlgUserFoftoken extends JPlugin
{
	/**
	 * Joomla XML form contexts where we need to inject our token management interface.
	 *
	 * @var  array
	 */
	private $allowedContexts = [
		'com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile',
	];

	/**
	 * The prefix of the user profile keys, without the dot.
	 *
	 * @var  string
	 */
	private $profileKeyPrefix = 'foftoken';

	/**
	 * Token length, in bytes.
	 *
	 * @var  int
	 */
	private $tokenLength = 32;

	/**
	 * Inject the FOF token management panel's data into the User Profile.
	 *
	 * This method is called whenever Joomla is preparing the data for an XML form for display.
	 *
	 * @param   string  $context
	 * @param   mixed   $data
	 *
	 * @return  bool
	 */
	public function onContentPrepareData($context, &$data)
	{
		// Check we are manipulating a valid form.
		if (!in_array($context, $this->allowedContexts))
		{
			return true;
		}

		// The $data must be an object
		if (!is_object($data))
		{
			return true;
		}

		// We expect the numeric user ID in $data->id
		if (!isset($data->id))
		{
			return true;
		}

		// Get the user ID
		$userId = isset($data->id) ? intval($data->id) : 0;

		// Make sure we have a positive integer user ID
		if ($userId <= 0)
		{
			return true;
		}

		// Oh, cool, Joomla has already loaded the profile data for us.
		if (isset($data->profile))
		{
			return true;
		}

		$data->{$this->profileKeyPrefix} = [];

		// Load the profile data from the database.

		try
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select([
					$db->qn('profile_key'), $db->qn('profile_value'),
				])
				->from($db->qn('#__user_profiles'))
				->where($db->qn('user_id') . ' = ' . $db->q($userId))
				->where($db->qn('profile_key') . ' LIKE ' . $db->q($this->profileKeyPrefix . '.%', false))
				->order($db->qn('ordering'));

			$results = $db->setQuery($query)->loadRowList();

			foreach ($results as $v)
			{
				$k                                   = str_replace($this->profileKeyPrefix . '.', '', $v[0]);
				$data->{$this->profileKeyPrefix}[$k] = $v[1];
			}
		}
		catch (Exception $e)
		{
			// We suppress any database error. It means we get no token saved by default.
		}

		return true;
	}

	/**
	 * @param   JForm  $form  The form to be altered.
	 * @param   mixed  $data  The associated data for the form.
	 *
	 * @return   bool
	 *
	 * @throws   Exception  When $form is not a valid form object
	 */
	public function onContentPrepareForm($form, &$data)
	{
		if (!($form instanceof JForm))
		{
			throw new Exception('JERROR_NOT_A_FORM');
		}

		// Check we are manipulating a valid form.
		if (!in_array($form->getName(), $this->allowedContexts))
		{
			return true;
		}

		// Add the registration fields to the form.
		JForm::addFormPath(dirname(__FILE__) . '/foftoken');
		$form->loadFile('foftoken', false);

		return true;
	}

	/**
	 * Save the FOF token in the user profile field
	 *
	 * @param   mixed   $data    The incoming form data
	 * @param   bool    $isNew   Is this a new user?
	 * @param   bool    $result  Has Joomla successfully saved the user?
	 * @param   string  $error   Error string
	 *
	 * @return bool
	 */
	public function onUserAfterSave($data, $isNew, $result, $error)
	{
		if (!is_array($data))
		{
			return false;
		}

		$userId = ArrayHelper::getValue($data, 'id', 0, 'int');

		if ($userId <= 0)
		{
			return false;
		}

		if (!$result)
		{
			return false;
		}

		if ($isNew || !isset($data[$this->profileKeyPrefix]) || empty($data[$this->profileKeyPrefix]['token']))
		{
			if (!isset($data[$this->profileKeyPrefix]))
			{
				$data[$this->profileKeyPrefix] = [];
			}

			if (isset($data[$this->profileKeyPrefix]['token']) && empty($data[$this->profileKeyPrefix]['token']))
			{
				unset($data[$this->profileKeyPrefix]['token']);
			}

			$default                       = $this->getDefaultProfileFieldValues();
			$data[$this->profileKeyPrefix] = array_merge($default, $data[$this->profileKeyPrefix]);
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->delete($db->qn('#__user_profiles'))
			->where($db->qn('user_id') . ' = ' . $db->q($userId))
			->where($db->qn('profile_key') . ' LIKE ' . $db->q($this->profileKeyPrefix . '.%', false));

		$db->setQuery($query)->execute();

		$order = 1;
		$query = $db->getQuery(true)
			->insert($db->qn('#__user_profiles'))
			->columns([$db->qn('user_id'), $db->qn('profile_key'), $db->qn('profile_value'), $db->qn('ordering')]);

		foreach ($data['ats'] as $k => $v)
		{
			$query->values($userId . ', ' . $db->quote($this->profileKeyPrefix . '.' . $k) . ', ' . $db->quote($v) . ', ' . $order++);
		}

		$db->setQuery($query)->execute();

		return true;
	}

	/**
	 * Remove the FOF token when the user account is deleted from the datase.
	 *
	 * This event is called after the user data is deleted from the database.
	 *
	 * @param   array    $user     Holds the user data
	 * @param   boolean  $success  True if user was succesfully stored in the database
	 * @param   string   $msg      Message
	 *
	 * @return  bool
	 *
	 * @throws  Exception
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		$userId = ArrayHelper::getValue($user, 'id', 0, 'int');

		if ($userId <= 0)
		{
			return true;
		}

		try
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->delete($db->qn('#__user_profiles'))
				->where($db->qn('user_id') . ' = ' . $db->q($userId))
				->where($db->qn('profile_key') . ' LIKE ' . $db->q($this->profileKeyPrefix . '.%', false));
			$db->setQuery($query)->execute();
		}
		catch (Exception $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Return an array of transparent authentication credentials.
	 *
	 * This is called from TransparentAuthentication::getTransparentAuthenticationCredentials. Return an array of
	 * credentials or an empty array if no suitable credentials were found.
	 *
	 * @param   Container  $container  The container of the component performing a transparent authentication check.
	 *
	 * @return  array  Authentication credentials
	 */
	public function onFOFGetTransparentAuthenticationCredentials(Container $container)
	{
		$token = '';

		// Check for HTTP header "Authentication: Bearer <token>"
		if (isset($_SERVER['HTTP_AUTHENTICATION']))
		{
			$authHeader = $_SERVER['HTTP_AUTHENTICATION'];

			if (substr($authHeader, 0, 7) == 'Bearer ')
			{
				$parts  = explode(' ', $authHeader, 2);
				$token  = $parts[1];
				$filter = InputFilter::getInstance();
				$token  = $filter->clean($token, 'BASE64');
			}
		}

		// Check for _fofToken query param
		if (empty($token))
		{
			$input = $container->input;
			$token = $input->getBase64('_fofToken', '');
		}

		// No token. Return an empty array.
		if (empty($token))
		{
			return [];
		}

		// Return the credentials array. Will be used by onFOFUserAuthenticate.
		return [
			'fofToken' => $token,
		];
	}

	/**
	 * Perform user authentication only in the FOF transparent authentication context.
	 *
	 * This is called by Platform::loginUser() when Joomla fails to login a user using its own authentication plugins.
	 * Implementations of this even handler MUST NOT duplicate Joomla functionality, i.e. you shouldn't do username and
	 * password authentication or any other authentication performed by Joomla. This is meant for FOF-specific
	 * authentication, typically using tokens.
	 *
	 * @param   array  $credentials
	 * @param   array  $options
	 *
	 * @return  AuthenticationResponse
	 */
	public function onFOFUserAuthenticate(array $credentials, array $options)
	{
		// Default response: failure
		$response                = new AuthenticationResponse();
		$response->type          = 'foftoken';
		$response->status        = Authentication::STATUS_FAILURE;
		$response->error_message = Text::_('PLG_USER_FOFTOKEN_ERR_INVALIDTOKEN');

		// Immediate failure if there is no usable token
		if (empty($credentials) || !is_array($credentials) || !array_key_exists('fofToken', $credentials) || empty($credentials['fofToken']))
		{
			return $response;
		}

		/**
		 * Convert the token into the format used to store it in the database.
		 *
		 * The base64 encoded token is first converted to raw bytes. Then it's encrypted with the site's secret to
		 * generate the same string format we use to store the token in the database.
		 */
		$filter = InputFilter::getInstance();
		$token  = $filter->clean($credentials['fofToken'], 'BASE64');

		if (empty($token))
		{
			return $response;
		}

		$rawToken = base64_decode($token);

		try
		{
			$siteSecret = JFactory::getApplication()->get('secret');
		}
		catch (Exception $e)
		{
			$jConfig    = JFactory::getConfig();
			$siteSecret = $jConfig->get('secret');
		}

		$phpFunc     = new Phpfunc();
		$aes         = new Aes($siteSecret, 128, 'cbc', $phpFunc);
		$searchToken = '###AES128###' . $aes->encryptString($rawToken, true);

		// Search the foftoken.token user profile fields for a match
		$db             = JFactory::getDbo();
		$userIdSubquery = $db->getQuery(true)
			->select($db->qn('user_id'))
			->from($db->qn('#__user_profiles'))
			->where($db->qn('profile_key') . ' = ' . $db->q($this->profileKeyPrefix . '.token'))
			->where($db->qn('profile_value') . ' = ' . $db->q($searchToken));
		$dataQuery      = $db->getQuery(true)
			->select($db->qn([
				'user_id', 'profile_key', 'profile_value',
			]))
			->from($db->qn('#__user_profiles'))
			->where($db->qn('user_id') . ' IN(' . (string) $userIdSubquery . ')')
			->where($db->qn('profile_key') . ' LIKE ' . $db->q($this->profileKeyPrefix . '.%', false))
			->order($db->qn('ordering'));

		$data = $db->setQuery($dataQuery)->loadAssocList();

		// DO NOT OPTIMIZE. We need to consume approx. the same time for missing and disabled keys.
		$enabledKey = $this->profileKeyPrefix . '.enabled';
		$enabled    = array_key_exists($enabledKey, $data) ? (bool) ($data[$enabledKey]) : false;

		if (!$enabled)
		{
			return $response;
		}

		$userId = $data[0]['user_id'];

		if ($userId <= 0)
		{
			return $response;
		}

		$user = JFactory::getUser($userId);

		// Disallow login for blocked, inactive or password reset required users
		if ($user->block || !empty(trim($user->activation)) || $user->requireReset)
		{
			$response->status = Authentication::STATUS_DENIED;

			return $response;
		}

		// Update the response to indicate successful login
		$response->status        = Authentication::STATUS_SUCCESS;
		$response->error_message = '';
		$response->username      = $user->username;
		$response->email         = $user->email;
		$response->fullname      = $user->name;
		$response->timezone      = $user->get('timezone');
		$response->language      = $user->get('language');

		return $response;
	}

	/**
	 * Creates a new, encrypted token
	 *
	 * @param   int  $byteLength
	 *
	 * @return  string
	 */
	private function getNewToken($byteLength = 32)
	{
		// The site secret is used to encrypt the generated token
		try
		{
			$siteSecret = JFactory::getApplication()->get('secret');
		}
		catch (Exception $e)
		{
			$jConfig    = JFactory::getConfig();
			$siteSecret = $jConfig->get('secret');
		}

		$phpFunc = new Phpfunc();
		$randVal = new Randval($phpFunc);
		$aes     = new Aes($siteSecret, 128, 'cbc', $phpFunc);

		return '###AES128###' . $aes->encryptString($randVal->generate($byteLength), true);
	}

	/**
	 * Returns an array with the default profile field values.
	 *
	 * This is used when loading / saving the form data of a user without a token and when a new user is being created.
	 *
	 * @return  array
	 */
	private function getDefaultProfileFieldValues()
	{
		return [
			'token'   => $this->getNewToken($this->tokenLength),
			'enabled' => true,
		];
	}
}