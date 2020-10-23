<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Tests\DataModel;

use FOF30\Form\Form;
use FOF30\Tests\Helpers\ReflectionHelper;
use FOF30\Tests\Helpers\TestContainer;
use FOF30\Tests\Stubs\Model\DataModelStub;
use FOF30\Tests\Helpers\ClosureHelper;
use FOF30\Tests\Helpers\DatabaseTest;

require_once 'GenericDataprovider.php';

/**
 * @covers      FOF30\Model\DataModel::<protected>
 * @covers      FOF30\Model\DataModel::<private>
 * @package     FOF30\Tests\DataModel
 */
class DataModelGenericTest extends DatabaseTest
{
	/**
	 * @group           DataModel
	 * @group           DataModelGetTableFields
	 * @covers          FOF30\Model\DataModel::getTableFields
	 * @dataProvider    DataModelGenericDataprovider::getTestGetTableFields
	 */
	public function testGetTableFields($test, $check)
	{
		$msg = 'DataModel::getTableFields %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(self::$container, $config);

		// Mocking the whole database it's simply too hard. We will play with the cache and we won't get 100% code coverage
		if ($test['mock']['tables'] !== null)
		{
			$tables = ReflectionHelper::getValue($model, 'tableFieldCache');

			if ($test['mock']['tables'] == 'nuke')
			{
				$tables = [];
			}
			else
			{
				foreach ($test['mock']['tables'] as $mockedTable => $value)
				{
					if ($value == 'unset')
					{
						unset($tables[$mockedTable]);
					}
					else
					{
						$tables[$mockedTable] = $value;
					}
				}
			}

			ReflectionHelper::setValue($model, 'tableFieldCache', $tables);
		}

		if ($test['mock']['tableName'] !== null)
		{
			ReflectionHelper::setValue($model, 'tableName', $test['mock']['tableName']);
		}

		$result   = $model->getTableFields($test['table']);
		$db       = $model->getDbo();
		$expected = $this->_normalizeTableFields($check['result'], $db);
		$actual   = $this->_normalizeTableFields($result, $db);

		$this->assertEquals($expected, $actual, sprintf($msg, 'Returned the wrong result'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetDbo
	 * @covers          FOF30\Model\DataModel::getDbo
	 * @dataProvider    DataModelGenericDataprovider::getTestGetDbo
	 */
	public function testGetDbo($test, $check)
	{
		// Please note that if you try to debug this test, you'll get a "Couldn't fetch mysqli_result" error
		// That's harmless and appears in debug only, you might want to suppress exception throwing
		//\PHPUnit_Framework_Error_Warning::$enabled = false;

		$msg       = 'DataModel::setFieldValue %s - Case: ' . $check['case'];
		$dbcounter = 0;
		$selfDb    = \JFactory::getDbo();

		$config = [
			'idFieldName' => 'foftest_foobar_id',
			'tableName'   => '#__foftest_foobars',
		];

		$model = new DataModelStub(self::$container, $config);

		$newContainer = new TestContainer([
			'mediaVersion' => '123',
			'db'           => function () use (&$dbcounter, $selfDb) {
				$dbcounter++;

				return $selfDb;
			},
		]);

		ReflectionHelper::setValue($model, 'container', $newContainer);

		if ($test['nuke'])
		{
			ReflectionHelper::setValue($model, 'dbo', null);
		}

		$db = $model->getDbo();

		$this->assertInstanceOf('JDatabaseDriver', $db, sprintf($msg, 'Should return an instance of Driver'));
		$this->assertEquals($check['dbCounter'], $dbcounter, sprintf($msg, ''));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelSetFieldValue
	 * @covers          FOF30\Model\DataModel::setFieldValue
	 * @dataProvider    DataModelGenericDataprovider::getTestSetFieldValue
	 */
	public function testSetFieldValue($test, $check)
	{
		$msg = 'DataModel::setFieldValue %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_foobar_id',
			'tableName'   => '#__foftest_foobars',
		];

		$model = new DataModelStub(self::$container, $config);

		ReflectionHelper::setValue($model, 'aliasFields', $test['mock']['alias']);

		$model->setFieldValue($test['name'], $test['value']);

		$data = ReflectionHelper::getValue($model, 'recordData');

		$this->assertArrayHasKey($check['key'], $data, sprintf($msg, ''));
		$this->assertEquals($check['value'], $data[$check['key']], sprintf($msg, ''));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelReset
	 * @covers          FOF30\Model\DataModel::reset
	 * @dataProvider    DataModelGenericDataprovider::getTestReset
	 */
	public function testReset($test, $check)
	{
		$msg = 'DataModel::reset %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => $test['table_id'],
			'tableName'   => $test['table'],
		];

		$model = new DataModelStub(self::$container, $config);

		$relation = $this->getMockBuilder('FOF30\\Model\\DataModel\\RelationManager')
			->setMethods(['resetRelations'])
			->setConstructorArgs([$model])
			->getMock();

		//$relation->expects($check['resetRelations'] ? $this->once() : $this->never())->method('resetRelations');

		ReflectionHelper::setValue($model, 'relationManager', $relation);
		ReflectionHelper::setValue($model, 'recordData', $test['mock']['recordData']);
		ReflectionHelper::setValue($model, 'eagerRelations', $test['mock']['eagerRelations']);
		ReflectionHelper::setValue($model, 'relationFilters', $test['mock']['relationFilters']);

		$return = $model->reset($test['default'], $test['relations']);

		$data    = ReflectionHelper::getValue($model, 'recordData');
		$eager   = ReflectionHelper::getValue($model, 'eagerRelations');
		$filters = ReflectionHelper::getValue($model, 'relationFilters');

		$this->assertInstanceOf('\\FOF30\\Model\\DataModel', $return, sprintf($msg, 'Should return an instance of itself'));
		$this->assertEquals($check['data'], $data, sprintf($msg, 'Failed to reset the internal data'));
		$this->assertEquals($check['eager'], $eager, sprintf($msg, 'Eager relations are not correctly set'));
		$this->assertEmpty($filters, sprintf($msg, 'Relations filters should be empty'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetFieldValue
	 * @covers          FOF30\Model\DataModel::getFieldValue
	 * @dataProvider    DataModelGenericDataprovider::getTestGetFieldValue
	 */
	public function testGetFieldValue($test, $check)
	{
		$msg = 'DataModel::getFieldValue %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_foobar_id',
			'tableName'   => '#__foftest_foobars',
		];

		$model = new DataModelStub(self::$container, $config);

		ReflectionHelper::setValue($model, 'aliasFields', $test['mock']['alias']);

		if ($test['find'])
		{
			$model->find($test['find']);
		}

		$result = $model->getFieldValue($test['property'], $test['default']);

		$this->assertEquals($check['result'], $result, sprintf($msg, 'Returned the wrong value'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelHasField
	 * @covers          FOF30\Model\DataModel::hasField
	 * @dataProvider    DataModelGenericDataprovider::getTestHasField
	 */
	public function testHasField($test, $check)
	{
		$msg = 'DataModel::hasField %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_foobar_id',
			'tableName'   => '#__foftest_foobars',
		];

		$model = $this->getMockBuilder('\\FOF30\\Tests\\Stubs\\Model\\DataModelStub')
			->setMethods(['getFieldAlias'])
			->setConstructorArgs([self::$container, $config])
			->getMock();

		$model->method('getFieldAlias')->willReturn($test['mock']['getAlias']);

		ReflectionHelper::setValue($model, 'knownFields', $test['mock']['fields']);

		$result = $model->hasField($test['field']);

		$this->assertEquals($check['result'], $result, sprintf($msg, 'Returned the wrong value'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetFieldAlias
	 * @covers          FOF30\Model\DataModel::getFieldAlias
	 * @dataProvider    DataModelGenericDataprovider::getTestGetFieldAlias
	 */
	public function testGetFieldAlias($test, $check)
	{
		$msg = 'DataModel::getFieldAlias %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_foobar_id',
			'tableName'   => '#__foftest_foobars',
		];

		$model = new DataModelStub(self::$container, $config);

		ReflectionHelper::setValue($model, 'aliasFields', $test['mock']['alias']);

		$result = $model->getFieldAlias($test['field']);

		$this->assertEquals($check['result'], $result, sprintf($msg, 'Returned the wrong result'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetData
	 * @covers          FOF30\Model\DataModel::getData
	 */
	public function testGetData()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(self::$container, $config);
		$model->find(1);

		$result = $model->getData();

		$check = ['foftest_bare_id' => 1, 'title' => 'First Row'];

		$this->assertEquals($check, $result, 'DataModel::getData Returned the wrong result');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelChunk
	 * @covers          FOF30\Model\DataModel::chunk
	 * @dataProvider    DataModelGenericDataprovider::getTestChunk
	 */
	public function testChunk($test, $check)
	{
		$msg = 'DataModel::chunk %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$fakeGet = new ClosureHelper([
			'transform' => function () {
			},
		]);

		$model = $this->getMockBuilder('\\FOF30\\Tests\\Stubs\\Model\\DataModelStub')
			->setMethods(['count', 'get'])
			->setConstructorArgs([self::$container, $config])
			->getMock();

		$model->expects($this->once())->method('count')->willReturn($test['mock']['count']);
		$model->expects($this->exactly($check['get']))->method('get')->willReturn($fakeGet);

		$result = $model->chunk($test['chunksize'], function () {
		});

		$this->assertInstanceOf('\\FOF30\\Model\\DataModel', $result, sprintf($msg, 'Should return an instance of itself'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelCount
	 * @covers          FOF30\Model\DataModel::count
	 */
	public function testCount()
	{
		$db    = \JFactory::getDbo();
		$after = 0;

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		// I am passing those methods so I can double check if the method is really called
		$methods = [
			'buildCountQuery' => function () use (&$after) {
				$after++;
			},
		];

		$mockedQuery = $db->getQuery(true)->select('*')->from('#__foftest_bares');

		$model = $this->getMockBuilder('\\FOF30\\Tests\\Stubs\\Model\\DataModelStub')
			->setMethods(['buildQuery'])
			->setConstructorArgs([static::$container, $config, $methods])
			->getMock();

		$model->method('buildQuery')->willReturn($mockedQuery);

		// Let's mock the dispatcher, too. So I can check if events are really triggered
		$dispatcher = $this->getMockBuilder('\\FOF30\\Event\\Dispatcher')
			->setMethods(['trigger'])
			->setConstructorArgs([static::$container])
			->getMock();

		$dispatcher->expects($this->once())->method('trigger')->withConsecutive(
			[$this->equalTo('onBuildCountQuery')]
		);

		ReflectionHelper::setValue($model, 'behavioursDispatcher', $dispatcher);

		$result = $model->count();

		$query = $db->getQuery(true)->select('COUNT(*)')->from('#__foftest_bares');
		$count = $db->setQuery($query)->loadResult();

		$this->assertEquals($count, $result, 'DataModel::count Failed to return the right amount of rows');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelBuildQuery
	 * @covers          FOF30\Model\DataModel::buildQuery
	 * @dataProvider    DataModelGenericDataprovider::getTestBuildQuery
	 */
	public function testBuildQuery($test, $check)
	{
		// Please note that if you try to debug this test, you'll get a "Couldn't fetch mysqli_result" error
		// That's harmless and appears in debug only, you might want to suppress exception throwing
		//\PHPUnit_Framework_Error_Warning::$enabled = false;

		$before = 0;
		$after  = 0;
		$msg    = 'DataModel::buildQuery %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		// I am passing those methods so I can double check if the method is really called
		$methods = [
			'onBeforeBuildQuery' => function () use (&$before) {
				$before++;
			},
			'onAfterBuildQuery'  => function () use (&$after) {
				$after++;
			},
		];

		$model = $this->getMockBuilder('\\FOF30\\Tests\\Stubs\\Model\\DataModelStub')
			->setMethods(['getState'])
			->setConstructorArgs([static::$container, $config, $methods])
			->getMock();

		$model->expects($check['filter'] ? $this->exactly(2) : $this->never())->method('getState')->willReturnCallback(
			function ($state, $default) use ($test) {
				if ($state == 'filter_order')
				{
					if (isset($test['mock']['order']))
					{
						return $test['mock']['order'];
					}
				}
				elseif ($state == 'filter_order_Dir')
				{
					if (isset($test['mock']['dir']))
					{
						return $test['mock']['dir'];
					}
				}

				return $default;
			}
		);

		// Let's mock the dispatcher, too. So I can check if events are really triggered
		$dispatcher = $this->getMockBuilder('\\FOF30\\Event\\Dispatcher')
			->setMethods(['trigger'])
			->setConstructorArgs([static::$container])
			->getMock();

		$dispatcher->expects($this->exactly(2))->method('trigger')->withConsecutive(
			[$this->equalTo('onBeforeBuildQuery')],
			[$this->equalTo('onAfterBuildQuery')]
		);

		ReflectionHelper::setValue($model, 'behavioursDispatcher', $dispatcher);
		ReflectionHelper::setValue($model, 'whereClauses', $test['mock']['where']);

		$query = $model->buildQuery($test['override']);

		$select = $query->select->getElements();
		$table  = $query->from->getElements();
		$where  = $query->where ? $query->where->getElements() : [];
		$order  = $query->order ? $query->order->getElements() : [];

		$this->assertInstanceOf('\\JDatabaseQuery', $query, sprintf($msg, 'Should return an instance of JDatabaseQuery'));

		$this->assertEquals(['*'], $select, sprintf($msg, 'Wrong SELECT clause'));
		$this->assertEquals(['#__foftest_bares'], $table, sprintf($msg, 'Wrong FROM clause'));
		$this->assertEquals($check['where'], $where, sprintf($msg, 'Wrong WHERE clause'));
		$this->assertEquals($check['order'], $order, sprintf($msg, 'Wrong ORDER BY clause'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGet
	 * @covers          FOF30\Model\DataModel::get
	 * @dataProvider    DataModelGenericDataprovider::getTestGet
	 */
	public function testGet($test, $check)
	{
		$msg = 'DataModel::get %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = $this->getMockBuilder('\\FOF30\\Tests\\Stubs\\Model\\DataModelStub')
			->setMethods(['getState', 'getItemsArray', 'eagerLoad'])
			->setConstructorArgs([self::$container, $config])
			->getMock();

		$model->method('getState')->willReturnCallback(
			function ($state, $default) use ($test) {
				if ($state == 'limitstart')
				{
					return $test['mock']['limitstart'];
				}
				elseif ($state == 'limit')
				{
					return $test['mock']['limit'];
				}

				return $default;
			}
		);

		$model->expects($this->once())->method('getItemsArray')
			->with($this->equalTo($check['limitstart']), $this->equalTo($check['limit']))
			->willReturn([]);

		$result = $model->get($test['override'], $test['limitstart'], $test['limit']);

		$this->assertInstanceOf('\\FOF30\\Model\\DataModel\\Collection', $result, sprintf($msg, 'Returned the wrong object'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetId
	 * @covers          FOF30\Model\DataModel::getId
	 */
	public function testGetId()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(self::$container, $config);
		$model->find(2);

		$id = $model->getId();

		$this->assertEquals(2, $id, 'DataModel::getId Failed to return the correct id');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetIdFieldName
	 * @covers          FOF30\Model\DataModel::getIdFieldName
	 */
	public function testGetIdFieldName()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(self::$container, $config);
		$id    = $model->getIdFieldName();

		$this->assertEquals('foftest_bare_id', $id, 'DataModel::getIdFieldName Failed to return the table column id');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetTableName
	 * @covers          FOF30\Model\DataModel::getTableName
	 */
	public function testGetTableName()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(self::$container, $config);
		$table = $model->getTableName();

		$this->assertEquals('#__foftest_bares', $table, 'DataModel::getTableName Failed to return the table name');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelAddBehaviour
	 * @covers          FOF30\Model\DataModel::addBehaviour
	 * @dataProvider    DataModelGenericDataprovider::getTestAddBehaviour
	 */
	public function testAddBehaviour($test, $check)
	{
		$msg = 'DataModel::addBehaviour %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		$result = $model->addBehaviour($test['class']);

		$dispatcher = $model->getBehavioursDispatcher();
		$attached   = $dispatcher->hasObserverClass($check['class']);

		$this->assertInstanceOf('\\FOF30\\Model\\DataModel', $result, sprintf($msg, 'Should return and instance of itself'));
		$this->assertEquals($check['attached'], $attached, sprintf($msg, 'Failed to properly attach the behaviour'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetBehavioursDispatcher
	 * @covers          FOF30\Model\DataModel::getBehavioursDispatcher
	 */
	public function testGetBehavioursDispatcher()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		$reflDisp = ReflectionHelper::getValue($model, 'behavioursDispatcher');
		$disp     = $model->getBehavioursDispatcher();

		$this->assertSame($reflDisp, $disp, 'DataModel::getBehavioursDispatcher did not return the same object');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelOrderBy
	 * @covers          FOF30\Model\DataModel::orderBy
	 * @dataProvider    DataModelGenericDataprovider::getTestOrderBy
	 */
	public function testOrderBy($test, $check)
	{
		$msg = 'DataModel::orderBy %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_fobar_id',
			'tableName'   => '#__foftest_foobars',
		];

		$model = $this->getMockBuilder('FOF30\Tests\Stubs\Model\DataModelStub')
			->setMethods(['setState'])
			->setConstructorArgs([static::$container, $config])
			->getMock();
		$model->expects($this->exactly(2))->method('setState')->withConsecutive(
			[$this->equalTo('filter_order'), $this->equalTo($check['field'])],
			[$this->equalTo('filter_order_Dir'), $this->equalTo($check['dir'])]
		);

		$result = $model->orderBy($test['field'], $test['dir']);

		$this->assertInstanceOf('\\FOF30\\Model\\DataModel', $result, sprintf($msg, 'Should return an instance of itself'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelSkip
	 * @covers          FOF30\Model\DataModel::skip
	 * @dataProvider    DataModelGenericDataprovider::getTestSkip
	 */
	public function testSkip($test, $check)
	{
		$msg = 'DataModel::skip %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = $this->getMockBuilder('FOF30\Tests\Stubs\Model\DataModelStub')
			->setMethods(['setState'])
			->setConstructorArgs([static::$container, $config])
			->getMock();
		$model->expects($this->once())->method('setState')->with($this->equalTo('limitstart'), $this->equalTo($check['limitstart']));

		$result = $model->skip($test['limitstart']);

		$this->assertInstanceOf('\\FOF30\\Model\\DataModel', $result, sprintf($msg, 'Should return an instance of itself'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelTake
	 * @covers          FOF30\Model\DataModel::take
	 * @dataProvider    DataModelGenericDataprovider::getTestTake
	 */
	public function testTake($test, $check)
	{
		$msg = 'DataModel::take %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = $this->getMockBuilder('FOF30\Tests\Stubs\Model\DataModelStub')
			->setMethods(['setState'])
			->setConstructorArgs([static::$container, $config])
			->getMock();
		$model->expects($this->once())->method('setState')->with($this->equalTo('limit'), $this->equalTo($check['limit']));

		$result = $model->take($test['limit']);

		$this->assertInstanceOf('\\FOF30\\Model\\DataModel', $result, sprintf($msg, 'Should return an instance of itself'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelToArray
	 * @covers          FOF30\Model\DataModel::toArray
	 */
	public function testToarray()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);
		$model->find(1);

		$result = $model->toArray();

		$check = [
			'foftest_bare_id' => 1,
			'title'           => 'First Row',
		];

		$this->assertEquals($check, $result, 'DataModel::toArray Failed to return the array format');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelToJson
	 * @covers          FOF30\Model\DataModel::toJson
	 * @dataProvider    DataModelGenericDataprovider::getTestToJson
	 */
	public function testToJson($test)
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);
		$model->find(1);

		$result = $model->toJSon($test['pretty']);

		$check = [
			'foftest_bare_id' => '1',
			'title'           => 'First Row',
		];

		if (defined('JSON_PRETTY_PRINT'))
		{
			$options = $test['pretty'] ? JSON_PRETTY_PRINT : 0;
		}
		else
		{
			$options = 0;
		}

		$check = json_encode($check, $options);

		$this->assertEquals($check, $result, 'DataModel::toJson Failed to return the correct result');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelWhere
	 * @covers          FOF30\Model\DataModel::where
	 * @dataProvider    DataModelGenericDataprovider::getTestWhere
	 */
	public function testWhere($test, $check)
	{
		$msg = 'DataModel::where %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = $this->getMockBuilder('\\FOF30\\Tests\\Stubs\\Model\\DataModelStub')
			->setMethods(['getIdFieldName', 'setState', 'addBehaviour'])
			->setConstructorArgs([static::$container, $config])
			->getMock();

		$model->expects($check['add'] ? $this->once() : $this->never())->method('addBehaviour')->willReturn(null);
		$model->method('getIdFieldName')->willReturn($test['mock']['id_field']);
		$model->expects($this->once())->method('setState')->with($this->equalTo($check['field']), $this->equalTo($check['options']));

		$dispatcher = $this->getMockBuilder('\\FOF30\\Event\\Dispatcher')
			->setMethods(['hasObserverClass'])
			->setConstructorArgs([static::$container, $config])
			->getMock();

		$dispatcher->method('hasObserverClass')->willReturn($test['mock']['hasClass']);

		ReflectionHelper::setValue($model, 'behavioursDispatcher', $dispatcher);

		$result = $model->where($test['field'], $test['method'], $test['values']);

		$this->assertInstanceOf('\\FOF30\\Model\\DataModel', $result, sprintf($msg, 'Should return an instance of itself'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelWhere
	 * @covers          FOF30\Model\DataModel::where
	 */
	public function testWhereException()
	{
		$this->setExpectedException('FOF30\Model\DataModel\Exception\InvalidSearchMethod');

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);
		$model->where('id', 'wrong', null);
	}

	/**
	 * @group           DataModel
	 * @group           DataModelWhereRaw
	 * @covers          FOF30\Model\DataModel::whereRaw
	 */
	public function testWhereRaw()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		$result = $model->whereRaw('foo = bar');
		$where  = ReflectionHelper::getValue($model, 'whereClauses');

		$this->assertEquals(['foo = bar'], $where, 'DataModel::whereRaw failed to save custom where clause');
		$this->assertInstanceOf('\\FOF30\\Model\\DataModel', $result, 'DataModel::whereRaw should return an instance of itself');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelWith
	 * @covers          FOF30\Model\DataModel::with
	 * @dataProvider    DataModelGenericDataprovider::getTestWith
	 */
	public function testWith($test, $check)
	{
		$msg = 'DataModel::has %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$fakeRelationManager = new ClosureHelper([
			'getRelationNames' => function () use ($test) {
				return $test['mock']['relNames'];
			},
		]);

		$model = new DataModelStub(static::$container, $config);

		ReflectionHelper::setValue($model, 'relationManager', $fakeRelationManager);

		$result = $model->with($test['relations']);

		$eager = ReflectionHelper::getValue($model, 'eagerRelations');

		$this->assertInstanceOf('\\FOF30\\Model\\DataModel', $result, sprintf($msg, 'Should return an instance of itself'));
		$this->assertEquals($check['eager'], $eager, sprintf($msg, 'Failed to set the eagerLoad relationships'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelApplyAccessFiltering
	 * @covers          FOF30\Model\DataModel::applyAccessFiltering
	 * @dataProvider    DataModelGenericDataprovider::getTestapplyAccessFiltering
	 */
	public function testapplyAccessFiltering($test, $check)
	{
		$msg = 'DataModel::applyAccessFiltering %s - Case: ' . $check['case'];

		$platform        = static::$container->platform;
		$platform::$user = new ClosureHelper([
			'getAuthorisedViewLevels' => function () {
				return [5, 10];
			},
		]);

		$config = [
			'idFieldName' => $test['tableid'],
			'tableName'   => $test['table'],
		];

		$model = $this->getMockBuilder('FOF30\Tests\Stubs\Model\DataModelStub')
			->setMethods(['setState'])
			->setConstructorArgs([static::$container, $config])
			->getMock();
		$model->expects($check['state'] ? $this->once() : $this->never())->method('setState');

		$result = $model->applyAccessFiltering();

		$this->assertInstanceOf('FOF30\Model\DataModel', $result, sprintf($msg, 'Should return an instance of iteself'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetContentType
	 * @covers          FOF30\Model\DataModel::getContentType
	 * @dataProvider    DataModelGenericDataprovider::getTestGetContentType
	 */
	public function testGetContentType($test, $check)
	{
		$msg = 'DataModel::getContentType %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		ReflectionHelper::setValue($model, 'contentType', $test['contentType']);

		if ($check['exception'])
		{
			$this->setExpectedException('FOF30\Model\DataModel\Exception\NoContentType');
		}

		$result = $model->getContentType();

		$this->assertEquals($check['result'], $result, sprintf($msg, 'Returned the wrong value'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetItemsArray
	 * @covers          FOF30\Model\DataModel::getItemsArray
	 */
	public function testGetItemsArray()
	{
		$msg     = 'DataModel::getItemsArray %s ';
		$counter = 0;

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$query = \JFactory::getDbo()->getQuery(true)
			->select('*')
			->from('#__foftest_bares')
			->where('foftest_bare_id = 1');

		$methods = [
			'onAfterGetItemsArray' => function () use (&$counter) {
				$counter++;
			},
		];

		$model = $this->getMockBuilder('\\FOF30\\Tests\\Stubs\\Model\\DataModelStub')
			->setMethods(['buildQuery'])
			->setConstructorArgs([static::$container, $config, $methods])
			->getMock();

		$model->method('buildQuery')->willReturn($query);

		$result = $model->getItemsArray(0, 0, false);

		$this->assertInternalType('array', $result, sprintf($msg, 'Should return an array'));

		$array_keys = array_keys($result);
		$key        = array_shift($array_keys);
		$item       = array_shift($result);

		$this->assertSame(1, $key, sprintf($msg, 'Should index the array by the record id'));
		$this->assertInstanceOf('FOF30\Model\DataModel', $item, sprintf($msg, 'Should return an array of DataModels'));
		$this->assertSame('1', $item->foftest_bare_id, sprintf($msg, 'Should bind the data to the object'));
		$this->assertEquals(1, $counter, sprintf($msg, 'Failed to invoke the onAfter event'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelAddKnownField
	 * @covers          FOF30\Model\DataModel::addKnownField
	 * @dataProvider    DataModelGenericDataprovider::getTestAddKnownField
	 */
	public function testAddKnownField($test, $check)
	{
		$msg = 'DataModel::addKnownField %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		$result = $model->addKnownField($test['name'], 'foobar', 'varchar(100)', $test['replace']);

		$known = ReflectionHelper::getValue($model, 'knownFields');
		$data  = ReflectionHelper::getValue($model, 'recordData');

		$this->assertInstanceOf('\FOF30\Model\DataModel', $result, sprintf($msg, 'Returned the wrong result'));
		$this->assertArrayHasKey($check['field'], $known, sprintf($msg, 'Failed to set the field into the internal array'));
		$this->assertEquals($known[$check['field']], $check['info'], sprintf($msg, 'Failed to set the field info'));
		$this->assertSame($data[$check['field']], $check['value'], sprintf($msg, 'Failed to set field default value'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetRules
	 * @covers          FOF30\Model\DataModel::getRules
	 */
	public function testGetRules()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		ReflectionHelper::setValue($model, '_rules', 'test');

		$this->assertEquals('test', $model->getRules(), 'DataModel::getRules Returned the wrong result');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelIsAssetsTracked
	 * @covers          FOF30\Model\DataModel::isAssetsTracked
	 */
	public function testIsAssetsTracked()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		ReflectionHelper::setValue($model, '_trackAssets', true);

		$this->assertEquals(true, $model->isAssetsTracked(), 'DataModel::isAssetTracked Returned the wrong result');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelSetAssetsTracked
	 * @covers          FOF30\Model\DataModel::setAssetsTracked
	 */
	public function testSetAssetsTracked()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);
		$model->setAssetsTracked(true);

		$value = ReflectionHelper::getValue($model, '_trackAssets');

		$this->assertEquals(true, $value, 'DataModel::setAssetsTracked Returned the wrong result');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelHasTags
	 * @covers          FOF30\Model\DataModel::hasTags
	 */
	public function testHasTags()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		ReflectionHelper::setValue($model, '_has_tags', true);

		$this->assertEquals(true, $model->hasTags(), 'DataModel::hasTags Returned the wrong result');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelSetHasTags
	 * @covers          FOF30\Model\DataModel::setHasTags
	 */
	public function testSetHasTags()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);
		$model->setHasTags(true);

		$value = ReflectionHelper::getValue($model, '_has_tags');

		$this->assertEquals(true, $value, 'DataModel::setHasTags Returned the wrong result');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelSetAssetKey
	 * @covers          FOF30\Model\DataModel::setAssetKey
	 */
	public function testSetAssetKey()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);
		$model->setAssetKey('com_fakeapp.foobars');

		$value = ReflectionHelper::getValue($model, '_assetKey');

		$this->assertEquals('com_fakeapp.foobars', $value, 'DataModel::setAssetKey Returned the wrong result');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetAssetName
	 * @covers          FOF30\Model\DataModel::getAssetName
	 * @dataProvider    DataModelGenericDataprovider::getTestGetAssetName
	 */
	public function testGetAssetName($test, $check)
	{
		$msg = 'DataModel::getAssetName %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		if ($test['load'])
		{
			$model->find($test['load']);
		}

		ReflectionHelper::setValue($model, '_assetKey', $test['assetkey']);

		if ($check['exception'])
		{
			$this->setExpectedException('FOF30\Model\DataModel\Exception\NoAssetKey');
		}

		$result = $model->getAssetName();

		$this->assertEquals($check['result'], $result, sprintf($msg, 'Returned the wrong result'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetAssetKey
	 * @covers          FOF30\Model\DataModel::getAssetKey
	 */
	public function testGetAssetKey()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		ReflectionHelper::setValue($model, '_assetKey', 'com_fakeapp.foobar');

		$this->assertEquals('com_fakeapp.foobar', $model->getAssetKey(), 'DataModel::getAssetKey Returned the wrong result');
	}

	/**
	 * @group           DataModel
	 * @group           DataModelSetBehaviorParam
	 * @covers          FOF30\Model\DataModel::setBehaviorParam
	 */
	public function testSetBehaviorParam()
	{
		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		$result = $model->setBehaviorParam('foo', 'bar');

		$behaviors = ReflectionHelper::getValue($model, '_behaviorParams');

		$this->assertArrayHasKey('foo', $behaviors);
		$this->assertEquals('bar', $behaviors['foo']);
		$this->assertInstanceOf('FOF30\Model\DataModel', $result);
	}

	/**
	 * @group           DataModel
	 * @group           DataModelGetBehaviorParam
	 * @covers          FOF30\Model\DataModel::getBehaviorParam
	 * @dataProvider    DataModelGenericDataprovider::getTestGetBehaviorParam
	 */
	public function testGetBehaviorParam($test, $check)
	{
		$msg = 'DataModel::getBehaviorParam %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		ReflectionHelper::setValue($model, '_behaviorParams', $test['mock']['behaviors']);

		$result = $model->getBehaviorParam($test['name'], $test['default']);

		$this->assertEquals($check['result'], $result, sprintf($msg, 'Returned the wrong result'));
	}

	/**
	 * @group           DataModel
	 * @group           DataModelBlacklistFilters
	 * @covers          FOF30\Model\DataModel::blacklistFilters
	 * @dataProvider    DataModelGenericDataprovider::getTestBlacklistFilters
	 */
	public function testBlacklistFilters($test, $check)
	{
		$msg = 'DataModel::blacklistFilters %s - Case: ' . $check['case'];

		$config = [
			'idFieldName' => 'foftest_bare_id',
			'tableName'   => '#__foftest_bares',
		];

		$model = new DataModelStub(static::$container, $config);

		ReflectionHelper::setValue($model, '_behaviorParams', ['blacklistFilters' => ['test']]);

		$result = $model->blacklistFilters($test['list'], $test['reset']);

		$behaviors = ReflectionHelper::getValue($model, '_behaviorParams');
		$filters   = isset($behaviors['blacklistFilters']) ? $behaviors['blacklistFilters'] : [];

		$this->assertSame($check['result'], $result, sprintf($msg, 'Returned the wrong result'));
		$this->assertEquals($check['filters'], $filters, sprintf($msg, 'Failed to set the filters'));
	}
}
