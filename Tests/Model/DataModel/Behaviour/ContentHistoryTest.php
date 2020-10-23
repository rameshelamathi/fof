<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Tests\DataModel;

use FOF30\Model\DataModel\Behaviour\ContentHistory;
use FOF30\Tests\Helpers\ClosureHelper;
use FOF30\Tests\Helpers\DatabaseTest;
use FOF30\Tests\Helpers\ReflectionHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\ComponentRecord;

require_once 'ContentHistoryDataprovider.php';

/**
 * @covers      FOF30\Model\DataModel\Behaviour\ContentHistory::<protected>
 * @covers      FOF30\Model\DataModel\Behaviour\ContentHistory::<private>
 * @package     FOF30\Tests\DataModel\Behaviour\ContentHistory
 */
class ContentHistoryTest extends DatabaseTest
{
	/**
	 * @group           Behaviour
	 * @group           ContentHistoryOnAfterSave
	 * @covers          FOF30\Model\DataModel\Behaviour\ContentHistory::onAfterSave
	 * @dataProvider    ContentHistoryDataprovider::getTestOnAfterSave
	 */
	public function testOnAfterSave($test, $check)
	{
		$msg     = 'ContentHistory::onAfterSave %s - Case: ' . $check['case'];
		$counter = 0;

		$config = [
			'idFieldName' => 'foftest_foobar_id',
			'tableName'   => '#__foftest_foobars',
		];

		$model = $this->getMockBuilder('FOF30\Tests\Stubs\Model\DataModelStub')
			->setMethods(['getContentType', 'checkContentType'])
			->setConstructorArgs([static::$container, $config])
			->getMock();
		$model->method('getContentType')->willReturn('com_foftest.test');

		$dispatcher = $model->getBehavioursDispatcher();
		$behavior   = new ContentHistory($dispatcher);

		$fakeHelper = new ClosureHelper([
			'store' => function () use (&$counter) {
				$counter++;
			},
		]);

		$fakeComponent = [
			'com_foftest' => new ComponentRecord([
				'params' => new \JRegistry([
					'save_history' => $test['save_history'],
				]),
			]),
		];

		ReflectionHelper::setValue($behavior, 'historyHelper', $fakeHelper);
		ReflectionHelper::setValue('\\Joomla\\CMS\\Component\\ComponentHelper', 'components', $fakeComponent);

		$behavior->onAfterSave($model);

		$this->assertEquals($check['store'], $counter, sprintf($msg, 'Failed to correctly invoke the Content History helper'));
	}

	/**
	 * @group           Behaviour
	 * @group           ContentHistoryOnBeforeDelete
	 * @covers          FOF30\Model\DataModel\Behaviour\ContentHistory::onBeforeDelete
	 * @dataProvider    ContentHistoryDataprovider::getTestOnBeforeDelete
	 */
	public function testOnBeforeDelete($test, $check)
	{
		$msg     = 'ContentHistory::onBeforeDelete %s - Case: ' . $check['case'];
		$counter = 0;

		$config = [
			'idFieldName' => 'foftest_foobar_id',
			'tableName'   => '#__foftest_foobars',
		];

		$model = $this->getMockBuilder('FOF30\Tests\Stubs\Model\DataModelStub')
			->setMethods(['getContentType', 'checkContentType'])
			->setConstructorArgs([static::$container, $config])
			->getMock();
		$model->method('getContentType')->willReturn('com_foftest.test');

		$dispatcher = $model->getBehavioursDispatcher();
		$behavior   = new ContentHistory($dispatcher);

		$fakeHelper = new ClosureHelper([
			'deleteHistory' => function () use (&$counter) {
				$counter++;
			},
		]);

		$fakeComponent = [
			'com_foftest' => new ComponentRecord([
				'params' => new \JRegistry([
					'save_history' => $test['save_history'],
				]),
			]),
		];

		ReflectionHelper::setValue($behavior, 'historyHelper', $fakeHelper);
		ReflectionHelper::setValue('\\Joomla\\CMS\\Component\\ComponentHelper', 'components', $fakeComponent);

		$behavior->onBeforeDelete($model, 1);

		$this->assertEquals($check['delete'], $counter, sprintf($msg, 'Failed to correctly invoke the Content History helper'));
	}

	/**
	 * @group           Behaviour
	 * @group           ContentHistoryOnAfterPublish
	 * @covers          FOF30\Model\DataModel\Behaviour\ContentHistory::onAfterPublish
	 */
	public function testOnAfterPublish()
	{
		$config = [
			'idFieldName' => 'foftest_foobar_id',
			'tableName'   => '#__foftest_foobars',
		];

		$model = $this->getMockBuilder('FOF30\Tests\Stubs\Model\DataModelStub')
			->setMethods(['updateUcmContent'])
			->setConstructorArgs([static::$container, $config])
			->getMock();

		$model->expects($this->once())->method('updateUcmContent');

		$dispatcher = $model->getBehavioursDispatcher();
		$behavior   = new ContentHistory($dispatcher);

		$behavior->onAfterPublish($model);
	}

	/**
	 * @group           Behaviour
	 * @group           ContentHistoryOnAfterUnpublish
	 * @covers          FOF30\Model\DataModel\Behaviour\ContentHistory::onAfterUnpublish
	 */
	public function testOnAfterUnpublish()
	{
		$config = [
			'idFieldName' => 'foftest_foobar_id',
			'tableName'   => '#__foftest_foobars',
		];

		$model = $this->getMockBuilder('FOF30\Tests\Stubs\Model\DataModelStub')
			->setMethods(['updateUcmContent'])
			->setConstructorArgs([static::$container, $config])
			->getMock();
		$model->expects($this->once())->method('updateUcmContent');

		$dispatcher = $model->getBehavioursDispatcher();
		$behavior   = new ContentHistory($dispatcher);

		$behavior->onAfterUnpublish($model);
	}

	protected function tearDown()
	{
		parent::tearDown();

		ReflectionHelper::setValue('\\Joomla\\CMS\\Component\\ComponentHelper', 'components', []);
	}
}
