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
use JFormFieldPlugins;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('plugins');

/**
 * Form Field class for FOF
 * Plugins installed on the site
 *
 * @deprecated 3.1  Support for XML forms will be removed in FOF 4
 */
class Plugins extends JFormFieldPlugins implements FieldInterface
{
	/**
	 * A monotonically increasing number, denoting the row number in a repeatable view
	 *
	 * @var  int
	 */
	public $rowid;
	/**
	 * The item being rendered in a repeatable form field
	 *
	 * @var  DataModel
	 */
	public $item;
	/**
	 * @var  string  Static field output
	 */
	protected $static;
	/**
	 * @var  string  Repeatable field output
	 */
	protected $repeatable;
	/**
	 * The Form object of the form attached to the form field.
	 *
	 * @var    Form
	 */
	protected $form;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   2.0
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
	 * Get the rendering of this field type for static display, e.g. in a single
	 * item view (typically a "read" task).
	 *
	 * @return  string  The field HTML
	 * @since 2.0
	 *
	 */
	public function getStatic()
	{
		if (isset($this->element['legacy']))
		{
			return $this->getInput();
		}

		$options = [
			'id' => $this->id,
		];

		return $this->getFieldContents($options);
	}

	/**
	 * Get the rendering of this field type for a repeatable (grid) display,
	 * e.g. in a view listing many item (typically a "browse" task)
	 *
	 * @return  string  The field HTML
	 * @since 2.0
	 *
	 */
	public function getRepeatable()
	{
		if (isset($this->element['legacy']))
		{
			return $this->getInput();
		}

		$options = [
			'class' => $this->id,
		];

		return $this->getFieldContents($options);
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @param   array  $fieldOptions  Options to be passed into the field
	 *
	 * @return  string  The field HTML
	 */
	public function getFieldContents(array $fieldOptions = [])
	{
		$id    = isset($fieldOptions['id']) ? 'id="' . $fieldOptions['id'] . '" ' : '';
		$class = $this->class . (isset($fieldOptions['class']) ? ' ' . $fieldOptions['class'] : '');

		return '<span ' . ($id ?: '') . 'class="' . $class . '">' .
			htmlspecialchars(GenericList::getOptionName($this->getOptions(), $this->value), ENT_COMPAT, 'UTF-8') .
			'</span>';
	}
}
