<?php
/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Utils\FEFHelper;

defined('_JEXEC') or die;

use FOF30\Container\Container;
use FOF30\Model\DataModel;
use FOF30\Utils\SelectOptions;
use FOF30\View\View;
use JHtml;
use JText;

/**
 * An HTML helper for Browse views.
 *
 * It reintroduces a FEF-friendly of some of the functionality found in FOF 3's Header and Field classes. These
 * helpers are also accessible through Blade, making the transition from XML forms to Blade templates easier.
 *
 * @since 3.3.0
 */
abstract class BrowseView
{
	/**
	 * Get the translation key for a field's label
	 *
	 * @param string $fieldName The field name
	 *
	 * @return string
	 *
	 * @since 3.3.0
	 */
	public static function fieldLabelKey($fieldName)
	{
		$view = self::getViewFromBacktrace();

		try
		{
			$inflector   = $view->getContainer()->inflector;
			$viewName    = $inflector->singularize($view->getName());
			$altViewName = $inflector->pluralize($view->getName());

			$keys = [
				strtoupper($view->getContainer()->componentName . '_' . $viewName . '_FIELD_' . $fieldName),
				strtoupper($view->getContainer()->componentName . '_' . $altViewName . '_FIELD_' . $fieldName),
			];

			foreach ($keys as $key)
			{
				if (JText::_($key) != $key)
				{
					return $key;
				}
			}

			return $key;
		}
		catch (\Exception $e)
		{
			return ucfirst($fieldName);
		}
	}

	/**
	 * Returns the label for a field (translated)
	 *
	 * @param string $fieldName The field name
	 *
	 * @return string
	 */
	public static function fieldLabel($fieldName)
	{
		return JText::_(self::fieldLabelKey($fieldName));
	}

	/**
	 * Create a browse view filter from values returned by a model
	 *
	 * @param string $localField      Field name
	 * @param string $modelTitleField Foreign model field for drop-down display values
	 * @param null   $modelName       Foreign model name
	 * @param array  $params          Generic select display parameters
	 *
	 * @return string
	 *
	 * @since 3.3.0
	 */
	public static function modelFilter($localField, $modelTitleField = 'title', $modelName = null, $placeholder = null, array $params = [])
	{
		/** @var DataModel $model */
		$model = self::getViewFromBacktrace()->getModel();

		if (empty($modelName))
		{
			$modelName = $model->getForeignModelNameFor($localField);
		}

		if (is_null($placeholder))
		{
			$placeholder = self::fieldLabelKey($localField);
		}

		$params = array_merge([
			'list.none'      => '&mdash; ' . JText::_($placeholder) . ' &mdash;',
			'value_field'    => $modelTitleField,
			'fof.autosubmit' => true,
		], $params);

		return self::modelSelect($localField, $modelName, $model->getState($localField), $params);
	}

	/**
	 * Create a browse view filter with dropdown values
	 *
	 * @param string $localField Field name
	 * @param array  $options    The JHtml options list to use
	 * @param array  $params     Generic select display parameters
	 *
	 * @return string
	 *
	 * @since 3.3.0
	 */
	public static function selectFilter($localField, array $options, $placeholder = null, array $params = [])
	{
		/** @var DataModel $model */
		$model = self::getViewFromBacktrace()->getModel();

		if (is_null($placeholder))
		{
			$placeholder = self::fieldLabelKey($localField);
		}

		$params = array_merge([
			'list.none'      => '&mdash; ' . JText::_($placeholder) . ' &mdash;',
			'fof.autosubmit' => true,
		], $params);

		return self::genericSelect($localField, $options, $model->getState($localField), $params);
	}

	public static function accessFilter($localField, $placeholder = null, array $params = [])
	{
		return self::selectFilter($localField, SelectOptions::getOptions('access', $params), $placeholder, $params);
	}

	public static function publishedFilter($localField, $placeholder = null, array $params = [])
	{
		return self::selectFilter($localField, SelectOptions::getOptions('published', $params), $placeholder, $params);
	}

