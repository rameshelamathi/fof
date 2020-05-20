<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Form\Field;

defined('_JEXEC') || die;

use FOF30\Form\Field\Text as TextField;
use FOF30\Form\FieldInterface;
use FOF30\Form\Form;
use FOF30\Utils\StringHelper;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('text');

/**
 * Form Field class for the FOF framework
 * Supports a button input.
 *
 * @deprecated 3.1  Support for XML forms will be removed in FOF 4
 */
class Button extends TextField implements FieldInterface
{
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
	 * Get the rendering of this field type for static display, e.g. in a single
	 * item view (typically a "read" task).
	 *
	 * @return  string  The field HTML
	 * @since 2.0
	 *
	 */
	public function getStatic()
	{
		return $this->getInput();
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
		return $this->getInput();
	}

	/**
	 * Get the rendering of this field type for static display, e.g. in a single
	 * item view (typically a "read" task).
	 *
	 * @return  string  The field HTML
	 * @since 2.0
	 *
	 */
	public function getInput()
	{
		$this->label = '';

		$allowedElement = ['button', 'a'];

		if (in_array($this->element['htmlelement'], $allowedElement))
		{
			$type = $this->element['htmlelement'];
		}
		else
		{
			$type = 'button';
		}

		$text  = $this->element['text'] ? (string) $this->element['text'] : '';
		$class = $this->class ? $this->class : '';
		$icon  = $this->element['icon'] ? '<span class="icon ' . (string) $this->element['icon'] . '"></span> ' : '';

		if ($this->element['listItemTask'])
		{
			$this->onclick = "listItemTask('cb" . $this->item->getId() . "', '" . (string) $this->element['listItemTask'] . "')";
		}

		$onclick  = $this->onclick ? 'onclick="' . $this->onclick . '" ' : '';
		$url      = $this->element['url'] ? 'href="' . $this->parseFieldTags((string) $this->element['url']) . '" ' : '';
		$title    = $this->element['title'] ? 'title="' . Text::_((string) $this->element['title']) . '" ' : '';
		$useValue = StringHelper::toBool((string) $this->element['use_value']);

		if (!$useValue)
		{
			$this->value = Text::_($text);
		}

		$html = '<' . $type . ' id="' . $this->id . '" class="btn ' . $class . '" ' . $onclick . $url . $title . '>';
		$html .= $icon . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
		$html .= '</' . $type . '>';

		return $html;
	}

	/**
	 * Method to get the field title.
	 *
	 * @return  string  The field title.
	 */
	protected function getTitle()
	{
		return null;
	}
}
