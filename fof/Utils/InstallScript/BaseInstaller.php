<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Utils\InstallScript;

use DirectoryIterator;
use Exception;
use JFile;
use JFolder;
use JLoader;
use JLog;

defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.folder');
JLoader::import('joomla.filesystem.file');
JLoader::import('joomla.installer.installer');
JLoader::import('joomla.utilities.date');

class BaseInstaller
{
	/**
	 * Recursively copy a bunch of files, but only if the source and target file have a different size.
	 *
	 * @param   string $source Path to copy FROM
	 * @param   string $dest   Path to copy TO
	 *
	 * @return  void
	 */
	protected function recursiveConditionalCopy($source, $dest)
	{
		// Make sure source and destination exist
		if (!@is_dir($source))
		{
			return;
		}

		if (!@is_dir($dest))
		{
			if (!@mkdir($dest, 0755))
			{
				JFolder::create($dest, 0755);
			}
		}

		if (!@is_dir($dest))
		{
			$this->log(__CLASS__ . ": Cannot create folder $dest");

			return;
		}

		// List the contents of the source folder
		try
		{
			$di = new DirectoryIterator($source);
		}
		catch (Exception $e)
		{
			return;
		}

		// Process each entry
		foreach ($di as $entry)
		{
			// Ignore dot dirs (. and ..)
			if ($entry->isDot())
			{
				continue;
			}

			$sourcePath = $entry->getPathname();
			$fileName   = $entry->getFilename();

			// If it's a directory do a recursive copy
			if ($entry->isDir())
			{
				$this->recursiveConditionalCopy($sourcePath, $dest . DIRECTORY_SEPARATOR . $fileName);

				continue;
			}

			// If it's a file check if it's missing or identical
			$mustCopy   = false;
			$targetPath = $dest . DIRECTORY_SEPARATOR . $fileName;

			if (!@is_file($targetPath))
			{
				$mustCopy = true;
			}
			else
			{
				$sourceSize = @filesize($sourcePath);
				$targetSize = @filesize($targetPath);

				$mustCopy = $sourceSize != $targetSize;
			}

			if (!$mustCopy)
			{
				continue;
			}

			if (!@copy($sourcePath, $targetPath))
			{
				if (!JFile::copy($sourcePath, $targetPath))
				{
					$this->log(__CLASS__ . ": Cannot copy $sourcePath to $targetPath");
				}
			}
		}
	}

	/**
	 * Try to log a warning / error with Joomla
	 *
	 * @param   string $message  The message to write to the log
	 * @param   bool   $error    Is this an error? If not, it's a warning. (default: false)
	 * @param   string $category Log category, default jerror
	 *
	 * @return  void
	 */
	protected function log($message, $error = false, $category = 'jerror')
	{
		// Just in case...
		if (!class_exists('JLog', true))
		{
			return;
		}

		$priority = $error ? JLog::ERROR : JLog::WARNING;

		try
		{
			JLog::add($message, $priority, $category);
		}
		catch (Exception $e)
		{
			// Swallow the exception.
		}
	}
}