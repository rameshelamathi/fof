<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Tests\Helpers;

use FOF30\Container\Container;

abstract class DatabaseTest extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @var			array	The JFactory pointers saved before the execution of the test
     */
    protected $savedFactoryState = array();

	/**
	 * Assigns mock callbacks to methods.
	 *
	 * @param   object  $mockObject  The mock object that the callbacks are being assigned to.
	 * @param   array   $array       An array of methods names to mock with callbacks.
	 *
	 * @return  void
	 *
	 * @note    This method assumes that the mock callback is named {mock}{method name}.
	 * @since   1.0
	 */
	public function assignMockCallbacks($mockObject, $array)
	{
		foreach ($array as $index => $method)
		{
			if (is_array($method))
			{
				$methodName = $index;
				$callback = $method;
			}
			else
			{
				$methodName = $method;
				$callback = array(get_called_class(), 'mock' . $method);
			}

			$mockObject
				->method($methodName)
				->will($this->returnCallback($callback));
		}
	}

	/**
	 * Assigns mock values to methods.
	 *
	 * @param   object  $mockObject  The mock object.
	 * @param   array   $array       An associative array of methods to mock with return values:<br />
	 *                               string (method name) => mixed (return value)
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function assignMockReturns($mockObject, $array)
	{
		foreach ($array as $method => $return)
		{
			$mockObject
				->method($method)
				->will($this->returnValue($return));
		}
	}

	/**
	 * Returns the default database connection for running the tests.
	 *
	 * @return  \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
	 *
	 * @since   1.0
	 */
	protected function getConnection()
	{
        static $connection;

        if(!$connection)
        {
            $config = \JFactory::getConfig();

            // P.A. Test database prefix is fixed with jos_ so we can setup common tables
            $options = array (
                'driver'	=> ((isset ($config)) && ($config->get('dbtype') != 'mysqli')) ? $config->get('dbtype') : 'mysql',
                'host' 		=> $config->get('host', '127.0.0.1'),
                'user' 		=> $config->get('user', 'utuser'),
                'password' 	=> $config->get('password', 'ut1234'),
                'database' 	=> $config->get('db', 'joomla_ut'),
                'prefix' 	=> 'jos_'
            );

            $pdo = new \PDO('mysql:host='.$options['host'].';dbname='.$options['database'], $options['user'], $options['password']);
            $pdo->exec("SET @@SESSION.sql_mode = '';");
            $connection = $this->createDefaultDBConnection($pdo, $options['database']);
        }

        return $connection;
	}

    /**
     * Gets the data set to be loaded into the database during setup
     *
     * @return  \PHPUnit_Extensions_Database_DataSet_XmlDataSet
     *
     * @since   1.0
     */
    protected function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__ . '/../Stubs/schema/database.xml');
    }

	/**
	 * Returns the database operation executed in test setup.
	 *
	 * @return  \PHPUnit_Extensions_Database_Operation_Composite
	 *
	 * @since   1.0
	 */
	protected function getSetUpOperation()
	{
        // At the moment we can safely TRUNCATE tables, since we're not using InnoDB tables nor foreign keys
        // However if we ever need them, we can use our InsertOperation and TruncateOperation to suppress foreign keys
        return new \PHPUnit_Extensions_Database_Operation_Composite(
            array(
                \PHPUnit_Extensions_Database_Operation_Factory::TRUNCATE(),
                \PHPUnit_Extensions_Database_Operation_Factory::INSERT()
            )
        );
	}

    /** @var Container A container suitable for unit testing */
    public static $container = null;

    public static function setUpBeforeClass()
    {
        self::rebuildContainer();
    }

    public static function tearDownAfterClass()
    {
        static::$container = null;
    }

    public static function rebuildContainer()
    {
        static::$container = null;
        static::$container = new TestContainer(array(
            'componentName'	=> 'com_fakeapp',
        ));
    }

    public function setUp()
    {
        parent::setUp();

        // Since we're creating the platform only when we instantiate the test class, any modification
        // will be carried over in the other tests, so we have to manually reset the platform before
        // running any other test
        $platform = static::$container->platform;

        if(method_exists($platform, 'reset'))
        {
            $platform->reset();
        }
    }

    /**
     * Saves the Factory pointers
     *
     * @return void
     */
    protected function saveFactoryState()
    {
        // We have to clone the objects, otherwise it's useless to save them
        $this->savedFactoryState['application']	 = is_object(\JFactory::$application) ? clone \JFactory::$application : \JFactory::$application;
        $this->savedFactoryState['config']		 = is_object(\JFactory::$config) ? clone \JFactory::$config : \JFactory::$config;
        $this->savedFactoryState['dates']		 = \JFactory::$dates;
        $this->savedFactoryState['session']		 = is_object(\JFactory::$session) ? clone \JFactory::$session : \JFactory::$session;
        $this->savedFactoryState['language']	 = is_object(\JFactory::$language) ? clone \JFactory::$language : \JFactory::$language;
        $this->savedFactoryState['document']	 = is_object(\JFactory::$document) ? clone \JFactory::$document : \JFactory::$document;
        $this->savedFactoryState['acl']			 = is_object(\JFactory::$acl) ? clone \JFactory::$acl : \JFactory::$acl;
        $this->savedFactoryState['database']	 = is_object(\JFactory::$database) ? clone \JFactory::$database : \JFactory::$database;
        $this->savedFactoryState['mailer']		 = is_object(\JFactory::$mailer) ? clone \JFactory::$mailer : \JFactory::$mailer;
    }

    /**
     * Sets the Factory pointers
     *
     * @return  void
     */
    protected function restoreFactoryState()
    {
        \JFactory::$application	= $this->savedFactoryState['application'];
        \JFactory::$config		= $this->savedFactoryState['config'];
        \JFactory::$dates		= $this->savedFactoryState['dates'];
        \JFactory::$session		= $this->savedFactoryState['session'];
        \JFactory::$language	= $this->savedFactoryState['language'];
        \JFactory::$document	= $this->savedFactoryState['document'];
        \JFactory::$acl			= $this->savedFactoryState['acl'];
        \JFactory::$database	= $this->savedFactoryState['database'];
        \JFactory::$mailer		= $this->savedFactoryState['mailer'];
    }

	/**
	 * Normalizes two arrays containing lists of fields:
	 * * Converts utf8mb4 references to their utf8 equivalents
	 * * Converts null to empty strings (since the null/empty string result is db version dependent)
	 *
	 * @param                  $fields
	 * @param \JDatabaseDriver $db
	 *
	 * @return array
	 */
	protected function _normalizeTableFields($fields, \JDatabaseDriver $db)
	{
		if (!is_array($fields))
		{
			return $fields;
		}

		$ret = array();

		foreach ($fields as $fieldName => $def)
		{
			$def = (array)$def;

			$def = array_map(function ($value) {
				if (is_null($value)) return '';

				if (!is_numeric($value) && is_string($value))
				{
					$value = str_replace('utf8mb4_', 'utf8_', $value);
					$value = str_replace('_unicode_ci', '_general_ci', $value);
				}

				return $value;
			}, $def);

			$ret[$fieldName] = (object) $def;
		}

		return $ret;
	}
}
