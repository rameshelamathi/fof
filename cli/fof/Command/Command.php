<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Generator\Command;

use JFactory;
use JFile;
use JFolder;

abstract class Command
{
    /**
     * Parsed contents of composer.json
     *
     * @var \stdClass
     */
    protected $composer;

    /**
     * Input coming from the CLI
     *
     * @var \JInput
     */
    protected $input;

    /**
     * Name of the component
     *
     * @var string
     */
    protected $component;

    public function __construct($composer, $input)
    {
        $this->composer  = $composer;
        $this->input     = $input;
        $this->component = $this->getComponent($composer);
    }

    /**
     * This is where we execute the whole logic of the command
     *
     * @return
     */
    abstract public function execute();

	/**
	 * Get the component's name from the user
     *
	 * @return string The name of the component (com_foobar)
	 */
	protected function getComponentName($composer)
	{
		$extra        = (isset($composer->extra) && isset($composer->extra->fof)) ? $composer->extra->fof : false;
		$default_name = $extra ? $extra->name : array_pop(explode("/", $composer->name));
		$default_name = $default_name ? $default_name : 'com_foobar';

		// Add com_ if necessary
		if (stripos($default_name, "com_") !== 0)
        {
			$default_name = "com_" . $default_name;
		}

		$name = false;

		if (!$extra)
        {
            /** @var \FofApp $app */
            $app = JFactory::getApplication();

			$app->out("What's your component name? (" . $default_name . ")");
			$name = $this->in();
		}

		if (!$name)
        {
			$name = $default_name;
		}

		// Keep asking while the name is not valid
		while(!$name)
        {
			$name = $this->getComponentName($composer);
		}

		// Add com_ if necessary
		if (stripos($name, "com_") !== 0)
        {
			$name = "com_" . $name;
		}

		return strtolower($name);
	}

	/**
	 * Load the Joomla Configuration from a dev site
	 *
	 * @param boolean $force Should we ask the user even if we have a .fof file?
	 */
	public function setDevServer($force = false)
	{
        $path = '';

		// .fof file not found, ask the user!
		if (!JFile::exists(getcwd() . '/.fof') || $force)
        {
			$this->out("What's the dev site location? ( /var/www/ )");
			$path = $this->in();

			if (!$path || !JFolder::exists($path))
            {
				$this->out('The path does not exist');
				$this->setDevServer();
			}

			if (!JFile::exists($path . '/configuration.php'))
            {
				$this->out('The path does not contain a Joomla Website');
				$this->setDevServer();
			}

			$fof = array('dev' => $path);

			JFile::write(getcwd() . '/.fof', json_encode($fof));
		}
        else
        {
			$fof = json_decode(JFile::read(getcwd() . '/.fof'));

			if ($fof && $fof->dev)
            {
				$path = $fof->dev;
			}
		}

        if(!$path)
        {
            throw new \RuntimeException("Could not detect the path to the dev server");
        }

		// Load the configuration object.
        /** @var \FofApp $app */
        $app = JFactory::getApplication();
		$app->reloadConfiguration($path);
	}

	/**
	 * Proxy the in() call to the application
	 */
	protected function in()
    {
        /** @var \FofApp $app */
        $app = JFactory::getApplication();

		return $app->in();
	}

    /**
     * Proxy the out() call to the application
     *
     * @param   string $content     Outputs some text on the console
     *
     * @return \JApplicationCli
     */
	protected function out($content)
    {
        /** @var \FofApp $app */
        $app = JFactory::getApplication();

		return $app->out($content);
	}

    /**
     * Get the Component name from the composer file
     * @param  object $composer The composer.json info
     * @return string           The component name
     */
    protected function getComponent($composer)
    {
        // We do have a composer file, so we can start working
        $composer->extra = isset($composer->extra) ? $composer->extra : (object) array('fof' => array());
        $composer->extra->fof = isset($composer->extra->fof) ? $composer->extra->fof : array();

        $component = isset($composer->extra->fof->name) ? $composer->extra->fof->name : null;

        return $component;
    }

    /**
     * Get the view name from the input
     * @param  object $input The input object
     * @return string        The view name
     */
    protected function getViewName($input)
    {
        return ucfirst(strtolower($input->getString('name')));
    }
}