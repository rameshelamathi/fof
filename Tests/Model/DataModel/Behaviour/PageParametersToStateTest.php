<?php
/**
 * @package     FOF
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Tests\DataModel;

use FOF30\Input\Input;
use FOF30\Model\DataModel\Behaviour\PageParametersToState;
use FOF30\Tests\Helpers\ClosureHelper;
use FOF30\Tests\Helpers\DatabaseTest;
use FOF30\Tests\Helpers\TestContainer;
use FOF30\Tests\Stubs\Model\DataModelStub;

require_once 'PageParametersToStateDataprovider.php';

/**
 * @covers      FOF30\Model\DataModel\Behaviour\PageParametersToState::<protected>
 * @covers      FOF30\Model\DataModel\Behaviour\PageParametersToState::<private>
 * @package     FOF30\Tests\DataModel\Behaviour\PageParametersToState
 */
class PageParametersToStateTest extends DatabaseTest
{
    protected $savedApplication;

    public function setUp()
    {
        parent::setUp();

        $this->saveFactoryState();
    }

    protected function tearDown()
    {
        $this->restoreFactoryState();

        parent::tearDown();
    }

    /**
     * @group           Behaviour
     * @group           PageParametersToStateOnAfterConstruct
     * @covers          FOF30\Model\DataModel\Behaviour\PageParametersToState::onAfterConstruct
     * @dataProvider    PageParametersToStateDataprovider::getTestOnAfterConstruct
     */
    public function testOnAfterConstruct($test, $check)
    {
        $msg = 'PageParametersToState::onAfterConstruct %s - Case: '.$check['case'];

        // Use a table without assets support, otherwise we have to mock the application
        // while we need it to set the state
        $config = array(
            'idFieldName' => 'foftest_bare_id',
            'tableName'   => '#__foftest_bares'
        );

        $container = new TestContainer(array(
            'componentName'	=> 'com_fakeapp',
            'input'         => new Input($test['input'])
        ));

        /** @var \FOF30\Tests\Helpers\TestJoomlaPlatform $platform */
        $platform = $container->platform;
        $platform::$isAdmin = $test['mock']['admin'];
        $platform::$user = (object)array('id' => 99);
        $platform::$getUserStateFromRequest = 'do_not_mock';

        $model = new DataModelStub($container, $config);

        foreach($test['state'] as $key => $value)
        {
            $model->setState($key, $value);
        }

        $dispatcher = $model->getBehavioursDispatcher();

        $pageparams = new PageParametersToState($dispatcher);

        $fakeApp = new ClosureHelper(array(
            'getPageParameters' => function() use($test){
                return new \JRegistry($test['params']);
            }
        ));

        \JFactory::$application = $fakeApp;

        $pageparams->onAfterConstruct($model);

        foreach($check['state'] as $key => $value)
        {
            $this->assertEquals($value, $model->getState($key), sprintf($msg, 'Failed to set the correct value for the key: '.$key));
        }
    }
}
