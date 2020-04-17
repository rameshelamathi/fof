<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Utils;

use FOF30\Container\Container;
use JDatabaseDriver;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

/**
 * Class MediaVersion
 * @package FOF30\Utils
 *
 * @since 3.5.3
 */
class MediaVersion
{
	/**
	 * Cached the version and date of FOF-powered components
	 *
	 * @var   array
	 * @since 3.5.3
	 */
	protected static $componentVersionCache = [];

	/**
	 * The current component's container
	 *
	 * @var   Container
	 * @since 3.5.3
	 */
	protected $container;

	/**
	 * The configured media query version
	 *
	 * @var   string|null;
	 * @since 3.5.3
	 */
	protected $mediaVersion;

	/**
	 * MediaVersion constructor.
	 *
	 * @param   Container  $c  The component container
	 * @since   3.5.3
	 */
	public function __construct(Container $c)
	{
		$this->container = $c;
	}

	/**
	 * Get a component's version and date
	 *
	 * @param   string           $component
	 * @param   JDatabaseDriver  $db
	 *
	 * @return  array
	 * @since   3.5.3
	 */
	protected static function getComponentVersionAndDate($component, $db)
	{
		if (array_key_exists($component, self::$componentVersionCache))
		{
			return self::$componentVersionCache[$component];
		}

		$version = '0.0.0';
		$date    = date('Y-m-d H:i:s');

		try
		{
			$query = $db->getQuery(true)
				->select([
					$db->qn('manifest_cache'),
				])->from($db->qn('#__extensions'))
				->where($db->qn('type') . ' = ' . $db->q('component'))
				->where($db->qn('name') . ' = ' . $db->q($component));

			$db->setQuery($query);

			$json = $db->loadResult();

			if (class_exists('JRegistry'))
			{
				$params = new \JRegistry($json);
			}
			else
			{
				$params = new Registry($json);
			}

			$version = $params->get('version', $version);
			$date    = $params->get('creationDate', $date);
		}
		catch (\Exception $e)
		{
		}

		self::$componentVersionCache[$component] = [$version, $date];

		return self::$componentVersionCache[$component];
	}

	/**
	 * Returns the media query version string
	 *
	 * @return  string
	 * @since   3.5.3
	 */
	public function __toString()
	{
		if (empty($this->mediaVersion))
		{
			$this->mediaVersion = $this->getDefaultMediaVersion();
		}

		return $this->mediaVersion;
	}

	/**
	 * Sets the media query version string
	 *
	 * @param   mixed  $mediaVersion
	 * @since   3.5.3
	 */
	public function setMediaVersion($mediaVersion)
	{
		$this->mediaVersion = $mediaVersion;
	}

	/**
	 * Returns the default media query version string if none is already defined
	 *
	 * @return  string
	 * @since   3.5.3
	 */
	protected function getDefaultMediaVersion()
	{
		// Initialise
		list ($version, $date) = self::getComponentVersionAndDate($this->container->componentName, $this->container->db);

		// Get the site's secret
		try
		{
			$app = Factory::getApplication();

			if (method_exists($app, 'get'))
			{
				$secret = $app->get('secret');
			}
		}
		catch (\Exception $e)
		{
		}

		// Generate the version string
		return md5($version . $date . $secret);
	}
}