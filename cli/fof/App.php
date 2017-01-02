<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */
use FOF30\Generator\Command\Command;

/**
 * FOF3 Generator App
 */
class FofApp extends JApplicationCli
{

	protected $admin = true;

	/**
	 * JApplicationCli didn't want to run on PHP CGI. I have my way of becoming
	 * VERY convincing. Now obey your true master, you petty class!
	 *
	 * @param JInputCli $input
	 * @param JRegistry $config
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
					$query	 = ltrim($query);
					$argv	 = explode(' ', $query);
					$argc	 = count($argv);
					$_SERVER['argv'] = $argv;
				}

				if (class_exists('JInputCLI'))
				{
					$this->input = new JInputCLI();
				}
				else
				{
					$this->input = new JInputCli();
				}
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

        // $_SERVER variables required by the view, let's fake them
        $_SERVER['HTTP_HOST'] = 'http://www.example.com';
	}

	/**
	 * The main entry point of the application
	 */
	public function execute()
	{
		$this->loadLibraries();
		$this->displayBanner();
		$this->disableTimeLimit();

		// Get arguments
		$args = (array) $this->input->args;

		$composer = $this->getComposerInfo();

		$phar = Phar::running(false);
		$path = $phar ? (Phar::running(true) . '/fof/') : (realpath(dirname(__FILE__)) . '/');

		// Register the current namespace with the autoloader
		FOF30\Autoloader\Autoloader::getInstance()->addMap('FOF30\\Generator\\', array($path));
		FOF30\Autoloader\Autoloader::getInstance()->register();

		// Get command
		$command = array_shift($args);
		$command = ucfirst(strtolower($command));

		// Run automatically every know command
		$class = 'FOF30\Generator\Command\\' . $command;

		try
        {
			if (class_exists($class))
            {
                /** @var Command $class */
				$class = new $class($composer, $this->input);
				$class->execute();
			}
		}
        catch(Exception $e)
        {
			$this->out($e->getMessage());

			exit();
		}
	}

	/**
	 * Load the informations from the composer.json file
	 * @return object The composer file informations
	 */
	protected function getComposerInfo()
	{
		$this->out("Checking for Existing Composer File...");

		// Does the composer file exists?
		if (!file_exists(getcwd() . '/composer.json'))
        {
			// Ask to create it
			$this->out("Can't find a composer.json file in this directory. Run \"composer init\" to create it.");

	        return new \stdClass();
		}

		// Read composer's informations
		$composer = json_decode(file_get_contents(getcwd() . '/composer.json'));

		return $composer;
	}

	/**
	 * Disable PHP time limit
	 */
	protected function disableTimeLimit()
	{
		// Unset time limits
		$safe_mode = true;

		if (function_exists('ini_get'))
		{
			$safe_mode = ini_get('safe_mode');
		}

		if (!$safe_mode && function_exists('set_time_limit'))
		{
			$this->out("Unsetting time limit restrictions");
			@set_time_limit(0);
		}
	}

	/**
	 * Perform tedious tasks as loading Joomla files, error handling, etc
	 */
	protected function loadLibraries()
	{
		// Set all errors to output the messages to the console, in order to
		// avoid infinite loops in JError ;)
		restore_error_handler();
		JError::setErrorHandling(E_ERROR, 'die');
		JError::setErrorHandling(E_WARNING, 'echo');
		JError::setErrorHandling(E_NOTICE, 'echo');

		// Required by Joomla!
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.filesystem.file');
		JLoader::import('joomla.filesystem.folder');

		// Allow inclusion of Joomla! files
		if (!defined('_JEXEC'))
        {
            define('_JEXEC', 1);
        }

		// Load FOF
		if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
		{
			die('FOF 3.0 is not installed');
		}
	}

	/**
	 * Display the generator banner informations
	 */
	protected function displayBanner()
	{
		$year			 = gmdate('Y');
		$phpversion		 = PHP_VERSION;
		$phpenvironment	 = PHP_SAPI;

		$this->out("FOF3 Generator");
		$this->out("Copyright (C) 2010-$year Nicholas K. Dionysopoulos");
		$this->out(str_repeat('-', 79));
		$this->out("FOF3 is Free Software, distributed under the terms of the GNU General");
		$this->out("Public License version 2 or, at your option, any later version.");
		$this->out("This program comes with ABSOLUTELY NO WARRANTY as per sections 15 & 16 of the");
		$this->out("license. See http://www.gnu.org/licenses/gpl-2.0.html for details.");
		$this->out(str_repeat('-', 79));
		$this->out("You are using PHP $phpversion ($phpenvironment)");
		$this->out("");
	}

	/**
	 * Load the Joomla Configuration from specific path
	 *
	 * @param string $path The directory where we should find the configuration.php file
	 */
	public function reloadConfiguration($path)
	{
		// Load the configuration object.
		$this->loadConfiguration($this->fetchConfigurationData($path . '/configuration.php'));
	}

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'system';
    }

    public function in()
    {
        return trim(fgets(STDIN));
    }


}