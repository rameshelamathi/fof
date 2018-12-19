<?php
// ====================================================================================================================
// Configuration
// ====================================================================================================================
$sitePath           = '/var/www/test3/cli';
$stableVersionPath  = '/var/www/test3/000/fof-3.3.9';
$installVersionPath = '/var/www/test3/000/dev';

// Define ourselves as a parent file
define('_JEXEC', 1);
// Required by the CMS
define('DS', DIRECTORY_SEPARATOR);


// Load system defines
if (file_exists($sitePath . '/defines.php'))
{
	include_once $sitePath . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	$path = rtrim($sitePath, DIRECTORY_SEPARATOR);
	$rpos = strrpos($path, DIRECTORY_SEPARATOR);
	$path = substr($path, 0, $rpos);
	define('JPATH_BASE', $path);
	require_once JPATH_BASE . '/includes/defines.php';
}

// Load the rest of the framework include files
if (file_exists(JPATH_LIBRARIES . '/import.legacy.php'))
{
	require_once JPATH_LIBRARIES . '/import.legacy.php';
}
else
{
	require_once JPATH_LIBRARIES . '/import.php';
}
require_once JPATH_LIBRARIES . '/cms.php';

// Load the JApplicationCli class
JLoader::import('joomla.application.cli');
JLoader::import('joomla.environment.request');
JLoader::import('joomla.environment.uri');
JLoader::import('joomla.utilities.date');
JLoader::import('joomla.application.component.helper');
JLoader::import('legacy.component.helper');
JLoader::import('joomla.application.component.helper');
JLoader::import('joomla.updater.update');
JLoader::import('joomla.filesystem.file');
JLoader::import('joomla.filesystem.folder');

// Load the language files
$paths = [JPATH_ADMINISTRATOR, JPATH_ROOT];
$jlang = JFactory::getLanguage();
$jlang->load('lib_joomla', $paths[0], 'en-GB', true);

if (version_compare(JVERSION, '3.4.9999', 'ge'))
{
	// Joomla! 3.5 and later does not load the configuration.php unless you explicitly tell it to.
	JFactory::getConfig(JPATH_CONFIGURATION . '/configuration.php');
}

class FOFTestInstall extends JApplicationCli
{
	/**
	 * JApplicationCli didn't want to run on PHP CGI. I have my way of becoming
	 * VERY convincing. Now obey your true master, you petty class!
	 *
	 * @param JInputCli   $input
	 * @param JRegistry   $config
	 * @param JDispatcher $dispatcher
	 */
	public function __construct(JInputCli $input = null, JRegistry $config = null, JDispatcher $dispatcher = null)
	{
		// Close the application if we are not executed from the command line, Akeeba style (allow for PHP CGI)
		if (array_key_exists('REQUEST_METHOD', $_SERVER))
		{
			die('You are not supposed to access this script from the web. You have to run it from the command line. If you don\'t understand what this means, you must not try to use this file before reading the documentation. Thank you.');
		}

		$cgiMode = false;

		if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			$cgiMode = true;
		}

		// If a input object is given use it.
		if ($input instanceof JInput)
		{
			$this->input = $input;
		}
		// Create the input based on the application logic.
		else
		{
			if (class_exists('JInput'))
			{
				if ($cgiMode)
				{
					$query = "";
					if (!empty($_GET))
					{
						foreach ($_GET as $k => $v)
						{
							$query .= " $k";
							if ($v != "")
							{
								$query .= "=$v";
							}
						}
					}
					$query = ltrim($query);
					$argv  = explode(' ', $query);
					$argc  = count($argv);

					$_SERVER['argv'] = $argv;
				}

				$this->input = new JInputCLI();
			}
		}

		// If a config object is given use it.
		if ($config instanceof JRegistry)
		{
			$this->config = $config;
		}
		// Instantiate a new configuration object.
		else
		{
			$this->config = new JRegistry;
		}

		// If a dispatcher object is given use it.
		if ($dispatcher instanceof JDispatcher)
		{
			$this->dispatcher = $dispatcher;
		}
		// Create the dispatcher based on the application logic.
		else
		{
			$this->loadDispatcher();
		}

		// Load the configuration object.
		$this->loadConfiguration($this->fetchConfigurationData());

		// Set the execution datetime and timestamp;
		$this->set('execution.datetime', gmdate('Y-m-d H:i:s'));
		$this->set('execution.timestamp', time());

		// Set the current directory.
		$this->set('cwd', getcwd());

		JLog::addLogger([
			'logger'   => 'callback',
			'callback' => function (\Joomla\CMS\Log\LogEntry $entry) {
				switch ($entry->priority)
				{
					case JLog::ERROR:
						$priority = 'ERROR';
						break;
					case JLog::WARNING:
						$priority = 'WARNING';
						break;
					case JLog::NOTICE:
						$priority = 'NOTICE';
						break;
					default:
						$priority = 'OTHER';
						break;
				}
				echo "[ $priority :: {$entry->message} ]\n";
			},
		], JLog::ALL, ['jerror']);
	}


	protected function doExecute()
	{
		global $stableVersionPath, $installVersionPath;

		$tmpInstaller = new JInstaller;

		$isStable = $this->input->exists('stable');

		if ($isStable)
		{
			// Install the known good version
			$installResult = $tmpInstaller->install($stableVersionPath);

			return;
		}

		// Install the stable first. In another process to prevent having loaded the installation script already when installing the dev release.
		$myCommand = 'php ' . __FILE__ . ' --stable';
		passthru($myCommand);

		// Install the dev version
		$installResult = $tmpInstaller->install($installVersionPath);

		// Dump the installed version's version.txt file
		$path = JPATH_LIBRARIES . '/fof30/version.txt';
		readfile($path);
	}

	/**
	 * DO NOT EDIT BELOW THIS LINE
	 *
	 * This is necessary to install extensions from CLI
	 */

	public function flushAssets()
	{
		// This is an empty function since JInstall will try to flush the assets even if we're in CLI (!!!)
		return true;
	}

	public function getTemplate($params = false)
	{
		return '';
	}

	public function setHeader($name, $value, $replace = false)
	{
		return $this;
	}

	public function getCfg($name, $default = null)
	{
		return $this->get($name, $default);
	}

	public function getClientId()
	{
		return 1;
	}

	public function isClient($identifier)
	{
		return $identifier === 'administrator';
	}

	public function setUserState($key, $value)
	{
		$session  = &JFactory::getSession();
		$registry = &$session->get('registry');

		if (!is_null($registry))
		{
			return $registry->setValue($key, $value);
		}

		return null;
	}
}

try
{
	$cliApplication        = JApplicationCli::getInstance('FOFTestInstall');
	JFactory::$application = $cliApplication;
	$cliApplication->execute();
}
catch (Throwable $e)
{
	echo "\n\nERROR\n\n";
	echo $e->getCode() . '  --  ' . $e->getMessage() . "\n";
	echo $e->getFile() . ':' . $e->getLine() . "\n";
	echo $e->getTraceAsString() . "\n";
}
