<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Form\Exception;

defined('_JEXEC') || die;

use Exception;
use InvalidArgumentException;
use Joomla\CMS\Language\Text;

/**
 * Class InvalidGroupContents
 * @package    FOF30\Form\Exception
 * @deprecated 3.1  Support for XML forms will be removed in FOF 4
 */
class InvalidGroupContents extends InvalidArgumentException
{
	public function __construct($className, $code = 1, Exception $previous = null)
	{
		$message = Text::sprintf('LIB_FOF_FORM_ERR_GETOPTIONS_INVALID_GROUP_CONTENTS', $className);

		parent::__construct($message, $code, $previous);
	}
}