	/**
	 * Create a select box from the values returned by a model
	 *
	 * @param string $name         Field name
	 * @param string $modelName    The name of the model, e.g. "items" or "com_foobar.items"
	 * @param string $currentValue The currently selected value
	 * @param array  $params       Passed to optionsFromModel and genericSelect
	 * @param array  $modelState   Optional state variables to pass to the model
	 * @param array  $options      Any JHtml select options you want to add in front of the model's returned values
	 *
	 * @see   self::getOptionsFromModel
	 * @see   self::getOptionsFromSource
	 * @see   self::genericSelect
	 *
	 * @return string
	 *
	 * @since 3.3.0
	 */
	public static function modelSelect($name, $modelName, $currentValue, array $params = [], array $modelState = [], array $options = [])
	{
		$params = array_merge([
			'fof.autosubmit' => true,
		], $params);

		$options = self::getOptionsFromModel($modelName, $params, $modelState, $options);

		return self::genericSelect($name, $options, $currentValue, $params);
	}

	/**
	 * Create a generic select list based on a bunch of options. Option sources will be merged into the provided
	 * options automatically.
	 *
	 * Parameters:
	 * - format.depth The current indent depth.
	 * - format.eol The end of line string, default is linefeed.
	 * - format.indent The string to use for indentation, default is tab.
	 * - groups If set, looks for keys with the value "<optgroup>" and synthesizes groups from them. Deprecated. Default: true.
	 * - list.select Either the value of one selected option or an array of selected options. Default: $currentValue.
	 * - list.translate If true, text and labels are translated via JText::_(). Default is false.
	 * - list.attr HTML element attributes (key/value array or string)
	 * - list.none Placeholder for no selection (creates an option with an empty string key)
	 * - option.id The property in each option array to use as the selection id attribute. Defaults: null.
	 * - option.key The property in each option array to use as the Default: "value". If set to null, the index of the option array is used.
	 * - option.label The property in each option array to use as the selection label attribute. Default: null
	 * - option.text The property in each option array to use as the displayed text. Default: "text". If set to null, the option array is assumed to be a list of displayable scalars.
	 * - option.attr The property in each option array to use for additional selection attributes. Defaults: null.
	 * - option.disable: The property that will hold the disabled state. Defaults to "disable".
	 * - fof.autosubmit Should I auto-submit the form on change? Default: true
	 * - fof.formname Form to auto-submit. Default: adminForm
	 * - class CSS class to apply
	 * - size Size attribute for the input
	 * - multiple Is this a multiple select? Default: false.
	 * - required Is this a required field? Default: false.
	 * - autofocus Should I focus this field automatically? Default: false
	 * - disabled Is this a disabled field? Default: false
	 * - readonly Render as a readonly field with hidden inputs? Overrides 'disabled'. Default: false
	 * - onchange Custom onchange handler. Overrides fof.autosubmit. Default: NULL (use fof.autosubmit).
	 *
	 * @param       $name
	 * @param array $options
	 * @param       $currentValue
	 * @param array $params
	 *
	 * @return string
	 *
	 * @since 3.3.0
	 */
	public static function genericSelect($name, array $options, $currentValue, array $params = [])
	{
		$params = array_merge([
			'format.depth'   => 0,
			'format.eol'     => "\n",
			'format.indent'  => "\t",
			'groups'         => true,
			'list.select'    => $currentValue,
			'list.translate' => false,
			'option.id'      => null,
			'option.key'     => 'value',
			'option.label'   => null,
			'option.text'    => 'text',
			'option.attr'    => null,
			'option.disable' => 'disable',
			'list.attr'      => '',
			'list.none'      => '',
			'id'             => null,
			'fof.autosubmit' => true,
			'fof.formname'   => 'adminForm',
			'class'          => '',
			'size'           => '',
			'multiple'       => false,
			'required'       => false,
			'autofocus'      => false,
			'disabled'       => false,
			'onchange'       => null,
			'readonly'       => false,
		], $params);

		$currentValue = $params['list.select'];

		// If fof.autosubmit is enabled and onchange is not set we will add our own handler
		if ($params['fof.autosubmit'] && is_null($params['onchange']))
		{
			$formName           = $params['fof.formname'] ? $params['fof.formname'] : 'adminForm';
			$params['onchange'] = "document.{$formName}.submit()";
		}

		// Construct SELECT element's attributes
		$attr = '';
		$attr .= $params['class'] ? ' class="' . $params['class'] . '"' : '';
		$attr .= !empty($params['size']) ? ' size="' . $params['size'] . '"' : '';
		$attr .= $params['multiple'] ? ' multiple' : '';
		$attr .= $params['required'] ? ' required aria-required="true"' : '';
		$attr .= $params['autofocus'] ? ' autofocus' : '';
		$attr .= ($params['disabled'] || $params['readonly']) ? ' disabled="disabled"' : '';
		$attr .= $params['onchange'] ? ' onchange="' . $params['onchange'] . '"' : '';

		// We use the constructed SELECT element's attributes only if no 'attr' key was provided
		if (empty($params['list.attr']))
		{
			$params['list.attr'] = $attr;
		}

		// Merge the options with those fetched from a source (e.g. another Helper object)
		$options = array_merge($options, self::getOptionsFromSource($params));

		if (!empty($params['list.none']))
		{
			array_unshift($options, JHtml::_('FEFHelper.select.option', '', JText::_($params['list.none'])));
		}

		$html = [];

		// Create a read-only list (no name) with hidden input(s) to store the value(s).
		if ($params['readonly'])
		{
			$html[] = JHtml::_('FEFHelper.select.genericlist', $options, $name, $params);

			// E.g. form field type tag sends $this->value as array
			if ($params['multiple'] && is_array($currentValue))
			{
				if (!count($currentValue))
				{
					$currentValue[] = '';
				}

				foreach ($currentValue as $value)
				{
					$html[] = '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"/>';
				}
			}
			else
			{
				$html[] = '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"/>';
			}
		}
		else
			// Create a regular list.
		{
			$html[] = JHtml::_('FEFHelper.select.genericlist', $options, $name, $params);
		}

		return implode($html);
	}

