<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Factory\Exception;

use Exception;
use Joomla\CMS\Language\Text;class FormLoadData extends FormLoadGeneric
{
	public function __construct($message = "", $code = 500, Exception $previous = null)
	{
		if (empty($message))
		{
			$message = Text::_('LIB_FOF_FORM_ERR_COULD_NOT_LOAD_FROM_DATA');
		}

		parent::__construct($message, $code, $previous);
	}

}
