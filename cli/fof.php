#!/bin/php
<?php
/**
 * @package     FOF
 * @author 		Daniele Rosario (daniele@weble.it)
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 *
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  --
 *
 *  Command-line generator for FOF3 Save Scaffolding feature
 */

$phar = class_exists('Phar') && Phar::running(false);

if ($phar)
{
	Phar::interceptFileFuncs();
	$phar_path = Phar::running(true) . '/';
}
else
{
	$phar_path = realpath(dirname(__FILE__)) . '/';
}

// Try to set 0755
@chmod(__FILE__, 0755);

// Define ourselves as a parent file
define('_JEXEC', 1);

// Required by the CMS
define('DS', DIRECTORY_SEPARATOR);

// JSON_PRETTY_PRINT only in PHP 5.4.0
$minphp = '5.4.0';

if (version_compare(PHP_VERSION, $minphp, 'lt'))
{
	$curversion = PHP_VERSION;
	$bindir = PHP_BINDIR;

	echo <<< ENDWARNING
================================================================================
WARNING! Incompatible PHP version $curversion
================================================================================
This CRON script must be run using PHP version $minphp or later. Your server is
currently using a much older version which would cause this script to crash. As
a result we have aborted execution of the script. Please contact your host and
ask them for the correct path to the PHP CLI binary for PHP $minphp or later, then
edit your CRON job and replace your current path to PHP with the one your host
gave you.
For your information, the current PHP version information is as follows.
PATH:    $bindir
VERSION: $curversion
Further clarifications:
1. There is absolutely no possible way that you are receiving this warning in
   error. We are using the PHP_VERSION constant to detect the PHP version you
   are currently using. This is what PHP itself reports as its own version. It
   simply cannot lie.
2. Even though your *site* may be running in a higher PHP version that the one
   reported above, your CRON scripts will most likely not be running under it.
   This has to do with the fact that your site DOES NOT run under the command
   line and there are different executable files (binaries) for the web and
   command line versions of PHP.
3. Please note that you MUST NOT ask us for support about this error. We cannot
   possibly know the correct path to the PHP CLI binary as we have not set up
   your server. Your host must know and give that information.
4. The latest published versions of PHP can be found at http://www.php.net/
   Any older version is considered insecure and must NOT be used on a live
   server. If your server uses a much older version of PHP than that please
   notify them that their servers are insecure and in need of an update.
This script will now terminate. Goodbye.
ENDWARNING;
	die();
}

$cwd = getcwd();

// Are we in a joomla site (/cli/fof.php) ?
if (file_exists(dirname(__DIR__) . '/includes/defines.php'))
{
	$dir = dirname(__DIR__);
}
else
{
	// Do we have .fof file?
	if (!file_exists(getcwd() . '/.fof'))
    {
		fwrite(STDOUT, "Could not find a .fof file. Let me generate it for you \n");

		// Get the site dev path
		$path = false;

		while (!$path)
        {
	        $default_path = getcwd();

			// Get Path to the dev site
			fwrite(STDOUT, "What's the dev site location? [$default_path]\n");
			$path = trim(fgets(STDIN));

            if (empty($path))
	        {
		        $path = $default_path;
	        }

			if (!$path || !is_dir($path))
            {
				$path = false;
				fwrite(STDOUT, "The path does not exist\n");
			}

			// Check if it's joomla
			if (!is_dir($path . '/libraries'))
            {
				$path = false;
				fwrite(STDOUT, "The path does not seem to contain a Joomla! Website\n");
			}
		}

		// All ok, write the .fof file
		$fof = array('dev' => $path);
		$fofFile = fopen(getcwd() . '/.fof', 'w');

		fwrite($fofFile, json_encode($fof));
		fclose($fofFile);
	}

	// load from .fof file
	$fof = json_decode(file_get_contents(getcwd() . '/.fof'));

	if ($fof && $fof->dev)
    {
		$dir = $fof->dev;
	}
}

if (!defined('_JDEFINES'))
{
	$path = rtrim($dir, DIRECTORY_SEPARATOR);
	define('JPATH_BASE', $path);

	require_once JPATH_BASE . '/includes/defines.php';
}

// Load the rest of the necessary files
if (file_exists(JPATH_LIBRARIES . '/import.legacy.php'))
{
	require_once JPATH_LIBRARIES . '/import.legacy.php';
}
else
{
	require_once JPATH_LIBRARIES . '/import.php';
}

require_once JPATH_LIBRARIES . '/cms.php';

JLoader::import('joomla.application.cli');
JLoader::import('joomla.application.component.helper');
JLoader::import('cms.component.helper');

if (version_compare(JVERSION, '3.4.9999', 'ge'))
{
	// Joomla! 3.5 and later does not load the configuration.php unless you explicitly tell it to.
	JFactory::getConfig(JPATH_CONFIGURATION . '/configuration.php');
}

// load the app
require_once $phar_path . 'fof/App.php';

$app = JApplicationCli::getInstance('FofApp');
\JFactory::$application = $app;
$app->execute();