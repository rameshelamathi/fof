<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Generator\Command\Generate\Layout;

use FOF30\Generator\Command\Command as Command;
use FOF30\Factory\Scaffolding\Layout\Builder as LayoutBuilder;
use FOF30\Container\Container as Container;
use FOF30\Model\DataModel\Exception\NoTableColumns;

abstract class LayoutBase extends Command
{
	/**
	 * Create the xml file for a give view type

	 * @param  string $view      The view name
	 * @param  string $viewType  The type of the view (default, form, item)
	 * @param  boolean $backend   If it's for the backend
	 *
	 * @return string            The xml generated
	 *
	 * @throws \Exception Can throw exceptions. @see LayoutBuilder
	 */
	protected function createViewFile($view, $viewType, $backend)
	{
        // Let's force the use of the Magic Factory
		$container = Container::getInstance($this->component, array('factoryClass' => 'FOF30\\Factory\\MagicFactory'));
		$container->factory->setSaveScaffolding(true);

		// plural / singular
		if ($viewType != 'default')
		{
			$view = $container->inflector->singularize($view);
		}

		// Small trick: being in the CLI, the builder always tries to build in the frontend
		// Let's switch paths :)
		$originalFrontendPath = $container->frontEndPath;
		$originalBackendPath  = $container->backEndPath;

		$container->frontEndPath = $backend ? $container->backEndPath : $container->frontEndPath;

		$scaffolding = new LayoutBuilder($container);

		$return = $scaffolding->make('form.' . $viewType, $view);

		// And switch them back!
		$container->frontEndPath = $originalFrontendPath;
		$container->backEndPath  = $originalBackendPath;

		return $return;
	}

    /**
     * Create the view
     *
     * @param   string $viewType The type of the view (item, default, form)
     */
	protected function createView($viewType)
	{
		$this->setDevServer();

		$view = $this->getViewName($this->input);

		if (!$this->component)
		{
			$this->out("Can't find component details in composer.json file. Run 'fof init'");
			exit();
		}

		if (!$view)
		{
			$this->out("Syntax: fof generate " . $viewType . "view <name>");
			exit();
		}

		// Backend or frontend?
		$backend = !$this->input->get('frontend', false);

		try
        {
			// Create the view
			$this->createViewFile($view, $viewType, $backend);

			$message = $backend ? "Backend" : "Frontend";
			$message .= " " . $viewType . " view for " . $view . ' created!';

			// All ok!
			$this->out($message);

		}
        catch(\Exception $e)
        {
			if ($e instanceof NoTableColumns)
            {
				$container = Container::getInstance($this->component);

				$this->out("FOF cannot find a database table for " . $view . '. It should be named #__' . $this->component . '_' . strtolower($container->inflector->pluralize($view)));
				exit();
			}

			$this->out($e);

			exit();
		}
	}
}