<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Define ourselves as a parent file

// Try to get the path to the Joomla! installation
$joomlaPath = $_SERVER['HOME'] . '/Sites/dev3';

if (isset($_SERVER['JOOMLA_SITE']) && is_dir($_SERVER['JOOMLA_SITE']))
{
	$joomlaPath = $_SERVER['JOOMLA_SITE'];
}

if (!is_dir($joomlaPath))
{
	echo <<< TEXT


CONFIGURATION ERROR

Your configured path to the Joomla site does not exist. Rector requires loading
core Joomla classes to operate properly.

Please set the JOOMLA_SITE environment variable before running Rector.

Example:

JOOMLA_SITE=/var/www/joomla rector process $(pwd) --config rector.yaml \
  --dry-run

I will now error out. Bye-bye!

TEXT;

	throw new InvalidArgumentException("Invalid Joomla site root folder.");
}

// Required to run the boilerplate FOF CLI code
$originalDirectory = getcwd();
chdir($joomlaPath . '/cli');

// Setup and import the base CLI script
$minphp = '7.1.0';

// Boilerplate -- START
define('_JEXEC', 1);

foreach ([__DIR__, getcwd()] as $curdir)
{
	if (file_exists($curdir . '/defines.php'))
	{
		define('JPATH_BASE', realpath($curdir . '/..'));
		require_once $curdir . '/defines.php';

		break;
	}

	if (file_exists($curdir . '/../includes/defines.php'))
	{
		define('JPATH_BASE', realpath($curdir . '/..'));
		require_once $curdir . '/../includes/defines.php';

		break;
	}
}

defined('JPATH_LIBRARIES') || die ('This script must be placed in or run from the cli folder of your site.');

require_once __DIR__ . '/../fof/Cli/Application.php';
// Boilerplate -- END

// Undo the temporary change for the FOF CLI boilerplate code
chdir($originalDirectory);

// Load FOF 3
if (!defined('FOF30_INCLUDED') && !@include_once(__DIR__ . '/../fof/include.php'))
{
	throw new RuntimeException('FOF 3.0 is not installed', 500);
}

// Other classes
/** @var Composer\Autoload\ClassLoader $autoloader */
$autoloader = include(__DIR__ . '/../vendor/autoload.php');
$autoloader->addClassMap([
	# HTMLHelper classes do not follow namespaces
	'FEFHelperBrowse'                => __DIR__ . '/../fof/Utils/FEFHelper/browse.php',
	'FEFHelperEdit'                  => __DIR__ . '/../fof/Utils/FEFHelper/edit.php',
	'FEFHelperSelect'                => __DIR__ . '/../fof/Utils/FEFHelper/select.php',
	# Post-installation scripts, package and component
	'file_fof30InstallerScript'      => __DIR__ . '/../fof/script.fof.php',
	# Plugins
	'JFormFieldFofencryptedtoken'    => __DIR__ . '/../plugins/user/foftoken/fields/fofencryptedtoken.php',
	'PlgUserFoftoken'                => __DIR__ . '/../plugins/user/foftoken/foftoken.php',
	'plgUserFoftokenInstallerScript' => __DIR__ . '/../plugins/user/foftoken/script.php',
	# Joomla 3 classes
	'TagsHelperRoute'                => $joomlaPath . '/components/com_tags/helpers/route.php',
	# Akeeba FEF (must be installed in Joomla)
	'AkeebaFEFHelper'                => $joomlaPath . '/media/fef/fef.php',
]);