	/**
	 * Replace tags that reference fields with their values
	 *
	 * @param   string    $text Text to process
	 * @param   DataModel $item The DataModel instance to get values from
	 *
	 * @return  string         Text with tags replace
	 *
	 * @since 3.3.0
	 */
	public static function parseFieldTags($text, DataModel $item)
	{
		$ret = $text;

		if (empty($item))
		{
			return $ret;
		}

		/**
		 * Replace [ITEM:ID] in the URL with the item's key value (usually: the auto-incrementing numeric ID)
		 */
		$replace = $item->getId();
		$ret     = str_replace('[ITEM:ID]', $replace, $ret);

		// Replace the [ITEMID] in the URL with the current Itemid parameter
		$ret = str_replace('[ITEMID]', $item->getContainer()->input->getInt('Itemid', 0), $ret);

		// Replace the [TOKEN] in the URL with the Joomla! form token
		$ret = str_replace('[TOKEN]', $item->getContainer()->platform->getToken(true), $ret);

		// Replace other field variables in the URL
		$data = $item->getData();

		foreach ($data as $field => $value)
		{
			// Skip non-processable values
			if (is_array($value) || is_object($value))
			{
				continue;
			}

			$search = '[ITEM:' . strtoupper($field) . ']';
			$ret    = str_replace($search, $value, $ret);
		}

		return $ret;
	}

