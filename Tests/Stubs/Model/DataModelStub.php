<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Tests\Stubs\Model;

use FOF30\Container\Container;
use FOF30\Model\DataModel;
use FOF30\Tests\Helpers\ReflectionHelper;

class DataModelStub extends DataModel
{
	/**  @var null The container passed in the getInstance method */
	public static $passedContainerStatic = null;
	/**  @var null The container passed in the construct */
	public $passedContainer = null;
	/** @var array Simply counter to check if a specific function is called */
	public $methodCounter = [
		'scopeDummyProperty'   => 0,
		'scopeDummyNoProperty' => 0,
	];
	public $dynamicCall = [];
	public $dummyProperty = 'default';
	public $dummyPropertyNoFunction = 'default';
	protected $name = 'nestedset';
	private $methods = [];

	/**
	 * Assigns callback functions to the class, the $methods array should be an associative one, where
	 * the keys are the method names, while the values are the closure functions, e.g.
	 *
	 * array(
	 *    'foobar' => function(){ return 'Foobar'; }
	 * )
	 *
	 * @param           $container
	 * @param   array   $config
	 * @param   array   $methods
	 */
	public function __construct(Container $container, array $config = [], array $methods = [])
	{
		foreach ($methods as $method => $function)
		{
			$this->methods[$method] = $function;
		}

		/** @var \FOF30\Tests\Helpers\TestJoomlaPlatform $platform */
		$platform = $container->platform;

		// Provide a default mock function for getUserStateFromRequest, since we are going to query the model state
		// in the model and if it's not set, we will get an application error
		// PRO TIP: If you don't want to automatically mock such function, you can easily set the $getUserStateFromRequest
		// variable to a string, ie 'do not mock'
		// This check will fail, but in the parent we are checking if the variable is a callable. Since it's not (it's a string)
		// we will fallback to the parent, original method
		if ($platform instanceof \FOF30\Tests\Helpers\TestJoomlaPlatform && !$platform::$getUserStateFromRequest)
		{
			$platform::$getUserStateFromRequest = function ($key, $request, $input, $default, $type, $setUserState) {
				return $default;
			};
		}
		// Do the same if we have a Closure object
		elseif ($platform instanceof \FOF30\Tests\Helpers\ClosureHelper)
		{
			$methods = ReflectionHelper::getValue($platform, 'mockedMethods');

			if (!isset($methods['getUserStateFromRequest']))
			{
				$methods['getUserStateFromRequest'] = function ($key, $request, $input, $default, $type, $setUserState) {
					return $default;
				};

				ReflectionHelper::setValue($platform, 'mockedMethods', $methods);
			}
		}

		parent::__construct($container, $config);
	}

	public function __call($method, $args)
	{
		if (isset($this->methods[$method]))
		{
			$func = $this->methods[$method];

			// Let's pass an instance of ourselves, so we can manipulate other closures
			array_unshift($args, $this);

			return call_user_func_array($func, $args);
		}

		return parent::__call($method, $args);
	}

	/**
	 * A mocked object will have a random name, that won't match the regex expression in the parent.
	 * To prevent exceptions, we have to manually set the name
	 *
	 * @return string
	 */
	public function getName()
	{
		if (isset($this->methods['getName']))
		{
			$func = $this->methods['getName'];

			return call_user_func_array($func, []);
		}

		return $this->name;
	}

	/*
	 * The base object will perform a "method_exists" check, so we have to create them, otherwise they won't be invoked
	 */

