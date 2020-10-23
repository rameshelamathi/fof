<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Tests\Factory;

use FOF30\Factory\BasicFactory;
use FOF30\Tests\Helpers\FOFTestCase;
use FOF30\Tests\Helpers\ReflectionHelper;
use FOF30\Tests\Helpers\TestContainer;
use FOF30\Tests\Stubs\View\ViewStub;

require_once 'BasicFactoryDataprovider.php';

/**
 * @covers      FOF30\Factory\BasicFactory::<protected>
 * @covers      FOF30\Factory\BasicFactory::<private>
 * @package     FOF30\Tests\Factory
 */
class BasicFactoryTest extends FOFTestCase
{
    /**
     * @group           BasicFactory
     * @covers          FOF30\Factory\BasicFactory::__construct
     */
    public function test__construct()
    {
        $factory   = new BasicFactory(static::$container);
        $container = ReflectionHelper::getValue($factory, 'container');

        $this->assertSame(static::$container, $container, 'BasicFactory::__construct Failed to pass the container');
    }

    /**
     * @group           BasicFactory
     * @covers          FOF30\Factory\BasicFactory::controller
     * @dataProvider    BasicFactoryDataprovider::getTestController
     */
    public function testController($test, $check)
    {
        $msg   = 'BasicFactory::controller %s - Case: '.$check['case'];
        $names = array();

        $factory = $this->getMockBuilder('FOF30\Factory\BasicFactory')
            ->setMethods(array('createController'))
            ->setConstructorArgs(array(static::$container))
            ->getMock();

        $factory->method('createController')->willReturnCallback(function($class) use(&$test, &$names){
            $names[] = $class;
            $result = array_shift($test['mock']['create']);

            if($result !== true){
                throw new $result($class);
            }

            return $result;
        });

        if($check['exception'])
        {
            $this->setExpectedException($check['exception']);
        }

        $factory->controller($test['view']);

        $this->assertEquals($check['names'], $names, sprintf($msg, 'Failed to correctly search for the classname'));
    }

    /**
     * @group           BasicFactory
     * @covers          FOF30\Factory\BasicFactory::model
     * @dataProvider    BasicFactoryDataprovider::getTestModel
     */
    public function testModel($test, $check)
    {
        $msg   = 'BasicFactory::model %s - Case: '.$check['case'];
        $names = array();

        $factory = $this->getMockBuilder('FOF30\Factory\BasicFactory')
            ->setMethods(array('createModel'))
            ->setConstructorArgs(array(static::$container))
            ->getMock();

        $factory->method('createModel')->willReturnCallback(function($class) use(&$test, &$names){
            $names[] = $class;
            $result = array_shift($test['mock']['create']);

            if($result !== true){
                throw new $result($class);
            }

            return $result;
        });

        if($check['exception'])
        {
            $this->setExpectedException($check['exception']);
        }

        $factory->model($test['view']);

        $this->assertEquals($check['names'], $names, sprintf($msg, 'Failed to correctly search for the classname'));
    }

    /**
     * @group           BasicFactory
     * @covers          FOF30\Factory\BasicFactory::view
     * @dataProvider    BasicFactoryDataprovider::getTestView
     */
    public function testView($test, $check)
    {
        $msg   = 'BasicFactory::view %s - Case: '.$check['case'];
        $names = array();

        $factory = $this->getMockBuilder('FOF30\Factory\BasicFactory')
            ->setMethods(array('createView'))
            ->setConstructorArgs(array(static::$container))
            ->getMock();

        $factory->method('createView')->willReturnCallback(function($class) use(&$test, &$names){
            $names[] = $class;
            $result = array_shift($test['mock']['create']);

            if($result !== true){
                throw new $result($class);
            }

            return $result;
        });

        if($check['exception'])
        {
            $this->setExpectedException($check['exception']);
        }

        $factory->view($test['view'], $test['type']);

        $this->assertEquals($check['names'], $names, sprintf($msg, 'Failed to correctly search for the classname'));
    }