	/**
	 * Get JHtml options from an alternate source, e.g. a helper. This is useful for adding arbitrary options
	 * which are either dynamic or you do not want to inline to your view, e.g. reusable options across
	 * different views.
	 *
	 * The attribs can be:
	 * source_file          The file to load. You can use FOF's URIs such as 'admin:com_foobar/foo/bar'
	 * source_class         The class to use
	 * source_method        The static method to use on source_class
	 * source_key           Use * if you're returning a key/value array. Otherwise the array key for the key (ID) value.
	 * source_value         Use * if you're returning a key/value array. Otherwise the array key for the displayed value.
	 * source_translate     Should I pass the value field through JText? Default: true
	 * source_format        Set to "optionsobject" if you're returning an array of JHtml options. Ignored otherwise.
	 *
	 * @param  array $attribs
	 *
	 * @return array
	 *
	 * @since 3.3.0
	 */
	private static function getOptionsFromSource(array $attribs = [])
	{
		$options = [];

		$container = self::getContainerFromBacktrace();

		$attribs = array_merge([
			'source_file'      => '',
			'source_class'     => '',
			'source_method'    => '',
			'source_key'       => '*',
			'source_value'     => '*',
			'source_translate' => true,
			'source_format'    => '',
		], $attribs);

		$source_file      = $attribs['source_file'];
		$source_class     = $attribs['source_class'];
		$source_method    = $attribs['source_method'];
		$source_key       = $attribs['source_key'];
		$source_value     = $attribs['source_value'];
		$source_translate = $attribs['source_translate'];
		$source_format    = $attribs['source_format'];

		if ($source_class && $source_method)
		{
			// Maybe we have to load a file?
			if (!empty($source_file))
			{
				$source_file = $container->template->parsePath($source_file, true);

				if ($container->filesystem->fileExists($source_file))
				{
					include $source_file;
				}
			}

			// Make sure the class exists
			if (class_exists($source_class, true))
			{
				// ...and so does the option
				if (in_array($source_method, get_class_methods($source_class)))
				{
					// Get the data from the class
					if ($source_format == 'optionsobject')
					{
						$options = array_merge($options, $source_class::$source_method());
					}
					else
					{
						$source_data = $source_class::$source_method();

						// Loop through the data and prime the $options array
						foreach ($source_data as $k => $v)
						{
							$key   = (empty($source_key) || ($source_key == '*')) ? $k : @$v[$source_key];
							$value = (empty($source_value) || ($source_value == '*')) ? $v : @$v[$source_value];

							if ($source_translate)
							{
								$value = JText::_($value);
							}

							$options[] = JHtml::_('FEFHelper.select.option', $key, $value, 'value', 'text');
						}
					}
				}
			}
		}

		reset($options);

		return $options;
	}

	/**
	 * Get JHtml options from the values returned by a model.
	 *
	 * The params can be:
	 * key_field        The model field used for the OPTION's key. Default: the model's ID field.
	 * value_field      The model field used for the OPTION's displayed value. You must provide it.
	 * apply_access     Should I apply Joomla ACLs to the model? Default: FALSE.
	 * none             Placeholder for no selection. Default: NULL (no placeholder).
	 * translate        Should I pass the values through JText? Default: TRUE.
	 * with             Array of relation names for eager loading.
	 *
	 * @param string $modelName  The name of the model, e.g. "items" or "com_foobar.items"
	 * @param array  $params     Parameters which define which options to get from the model
	 * @param array  $modelState Optional state variables to pass to the model
	 * @param array  $options    Any JHtml select options you want to add in front of the model's returned values
	 *
	 * @return mixed
	 *
	 * @since 3.3.0
	 */
	private static function getOptionsFromModel($modelName, array $params = [], array $modelState = [], array $options = [])
	{
		// Let's find the FOF DI container from the call stack
		$container = self::getContainerFromBacktrace();

		// Explode model name into component name and prefix
		$componentName = $container->componentName;
		$mName         = $modelName;

		if (strpos($modelName, '.') !== false)
		{
			list ($componentName, $mName) = explode('.', $mName, 2);
		}

		if ($componentName != $container->componentName)
		{
			$container = Container::getInstance($componentName);
		}

		/** @var DataModel $model */
		$model = $container->factory->model($mName)->setIgnoreRequest(true)->savestate(false);

		$defaultParams = [
			'key_field'    => $model->getKeyName(),
			'value_field'  => 'title',
			'apply_access' => false,
			'none'         => null,
			'translate'    => true,
			'with'         => [],
		];

		$params = array_merge($defaultParams, $params);

		if (empty($defaultParams['none']) && !is_null($defaultParams['none']))
		{
			$langKey     = strtoupper($model->getContainer()->componentName . '_TITLE_' . $model->getName());
			$placeholder = JText::_($langKey);

			if ($langKey != $placeholder)
			{
				$defaultParams['none'] = '&mdash; ' . $placeholder . ' &mdash;';
			}
		}

		if (!empty($defaultParams['none']))
		{
			$options[] = JHtml::_('FEFHelper.select.option', null, JText::_($defaultParams['none']));
		}


		if ($params['apply_access'])
		{
			$model->applyAccessFiltering();
		}

		if (!is_null($params['with']))
		{
			$model->with($params['with']);
		}

		// Set the model's state, if applicable
		foreach ($modelState as $stateKey => $stateValue)
		{
			$model->setState($stateKey, $stateValue);
		}

		// Set the query and get the result list.
		$items = $model->get(true);

		// Build the field options.
		if (!empty($items))
		{
			foreach ($items as $item)
			{
				$value = $item->{$params['value_field']};

				if ($params['translate'])
				{
					$value = JText::_($value);
				}

				$options[] = JHtml::_('FEFHelper.select.option', $item->{$params['key_field']}, $value);
			}
		}

		return $options;
	}

