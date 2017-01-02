<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Generator\Command;

use FOF30\Generator\Command\Command as Command;

class Init extends Command
{
	public function execute()
    {
	    // If we do have a composer file use the information contained in it
	    $composer = $this->composer;

	    $composer->extra = isset($composer->extra) ? $composer->extra : array('fof' => new \stdClass());
	    $composer->extra->fof = isset($composer->extra->fof) ? $composer->extra->fof : new \stdClass();

	    $info = $composer->extra->fof;

	    if (!is_object($info))
	    {
		    if (empty($info))
		    {
			    $info = new \stdClass();
		    }
		    else
		    {
			    $info = (object) $info;
		    }
	    }

		// Component Name (default: what's already stored in composer / composer package name)
		$info->name = $this->getComponentName($composer);

		$files = array(
			'backend'               => 'component/backend',
			'frontend'              => 'component/frontend',
			'media'                 => 'component/media',
			'translationsbackend'   => 'translations/component/backend',
			'translationsfrontend'  => 'translations/component/frontend'
		);

	    if (!isset($info->paths) || empty($info->paths) || is_null($info->paths))
	    {
		    $info->paths = array();
	    }

	    if (is_object($info->paths))
	    {
		    $info->paths = (array) $info->paths;
	    }

	    $files = array_merge($files, $info->paths);

		foreach ($files as $key => $default)
        {
			$info->paths[$key] = $this->getPath($composer, $key, $default);
		}

		// Now check for fof.xml file
		$fof_xml = getcwd() .  '/' . $info->paths['backend'] . '/fof.xml';

		if (file_exists($fof_xml))
        {
            // @todo Read the XML?
		}

	    // @todo Maybe ask for namespaces?

		// Store back the info into the composer.json file
	    $composer->extra->fof = $info;
	    \JFile::write(getcwd() . '/composer.json', json_encode($composer, JSON_PRETTY_PRINT));

		$this->setDevServer(false);
	}


	/**
	 * Ask the user the path for each of the files folders
	 * @param  object $composer The composer json object
	 * @param  string $key      The key of the folder (backend)
	 * @param  string $default  The default path to use
	 * @return string           The user chosen path
	 */
	protected function getPath($composer, $key, $default)
	{
		$extra = $composer->extra ? $composer->extra->fof : false;
		$default_path = ($extra && $extra->paths && $extra->paths->$key) ? $extra->paths->$key : $default;

		// Keep asking while the path is not valid
		$path = false;

		$this->out("Location of " . $key . " files: [" . $default_path . "]");
		$path = $this->in();

		// Use the default path if needbe
		if (empty($path))
		{
			$path = $default_path;
		}

		// Create the directory if necessary
		if (!is_dir($path))
		{
			\JFolder::create(getcwd() . '/' . $path);
		}

		return $path;
	}
}