    /**
     * @group           BasicFactory
     * @covers          FOF30\Factory\BasicFactory::dispatcher
     * @dataProvider    BasicFactoryDataprovider::getTestDispatcher
     */
    public function testDispatcher($test, $check)
    {
        $msg  = 'BasicFactory::dispatcher %s - Case: '.$check['case'];
        $name = '';

        $factory = $this->getMockBuilder('FOF30\Factory\BasicFactory')
            ->setMethods(array('createDispatcher'))
            ->setConstructorArgs(array(static::$container))
            ->getMock();

        $factory->method('createDispatcher')->willReturnCallback(function($class) use($test, &$name){
                $name   = $class;
                $result = $test['mock']['create'];

                if($result !== true){
                    throw new $result($class);
                }

                return $result;
            });

        $result = $factory->dispatcher();

        $this->assertEquals($check['name'], $name, sprintf($msg, 'Failed to search for the correct class'));

        if(is_object($result))
        {
            $this->assertEquals('FOF30\Dispatcher\Dispatcher', get_class($result), sprintf($msg, 'Failed to return the correct result'));
        }
        else
        {
            $this->assertEquals($check['result'], $result, sprintf($msg, 'Failed to return the correct result'));
        }
    }

    /**
     * @group           BasicFactory
     * @covers          FOF30\Factory\BasicFactory::toolbar
     * @dataProvider    BasicFactoryDataprovider::getTestToolbar
     */
    public function testToolbar($test, $check)
    {
        $msg  = 'BasicFactory::toolbar %s - Case: '.$check['case'];
        $name = '';

        $factory = $this->getMockBuilder('FOF30\Factory\BasicFactory')
            ->setMethods(array('createToolbar'))
            ->setConstructorArgs(array(static::$container))
            ->getMock();

        $factory->method('createToolbar')->willReturnCallback(function($class) use($test, &$name){
            $name   = $class;
            $result = $test['mock']['create'];

            if($result !== true){
                throw new $result($class);
            }

            return $result;
        });

        $result = $factory->toolbar();

        $this->assertEquals($check['name'], $name, sprintf($msg, 'Failed to search for the correct class'));

        if(is_object($result))
        {
            $this->assertEquals('FOF30\Toolbar\Toolbar', get_class($result), sprintf($msg, 'Failed to return the correct result'));
        }
        else
        {
            $this->assertEquals($check['result'], $result, sprintf($msg, 'Failed to return the correct result'));
        }
    }

    /**
     * @group           BasicFactory
     * @covers          FOF30\Factory\BasicFactory::transparentAuthentication
     * @dataProvider    BasicFactoryDataprovider::getTestTransparentAuthentication
     */
    public function testTransparentAuthentication($test, $check)
    {
        $msg  = 'BasicFactory::transparentAuthentication %s - Case: '.$check['case'];
        $name = '';

        $factory = $this->getMockBuilder('FOF30\Factory\BasicFactory')
            ->setMethods(array('createTransparentAuthentication'))
            ->setConstructorArgs(array(static::$container))
            ->getMock();

        $factory->method('createTransparentAuthentication')->willReturnCallback(function($class) use($test, &$name){
            $name   = $class;
            $result = $test['mock']['create'];

            if($result !== true){
                throw new $result($class);
            }

            return $result;
        });

        $result = $factory->transparentAuthentication();

        $this->assertEquals($check['name'], $name, sprintf($msg, 'Failed to search for the correct class'));

        if(is_object($result))
        {
            $this->assertEquals('FOF30\TransparentAuthentication\TransparentAuthentication', get_class($result), sprintf($msg, 'Failed to return the correct result'));
        }
        else
        {
            $this->assertEquals($check['result'], $result, sprintf($msg, 'Failed to return the correct result'));
        }
    }

    /**
     * @group           BasicFactory
     * @covers          FOF30\Factory\BasicFactory::viewFinder
     */
    public function testViewFinder()
    {
        $msg  = 'BasicFactory::viewFinder %s';

        $configuration = $this->getMockBuilder('FOF30\Configuration\Configuration')
            ->setMethods(array('get'))
            ->setConstructorArgs(array())
            ->setMockClassName('')
            ->disableOriginalConstructor()
            ->getMock();

        $configuration->method('get')->willReturnCallback(
            function($key, $default){
                return $default;
            }
        );

        $container = new TestContainer(array(
            'appConfig' => $configuration,
        ));

        $platform = $container->platform;
        $platform::$template = 'fake_test_template';
        $platform::$uriBase  = 'www.example.com';

        $view    = new ViewStub($container);
        $factory = new BasicFactory($container);

        $result = $factory->viewFinder($view, array());

        // I can only test if the correct object is passed, since we are simply collecting all the data
        // and passing it to the ViewTemplateFinder constructor
        $this->assertEquals('FOF30\View\ViewTemplateFinder', get_class($result), sprintf($msg, 'Returned the wrong result'));
    }
}