	public function buildCountQuery()
	{
		if (isset($this->methods['buildCountQuery']))
		{
			$func = $this->methods['buildCountQuery'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeBuildQuery()
	{
		if (isset($this->methods['onBeforeBuildQuery']))
		{
			$func = $this->methods['onBeforeBuildQuery'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterBuildQuery()
	{
		if (isset($this->methods['onAfterBuildQuery']))
		{
			$func = $this->methods['onAfterBuildQuery'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeArchive()
	{
		if (isset($this->methods['onBeforeArchive']))
		{
			$func = $this->methods['onBeforeArchive'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterArchive()
	{
		if (isset($this->methods['onAfterArchive']))
		{
			$func = $this->methods['onAfterArchive'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeTrash()
	{
		if (isset($this->methods['onBeforeTrash']))
		{
			$func = $this->methods['onBeforeTrash'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterTrash()
	{
		if (isset($this->methods['onAfterTrash']))
		{
			$func = $this->methods['onAfterTrash'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeDelete()
	{
		if (isset($this->methods['onBeforeDelete']))
		{
			$func = $this->methods['onBeforeDelete'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterDelete()
	{
		if (isset($this->methods['onAfterDelete']))
		{
			$func = $this->methods['onAfterDelete'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeLock()
	{
		if (isset($this->methods['onBeforeLock']))
		{
			$func = $this->methods['onBeforeLock'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterLock()
	{
		if (isset($this->methods['onAfterLock']))
		{
			$func = $this->methods['onAfterLock'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforePublish()
	{
		if (isset($this->methods['onBeforePublish']))
		{
			$func = $this->methods['onBeforePublish'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterPublish()
	{
		if (isset($this->methods['onAfterPublish']))
		{
			$func = $this->methods['onAfterPublish'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeRestore()
	{
		if (isset($this->methods['onBeforeRestore']))
		{
			$func = $this->methods['onBeforeRestore'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterRestore()
	{
		if (isset($this->methods['onAfterRestore']))
		{
			$func = $this->methods['onAfterRestore'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeUnlock()
	{
		if (isset($this->methods['onBeforeUnlock']))
		{
			$func = $this->methods['onBeforeUnlock'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterUnlock()
	{
		if (isset($this->methods['onAfterUnlock']))
		{
			$func = $this->methods['onAfterUnlock'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeUnpublish()
	{
		if (isset($this->methods['onBeforeUnpublish']))
		{
			$func = $this->methods['onBeforeUnpublish'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterUnpublish()
	{
		if (isset($this->methods['onAfterUnpublish']))
		{
			$func = $this->methods['onAfterUnpublish'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeReorder()
	{
		if (isset($this->methods['onBeforeReorder']))
		{
			$func = $this->methods['onBeforeReorder'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterReorder()
	{
		if (isset($this->methods['onAfterReorder']))
		{
			$func = $this->methods['onAfterReorder'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeMove()
	{
		if (isset($this->methods['onBeforeMove']))
		{
			$func = $this->methods['onBeforeMove'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterMove()
	{
		if (isset($this->methods['onAfterMove']))
		{
			$func = $this->methods['onAfterMove'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeLoad()
	{
		if (isset($this->methods['onBeforeLoad']))
		{
			$func = $this->methods['onBeforeLoad'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterLoad()
	{
		if (isset($this->methods['onAfterLoad']))
		{
			$func = $this->methods['onAfterLoad'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeSave()
	{
		if (isset($this->methods['onBeforeSave']))
		{
			$func = $this->methods['onBeforeSave'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterSave()
	{
		if (isset($this->methods['onAfterSave']))
		{
			$func = $this->methods['onAfterSave'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeCreate()
	{
		if (isset($this->methods['onBeforeCreate']))
		{
			$func = $this->methods['onBeforeCreate'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterCreate()
	{
		if (isset($this->methods['onAfterCreate']))
		{
			$func = $this->methods['onAfterCreate'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeUpdate()
	{
		if (isset($this->methods['onBeforeUpdate']))
		{
			$func = $this->methods['onBeforeUpdate'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterUpdate()
	{
		if (isset($this->methods['onAfterUpdate']))
		{
			$func = $this->methods['onAfterUpdate'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterGetItemsArray()
	{
		if (isset($this->methods['onAfterGetItemsArray']))
		{
			$func = $this->methods['onAfterGetItemsArray'];

			return call_user_func_array($func, []);
		}
	}

	public function onBeforeLoadForm()
	{
		if (isset($this->methods['onBeforeLoadForm']))
		{
			$func = $this->methods['onBeforeLoadForm'];

			return call_user_func_array($func, []);
		}
	}

	public function onAfterLoadForm()
	{
		if (isset($this->methods['onAfterLoadForm']))
		{
			$func = $this->methods['onAfterLoadForm'];

			return call_user_func_array($func, []);
		}
	}

	public function dynamicCall()
	{
		$this->dynamicCall[] = func_get_args();
	}

	/**
	 * Method invoked by the __call magic method
	 *
	 * @see     DataModel::__call
	 */
	public function scopeDummyProperty()
	{
		$this->methodCounter['scopeDummyProperty']++;
	}

	/**
	 * Method invoked by the __set magic method
	 *
	 * @see     DataModel::__set
	 */
	public function scopeDummyNoProperty()
	{
		$this->methodCounter['scopeDummyNoProperty']++;
	}
}

