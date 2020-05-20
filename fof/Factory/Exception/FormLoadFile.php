<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Factory\Exception;

use Exception;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class FormLoadFile extends FormLoadGeneric
{
	public function __construct($file = "", $code = 500, Exception $previous = null)
	{
		$message = Text::sprintf('LIB_FOF_FORM_ERR_COULD_NOT_LOAD_FROM_FILE', $file);

		parent::__construct($message, $code, $previous);
	}

}
