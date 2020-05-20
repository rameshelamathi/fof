<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Form\Field;

use FOF30\Form\Exception\DataModelRequired;
use FOF30\Form\Exception\GetInputNotAllowed;
use FOF30\Form\Exception\GetStaticNotAllowed;
use FOF30\Form\FieldInterface;
use FOF30\Form\Form;
use FOF30\Model\DataModel;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use LogicException;

defined('_JEXEC') or die;

/**
 * Form Field class for FOF
 * Renders the checkbox in browse views which allows you to select rows
 *
 * @deprecated 3.1  Support for XML forms will be removed in FOF 4
 */
class SelectRow extends FormField implements FieldInterface
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
	 *
	 * @throws  LogicException
	 * @since 2.0
	 *
	 */
	public function getStatic()
	{
		throw new GetStaticNotAllowed(__CLASS__);
	}

	/**
	 * Get the rendering of this field type for a repeatable (grid) display,
	 * e.g. in a view listing many item (typically a "browse" task)
	 *
	 * @return  string  The field HTML
	 *
	 * @throws  DataModelRequired
	 * @since 2.0
	 *
	 */
	public function getRepeatable()
	{
		// Should I support checked-out elements?
		$checkoutSupport = false;

		if (isset($this->element['checkout']))
		{
			$checkoutSupportValue = (string) $this->element['checkout'];
			$checkoutSupport      = in_array(strtolower($checkoutSupportValue), ['yes', 'true', 'on', 1]);
		}

		if (!($this->item instanceof DataModel))
		{
			throw new DataModelRequired(__CLASS__);
		}

		// Is this record checked out?
		$userId      = $this->form->getContainer()->platform->getUser()->get('id', 0);
		$checked_out = false;

		if ($checkoutSupport)
		{
			$checked_out = $this->item->isLocked($userId);
		}

		// Get the key id for this record
		$key_field = $this->item->getKeyName();
		$key_id    = $this->item->$key_field;

		// Get the HTML
		return HTMLHelper::_('grid.id', $this->rowid, $key_id, $checked_out);
	}

	/**
	 * Method to get the field input markup for this field type.
	 *
	 * @return  string  The field input markup.
	 *
	 * @throws  GetInputNotAllowed
	 * @since 2.0
	 *
	 */
	protected function getInput()
	{
		throw new GetInputNotAllowed(__CLASS__);
	}
}
