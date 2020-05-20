<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Form\Field;

defined('_JEXEC') || die;

use FOF30\Form\Exception\DataModelRequired;
use FOF30\Form\FieldInterface;
use FOF30\Form\Form;
use FOF30\Model\DataModel;
use JFormFieldList;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('list');

/**
 * Form Field class for FOF
 * Supports a generic list of options.
 *
 * @deprecated 3.1  Support for XML forms will be removed in FOF 4
 */
class Published extends JFormFieldList implements FieldInterface
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
		$class = $this->class ? ' class="' . $this->class . '"' : '';

		return '<span id="' . $this->id . '" ' . $class . '>' .
			htmlspecialchars(GenericList::getOptionName($this->getOptions(), $this->value), ENT_COMPAT, 'UTF-8') .
			'</span>';
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
		if (!($this->item instanceof DataModel))
		{
			throw new DataModelRequired(__CLASS__);
		}

		$prefix       = $this->element['prefix'] ? (string) $this->element['prefix'] : '';
		$checkbox     = $this->element['checkbox'] ? (string) $this->element['checkbox'] : 'cb';
		$publish_up   = $this->element['publish_up'] ? (string) $this->element['publish_up'] : null;
		$publish_down = $this->element['publish_down'] ? (string) $this->element['publish_down'] : null;
		$container    = $this->form->getContainer();
		$privilege    = $this->element['acl_privilege'] ? $this->element['acl_privilege'] : 'core.edit.state';
		$component    = $this->element['acl_component'] ? $this->element['acl_component'] : $container->componentName;
		$component    = empty($component) ? null : $component;
		$enabled      = $container->platform->getUser()->authorise($privilege, $component);

		// @todo Enforce ACL checks to determine if the field should be enabled or not
		// Get the HTML
		return HTMLHelper::_('jgrid.published', $this->value, $this->rowid, $prefix, $enabled, $checkbox, $publish_up, $publish_down);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 * @since 2.0
	 *
	 */
	protected function getOptions()
	{
		$options = parent::getOptions();

		if (!empty($options))
		{
			return $options;
		}

		// If no custom options were defined let's figure out which ones of the
		// defaults we shall use...

		$config = [
			'published'   => 1,
			'unpublished' => 1,
			'archived'    => 0,
			'trash'       => 0,
			'all'         => 0,
		];

		$configMap = [
			'show_published'   => ['published', 1],
			'show_unpublished' => ['unpublished', 1],
			'show_archived'    => ['archived', 0],
			'show_trash'       => ['trash', 0],
			'show_all'         => ['all', 0],
		];

		foreach ($configMap as $attribute => $preferences)
		{
			list($configKey, $default) = $preferences;

			switch (strtolower($this->element[$attribute]))
			{
				case 'true':
				case '1':
				case 'yes':
					$config[$configKey] = true;
					break;

				case 'false':
				case '0':
				case 'no':
					$config[$configKey] = false;
					break;

				default:
					$config[$configKey] = $default;
			}
		}

		$stack = [];

		if ($config['published'])
		{
			$stack[] = HTMLHelper::_('select.option', '1', \Joomla\CMS\Language\Text::_('JPUBLISHED'));
		}

		if ($config['unpublished'])
		{
			$stack[] = HTMLHelper::_('select.option', '0', \Joomla\CMS\Language\Text::_('JUNPUBLISHED'));
		}

		if ($config['archived'])
		{
			$stack[] = HTMLHelper::_('select.option', '2', \Joomla\CMS\Language\Text::_('JARCHIVED'));
		}

		if ($config['trash'])
		{
			$stack[] = HTMLHelper::_('select.option', '-2', \Joomla\CMS\Language\Text::_('JTRASHED'));
		}

		if ($config['all'])
		{
			$stack[] = HTMLHelper::_('select.option', '*', \Joomla\CMS\Language\Text::_('JALL'));
		}

		return $stack;
	}
}