	/**
	 * Get the FOF DI container from the backtrace of the static call. MAGIC!
	 *
	 * @return  Container
	 *
	 * @since 3.3.0
	 */
	private static function getContainerFromBacktrace()
	{
		// In case we are on a braindead host
		if (!function_exists('debug_backtrace'))
		{
			throw new \RuntimeException("Your host has disabled the <code>debug_backtrace</code> PHP function. Please ask them to re-enable it. It's required for running this software.");
		}

		/**
		 * For performance reasons I look into the last 4 call stack entries. If I don't find a container I
		 * will expand my search by another 2 entries and so on until I either find a container or I stop
		 * finding new call stack entries.
		 */
		$lastNumberOfEntries = 0;
		$limit               = 4;
		$skip                = 0;
		$container           = null;

		while (true)
		{
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit);

			if (count($backtrace) == $lastNumberOfEntries)
			{
				throw new \RuntimeException(__METHOD__ . ": Cannot retrieve FOF container from call stack. You are either calling me from a non-FEF extension or your PHP is broken.");
			}

			$lastNumberOfEntries = count($backtrace);

			if ($skip)
			{
				$backtrace = array_slice($backtrace, $skip);
			}

			foreach ($backtrace as $bt)
			{
				if (!isset($bt['object']))
				{
					continue;
				}

				if (!method_exists($bt['object'], 'getContainer'))
				{
					continue;
				}

				return $bt['object']->getContainer();
			}

			$skip  = $limit;
			$limit += 2;
		}

		throw new \RuntimeException(__METHOD__ . ": Cannot retrieve FOF container from call stack. You are either calling me from a non-FEF extension or your PHP is broken.");
	}

	/**
	 * Get the FOF View from the backtrace of the static call. MAGIC!
	 *
	 * @return  View
	 *
	 * @since 3.3.0
	 */
	private static function getViewFromBacktrace()
	{
		// In case we are on a braindead host
		if (!function_exists('debug_backtrace'))
		{
			throw new \RuntimeException("Your host has disabled the <code>debug_backtrace</code> PHP function. Please ask them to re-enable it. It's required for running this software.");
		}

		/**
		 * For performance reasons I look into the last 4 call stack entries. If I don't find a container I
		 * will expand my search by another 2 entries and so on until I either find a container or I stop
		 * finding new call stack entries.
		 */
		$lastNumberOfEntries = 0;
		$limit               = 4;
		$skip                = 0;
		$container           = null;

		while (true)
		{
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit);

			if (count($backtrace) == $lastNumberOfEntries)
			{
				throw new \RuntimeException(__METHOD__ . ": Cannot retrieve FOF View from call stack. You are either calling me from a non-FEF extension or your PHP is broken.");
			}

			$lastNumberOfEntries = count($backtrace);

			if ($skip)
			{
				$backtrace = array_slice($backtrace, $skip);
			}

			foreach ($backtrace as $bt)
			{
				if (!isset($bt['object']))
				{
					continue;
				}

				if ($bt['object'] instanceof View)
				{
					return $bt['object'];
				}
			}

			$skip  = $limit;
			$limit += 2;
		}

		throw new \RuntimeException(__METHOD__ . ": Cannot retrieve FOF View from call stack. You are either calling me from a non-FEF extension or your PHP is broken.");
	}

}
