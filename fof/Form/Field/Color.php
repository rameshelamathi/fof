<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Form\Field;

defined('_JEXEC') || die;

use FOF30\Form\FieldInterface;
use FOF30\Form\Form;
use FOF30\Model\DataModel;
use JFormFieldColor;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('color');

/**
 * Form Field class for the FOF framework
 * Color field
 *
 * @deprecated 3.1  Support for XML forms will be removed in FOF 4
 */
class Color extends JFormFieldColor implements FieldInterface
{
	/**
	 * The item being rendered in a repeatable form field.
	 *
	 * @var DataModel
	 */
	public $item;
	/**
	 * A monotonically increasing number, denoting the row number in a repeatable view.
	 *
	 * @var int
	 */
	public $rowid;
	/**
	 * The Form object of the form attached to the form field.
	 *
	 * @var Form
	 */
	protected $form;
	/**
	 * Repeatable field output.
	 *
	 * @var string
	 */
	protected $repeatable;
	/**
	 * Static field output.
	 *
	 * @var string
	 */
	protected $static;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field
	 * object.
	 *
	 * @access    public
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 *
	 * @return    mixed    The property value or null.
	 * @since     2.0
	 *
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'static':
				if (empty($this->static))
				{
					$this->static = $this->getStatic();
				}

				return $this->static;
				break;

			case 'repeatable':
				if (empty($this->repeatable))
				{
					$this->repeatable = $this->getRepeatable();
				}

				return $this->repeatable;
				break;

			default:
				return parent::__get($name);
		}
	}

	/**
	 * Get the rendering of this field type for a repeatable (grid) display, e.g.
	 * in a view listing many item (typically a "browse" task)
	 *
	 * @access    public
	 *
	 *
	 * @return    string    The field HTML
	 * @since     2.0
	 *
	 */
	public function getRepeatable()
	{
		if (isset($this->element['legacy']))
		{
			return $this->getInput();
		}

		$class    = $this->class ?: '';
		$hexColor = '#' . ltrim($this->value, '#');

		return '<div class="' . $this->id . ' ' . $class
			. '" style="width:20px; height:20px; background-color:' . $hexColor . ';">' .
			'</div>';

	}

	/**
	 * Get the rendering of this field type for static display, e.g. in a single
	 * item view (typically a "read" task).
	 *
	 * @access    public
	 *
	 *
	 * @return    string    The field HTML
	 * @since     2.0
	 *
	 */
	public function getStatic()
	{
		// Return the joomla native control
		return $this->getInput();
	}

}
