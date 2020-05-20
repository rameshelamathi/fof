<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Factory\Exception;

defined('_JEXEC') || die;

use Exception;
use Joomla\CMS\Language\Text;
use RuntimeException;

class FormNotFound extends RuntimeException
{
	public function __construct($formClass, $code = 500, Exception $previous = null)
	{
		$message = Text::sprintf('LIB_FOF_FORM_ERR_NOT_FOUND', $formClass);

		parent::__construct($message, $code, $previous);
	}

}
