<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

class DataControllerDataprovider
{
	public static function getTest__construct()
	{
		$data[] = [
			[
				'model'      => '',
				'view'       => '',
				'cache'      => '',
				'privileges' => '',
			],
			[
				'case'       => 'Model and view not passed',
				'model'      => 'dummycontrollers',
				'view'       => 'dummycontrollers',
				'cache'      => ['browse', 'read'],
				'privileges' => [
					'*editown'    => 'core.edit.own',
					'add'         => 'core.create',
					'apply'       => '&getACLForApplySave',
					'archive'     => 'core.edit.state',
					'cancel'      => 'core.edit.state',
					'copy'        => '@add',
					'edit'        => 'core.edit',
					'loadhistory' => '@edit',
					'orderup'     => 'core.edit.state',
					'orderdown'   => 'core.edit.state',
					'publish'     => 'core.edit.state',
					'remove'      => 'core.delete',
					'save'        => '&getACLForApplySave',
					'savenew'     => 'core.create',
					'saveorder'   => 'core.edit.state',
					'trash'       => 'core.edit.state',
					'unpublish'   => 'core.edit.state',
					'forceRemove' => 'core.delete',
				],
			],
		];

		$data[] = [
			[
				'model'      => 'custom',
				'view'       => 'foobar',
				'cache'      => 'foo, bar',
				'privileges' => ['foo' => 'core.foo.bar'],
			],
			[
				'case'       => 'Model, cacheable task, privileges and view passed',
				'model'      => 'custom',
				'view'       => 'foobar',
				'cache'      => ['foo', 'bar'],
				'privileges' => [
					'*editown'    => 'core.edit.own',
					'add'         => 'core.create',
					'apply'       => '&getACLForApplySave',
					'archive'     => 'core.edit.state',
					'cancel'      => 'core.edit.state',
					'copy'        => '@add',
					'edit'        => 'core.edit',
					'loadhistory' => '@edit',
					'orderup'     => 'core.edit.state',
					'orderdown'   => 'core.edit.state',
					'publish'     => 'core.edit.state',
					'remove'      => 'core.delete',
					'save'        => '&getACLForApplySave',
					'savenew'     => 'core.create',
					'saveorder'   => 'core.edit.state',
					'trash'       => 'core.edit.state',
					'unpublish'   => 'core.edit.state',
					'foo'         => 'core.foo.bar',
					'forceRemove' => 'core.delete',
				],
			],
		];

		return $data;
	}

	public static function getTestExecute()
	{
		// Default task, the controller should detect the correct one
		$data[] = [
			[
				'task' => 'default',
			],
			[
				'getCrud' => true,
			],
		];

		// Task passed
		$data[] = [
			[
				'task' => 'read',
			],
			[
				'getCrud' => false,
			],
		];

		return $data;
	}

	public static function getTestGetView()
	{
		$data[] = [
			[
				'name'            => 'foobar',
				'config'          => [],
				'constructConfig' => [],
				'mock'            => [
					'view'      => null,
					'viewName'  => null,
					'instances' => [],
					'format'    => null,
					'getView'   => 'mocked',
				],
			],
			[
				'case'     => 'Creating HTML view, name passed, view not cached, internal reference are empty',
				'result'   => 'mocked',
				'viewName' => 'foobar',
				'type'     => 'html',
				'config'   => [],
			],
		];

		$data[] = [
			[
				'name'            => 'foobar',
				'config'          => [],
				'constructConfig' => [],
				'mock'            => [
					'view'      => null,
					'viewName'  => null,
					'instances' => [],
					'format'    => 'html',
					'getView'   => 'mocked',
				],
			],
			[
				'case'     => 'Creating HTML view, name passed, view not cached, internal reference are empty',
				'result'   => 'mocked',
				'viewName' => 'foobar',
				'type'     => 'html',
				'config'   => [],
			],
		];

		$data[] = [
			[
				'name'            => null,
				'config'          => [],
				'constructConfig' => [],
				'mock'            => [
					'view'      => null,
					'viewName'  => 'foobar',
					'instances' => [],
					'format'    => null,
					'getView'   => 'mocked',
				],
			],
			[
				'case'     => 'Creating HTML view, name not passed, fetched from the viewName property',
				'result'   => 'mocked',
				'viewName' => 'foobar',
				'type'     => 'html',
				'config'   => [],
			],
		];

		$data[] = [
			[
				'name'            => null,
				'config'          => [],
				'constructConfig' => [],
				'mock'            => [
					'view'      => 'foobar',
					'viewName'  => null,
					'instances' => [],
					'format'    => null,
					'getView'   => 'mocked',
				],
			],
			[
				'case'     => 'Creating HTML view, name not passed, fetched from the view property',
				'result'   => 'mocked',
				'viewName' => 'foobar',
				'type'     => 'html',
				'config'   => [],
			],
		];

		$data[] = [
			[
				'name'            => 'foobar',
				'config'          => [],
				'constructConfig' => [],
				'mock'            => [
					'view'      => null,
					'viewName'  => null,
					'instances' => [],
					'format'    => 'json',
					'getView'   => 'mocked',
				],
			],
			[
				'case'     => 'Creating JSON view, name passed, view not cached, internal reference are empty',
				'result'   => 'mocked',
				'viewName' => 'foobar',
				'type'     => 'json',
				'config'   => [],
			],
		];

		$data[] = [
			[
				'name'            => 'foobar',
				'config'          => [],
				'constructConfig' => [],
				'mock'            => [
					'view'      => null,
					'viewName'  => null,
					'instances' => ['foobar' => 'cached'],
					'format'    => null,
					'getView'   => 'mocked',
				],
			],
			[
				'case'     => 'Creating HTML view, fetched from the cache',
				'result'   => 'cached',
				'viewName' => '',
				'type'     => '',
				'config'   => [],
			],
		];

		$data[] = [
			[
				'name'            => 'foobar',
				'config'          => ['foo' => 'bar'],
				'constructConfig' => [],
				'mock'            => [
					'view'      => null,
					'viewName'  => null,
					'instances' => [],
					'format'    => null,
					'getView'   => 'mocked',
				],
			],
			[
				'case'     => 'Creating HTML view, name and config passed, view not cached, internal reference are empty',
				'result'   => 'mocked',
				'viewName' => 'foobar',
				'type'     => 'html',
				'config'   => ['foo' => 'bar'],
			],
		];

		$data[] = [
			[
				'name'            => 'foobar',
				'config'          => [],
				'constructConfig' => [
					'viewConfig' => [
						'foo' => 'bar',
					],
				],
				'mock'            => [
					'view'      => null,
					'viewName'  => null,
					'instances' => [],
					'format'    => null,
					'getView'   => 'mocked',
				],
			],
			[
				'case'     => 'Creating HTML view, name and config passed (in constructor), view not cached, internal reference are empty',
				'result'   => 'mocked',
				'viewName' => 'foobar',
				'type'     => 'html',
				'config'   => ['foo' => 'bar'],
			],
		];

		return $data;
	}

	public static function getTestBrowse()
	{
		$data[] = [
			[
				'mock' => [
					'getForm' => false,
					'cache'   => ['browse', 'read'],
					'layout'  => null,
					'input'   => [
						'savestate' => 0,
					],
				],
			],
			[
				'case'      => "Don't want any state saving",
				'display'   => true,
				'savestate' => 0,
				'formName'  => 'form.default',
			],
		];

		$data[] = [
			[
				'mock' => [
					'getForm' => false,
					'cache'   => ['browse', 'read'],
					'layout'  => null,
					'input'   => [],
				],
			],
			[
				'case'      => "Variable not set, by default I save the stater",
				'display'   => true,
				'savestate' => true,
				'formName'  => 'form.default',
			],
		];

		return $data;
	}

	public static function getTestRead()
	{
		$data[] = [
			[
				'mock' => [
					'getId'   => [3, null],
					'ids'     => 0,
					'layout'  => '',
					'getForm' => false,
					'cache'   => ['browse', 'read'],
				],
			],
			[
				'case'         => 'Getting the id from the model, using the default layout',
				'getIdFromReq' => 0,
				'display'      => true,
				'exception'    => false,
				'layout'       => 'item',
			],
		];

		$data[] = [
			[
				'mock' => [
					'getId'   => [3, null],
					'ids'     => 0,
					'layout'  => '',
					'getForm' => false,
					'cache'   => ['browse'],
				],
			],
			[
				'case'         => 'Getting the id from the model, using the default layout, task is not cacheable',
				'getIdFromReq' => 0,
				'display'      => false,
				'exception'    => false,
				'layout'       => 'item',
			],
		];

		$data[] = [
			[
				'mock' => [
					'getId'   => [false, 3],
					'ids'     => [3],
					'layout'  => '',
					'getForm' => false,
					'cache'   => ['browse', 'read'],
				],
			],
			[
				'case'         => 'Getting the id from the request, using the default layout',
				'getIdFromReq' => 1,
				'display'      => true,
				'exception'    => false,
				'layout'       => 'item',
			],
		];

		$data[] = [
			[
				'mock' => [
					'getId'   => [false, 3],
					'ids'     => [],
					'layout'  => '',
					'getForm' => false,
					'cache'   => ['browse', 'read'],
				],
			],
			[
				'case'         => 'Getting the id from the request, something wrong happens - part 1',
				'getIdFromReq' => 1,
				'display'      => true,
				'exception'    => true,
				'layout'       => 'item',
			],
		];

		$data[] = [
			[
				'mock' => [
					'getId'   => [false, false],
					'ids'     => [3],
					'layout'  => '',
					'getForm' => false,
					'cache'   => ['browse', 'read'],
				],
			],
			[
				'case'         => 'Getting the id from the request, something wrong happens - part 2',
				'getIdFromReq' => 1,
				'display'      => true,
				'exception'    => true,
				'layout'       => 'item',
			],
		];

		return $data;
	}

	public static function getTestAdd()
	{
		$data[] = [
			[
				'mock' => [
					'session' => '',
					'getForm' => false,
					'layout'  => '',
					'cache'   => ['browse', 'read'],
				],
			],
			[
				'case'     => 'No data in the session, no layout, no cache, no form',
				'display'  => false,
				'bind'     => '',
				'formName' => 'form.form',
				'layout'   => 'form',
			],
		];

		return $data;
	}

	public static function getTestEdit()
	{
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'session'   => null,
					'getId'     => true,
					'lock'      => true,
					'layout'    => '',
					'getForm'   => false,
					'cache'     => ['browse', 'read'],
				],
			],
			[
				'case'       => 'Getting the id from the model, no layout, no data in the session, everything works fine',
				'bind'       => false,
				'getFromReq' => false,
				'redirect'   => false,
				'url'        => '',
				'msg'        => '',
				'display'    => false,
				'layout'     => 'form',
				'formName'   => 'form.form',
			],
		];

		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'session'   => null,
					'getId'     => true,
					'lock'      => true,
					'layout'    => '',
					'getForm'   => false,
					'cache'     => ['browse', 'read', 'edit'],
				],
			],
			[
				'case'       => 'Task is cached',
				'bind'       => false,
				'getFromReq' => false,
				'redirect'   => false,
				'url'        => '',
				'msg'        => '',
				'display'    => true,
				'layout'     => 'form',
				'formName'   => 'form.form',
			],
		];

		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'session'   => ['foo' => 'bar'],
					'getId'     => false,
					'lock'      => true,
					'layout'    => 'custom',
					'getForm'   => false,
					'cache'     => ['browse', 'read'],
				],
			],
			[
				'case'       => 'Getting the id from the request, with layout, fetch data from the session, everything works fine',
				'bind'       => ['foo' => 'bar'],
				'getFromReq' => true,
				'redirect'   => false,
				'url'        => '',
				'msg'        => '',
				'display'    => false,
				'layout'     => 'custom',
				'formName'   => 'form.custom',
			],
		];

		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'session'   => null,
					'getId'     => true,
					'lock'      => 'throw',
					'layout'    => '',
					'getForm'   => false,
					'cache'     => ['browse', 'read'],
				],
			],
			[
				'case'       => 'Lock throws an error, no custom url',
				'bind'       => false,
				'getFromReq' => false,
				'redirect'   => true,
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'        => 'Exception thrown while locking',
				'display'    => false,
				'layout'     => '',
				'formName'   => '',
			],
		];

		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'session'   => null,
					'getId'     => true,
					'lock'      => 'throw',
					'layout'    => '',
					'getForm'   => false,
					'cache'     => ['browse', 'read'],
				],
			],
			[
				'case'       => 'Lock throws an error, custom url set',
				'bind'       => false,
				'getFromReq' => false,
				'redirect'   => true,
				'url'        => 'http://www.example.com/index.php?view=custom',
				'msg'        => 'Exception thrown while locking',
				'display'    => false,
				'layout'     => '',
				'formName'   => '',
			],
		];

		return $data;
	}

	public static function getTestApply()
	{
		$data[] = [
			[
				'mock' => [
					'id'        => 3,
					'returnurl' => '',
					'apply'     => true,
				],
			],
			[
				'redirect' => true,
				'url'      => 'index.php?option=com_fakeapp&view=dummycontroller&task=edit&id=3',
				'msg'      => 'COM_FAKEAPP_LBL_DUMMYCONTROLLER_SAVED',
			],
		];

		$data[] = [
			[
				'mock' => [
					'id'        => 3,
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'apply'     => true,
				],
			],
			[
				'redirect' => true,
				'url'      => 'http://www.example.com/index.php?view=custom',
				'msg'      => 'COM_FAKEAPP_LBL_DUMMYCONTROLLER_SAVED',
			],
		];

		$data[] = [
			[
				'mock' => [
					'id'        => 3,
					'returnurl' => '',
					'apply'     => false,
				],
			],
			[
				'redirect' => false,
				'url'      => '',
				'msg'      => '',
			],
		];

		return $data;
	}

	public static function getTestCopy()
	{
		// Everything works fine, no custom redirect set
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'find'      => [true],
					'copy'      => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => 'COM_FAKEAPP_LBL_DUMMYCONTROLLER_COPIED',
				'type' => null,
			],
		];

		// Everything works fine, custom redirect set
		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'find'      => [true],
					'copy'      => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'http://www.example.com/index.php?view=custom',
				'msg'  => 'COM_FAKEAPP_LBL_DUMMYCONTROLLER_COPIED',
				'type' => null,
			],
		];

		// Copy throws an error
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'find'      => [true],
					'copy'      => ['throw'],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => 'Exception in copy',
				'type' => 'error',
			],
		];

		// Find throws an error
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'find'      => ['throw'],
					'copy'      => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => 'Exception in find',
				'type' => 'error',
			],
		];

		return $data;
	}

	public static function getTestSave()
	{
		// Everything is fine, no custom redirect set
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'apply'     => true,
				],
			],
			[
				'redirect' => true,
				'url'      => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'      => 'COM_FAKEAPP_LBL_DUMMYCONTROLLER_SAVED',
			],
		];

		// Everything is fine, custom redirect set
		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'apply'     => true,
				],
			],
			[
				'redirect' => true,
				'url'      => 'http://www.example.com/index.php?view=custom',
				'msg'      => 'COM_FAKEAPP_LBL_DUMMYCONTROLLER_SAVED',
			],
		];

		// An error occurs, no custom redirect set
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'apply'     => false,
				],
			],
			[
				'redirect' => false,
				'url'      => '',
				'msg'      => '',
			],
		];

		return $data;
	}

	public static function getTestSavenew()
	{
		// Everything is fine, no custom redirect set
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'apply'     => true,
				],
			],
			[
				'redirect' => true,
				'url'      => 'index.php?option=com_fakeapp&view=dummycontroller&task=add',
				'msg'      => 'COM_FAKEAPP_LBL_DUMMYCONTROLLER_SAVED',
			],
		];

		// Everything is fine, custom redirect set
		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'apply'     => true,
				],
			],
			[
				'redirect' => true,
				'url'      => 'http://www.example.com/index.php?view=custom',
				'msg'      => 'COM_FAKEAPP_LBL_DUMMYCONTROLLER_SAVED',
			],
		];

		// An error occurs, no custom redirect set
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'apply'     => false,
				],
			],
			[
				'redirect' => false,
				'url'      => '',
				'msg'      => '',
			],
		];

		return $data;
	}

	public static function getTestCancel()
	{
		// Getting the id from the model, no custom redirect set
		$data[] = [
			[
				'mock' => [
					'session'   => ['foo' => 'bar'],
					'returnurl' => '',
					'getId'     => 3,
					'ids'       => [],
				],
			],
			[
				'getFromReq' => false,
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
			],
		];

		// Getting the id from request, no custom redirect set
		$data[] = [
			[
				'mock' => [
					'session'   => null,
					'returnurl' => '',
					'getId'     => null,
					'ids'       => [3],
				],
			],
			[
				'getFromReq' => true,
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
			],
		];

		// Getting the id from the model, custom redirect set
		$data[] = [
			[
				'mock' => [
					'session'   => null,
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'getId'     => 3,
					'ids'       => [],
				],
			],
			[
				'getFromReq' => false,
				'url'        => 'http://www.example.com/index.php?view=custom',
			],
		];

		return $data;
	}

	public static function getTestPublish()
	{
		// Everything works fine, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'publish'   => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => null,
				'type' => null,
			],
		];

		// Everything works fine, custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'publish'   => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'http://www.example.com/index.php?view=custom',
				'msg'  => null,
				'type' => null,
			],
		];

		// Publish throws an error, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'publish'   => ['throw'],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => 'Exception in publish',
				'type' => 'error',
			],
		];

		return $data;
	}

	public static function getTestUnpublish()
	{
		// Everything works fine, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'unpublish' => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => null,
				'type' => null,
			],
		];

		// Everything works fine, custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'unpublish' => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'http://www.example.com/index.php?view=custom',
				'msg'  => null,
				'type' => null,
			],
		];

		// Unpublish throws an error, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'unpublish' => ['throw'],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => 'Exception in unpublish',
				'type' => 'error',
			],
		];

		return $data;
	}

	public static function getTestArchive()
	{
		// Everything works fine, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'archive'   => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => null,
				'type' => null,
			],
		];

		// Everything works fine, custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'archive'   => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'http://www.example.com/index.php?view=custom',
				'msg'  => null,
				'type' => null,
			],
		];

		// Archive throws an error, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'archive'   => ['throw'],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => 'Exception in archive',
				'type' => 'error',
			],
		];

		return $data;
	}

	public static function getTestTrash()
	{
		// Everything works fine, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'trash'     => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => null,
				'type' => null,
			],
		];

		// Everything works fine, custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'trash'     => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'http://www.example.com/index.php?view=custom',
				'msg'  => null,
				'type' => null,
			],
		];

		// Trash throws an error, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'trash'     => ['throw'],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => 'Exception in trash',
				'type' => 'error',
			],
		];

		return $data;
	}

	public static function getTestCheckin()
	{
		// Everything works fine, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'checkin'   => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => null,
				'type' => null,
			],
		];

		// Everything works fine, custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'checkin'   => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'http://www.example.com/index.php?view=custom',
				'msg'  => null,
				'type' => null,
			],
		];

		// Trash throws an error, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'checkin'   => ['throw'],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => 'Exception in checkin',
				'type' => 'error',
			],
		];

		return $data;
	}

	public static function getTestSaveorder()
	{
		$data[] = [
			[
				'ordering'  => [3, 1, 2, 4],
				'returnurl' => '',
				'id'        => 'foftest_foobar_id',
				'table'     => '#__foftest_foobars',
				'mock'      => [
					'ids' => [1, 2, 3, 4],
				],
			],
			[
				'case' => 'No custom redirect set',
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => null,
				'type' => null,
				'rows' => [2, 3, 1, 4, 5],
			],
		];

		$data[] = [
			[
				'ordering'  => [3, 1, 2, 4],
				'returnurl' => 'http://www.example.com/index.php?view=custom',
				'id'        => 'foftest_foobar_id',
				'table'     => '#__foftest_foobars',
				'mock'      => [
					'ids' => [1, 2, 3, 4],
				],
			],
			[
				'case' => 'Custom redirect set',
				'url'  => 'http://www.example.com/index.php?view=custom',
				'msg'  => null,
				'type' => null,
				'rows' => [2, 3, 1, 4, 5],
			],
		];

		$data[] = [
			[
				'ordering'  => [3, 1, 2, 4],
				'returnurl' => '',
				'id'        => 'foftest_bare_id',
				'table'     => '#__foftest_bares',
				'mock'      => [
					'ids' => [1, 2, 3, 4],
				],
			],
			[
				'case' => 'Table with no ordering support',
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => '#__foftest_bares does not support ordering.',
				'type' => 'error',
				'rows' => [1, 2, 3, 4, 5],
			],
		];

		return $data;
	}

	public static function getTestOrderdown()
	{
		// Everything works fine, no custom redirect set, getting the id from the model
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'move'      => [true],
					'getId'     => 3,
					'ids'       => [],
				],
			],
			[
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'getFromReq' => false,
				'msg'        => null,
				'type'       => null,
			],
		];

		// Everything works fine, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'move'      => [true],
					'getId'     => null,
					'ids'       => [3],
				],
			],
			[
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'getFromReq' => true,
				'msg'        => null,
				'type'       => null,
			],
		];

		// Everything works fine, custom redirect set, getting the id from the model
		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'move'      => [true],
					'getId'     => 3,
					'ids'       => [],
				],
			],
			[
				'url'        => 'http://www.example.com/index.php?view=custom',
				'getFromReq' => false,
				'msg'        => null,
				'type'       => null,
			],
		];

		// Move throws an error, no custom redirect set, getting the id from the model
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'move'      => ['throw'],
					'getId'     => 3,
					'ids'       => [],
				],
			],
			[
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'getFromReq' => false,
				'msg'        => 'Exception in move',
				'type'       => 'error',
			],
		];

		return $data;
	}

	public static function getTestOrderup()
	{
		// Everything works fine, no custom redirect set, getting the id from the model
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'move'      => [true],
					'getId'     => 3,
					'ids'       => [],
				],
			],
			[
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'getFromReq' => false,
				'msg'        => null,
				'type'       => null,
			],
		];

		// Everything works fine, no custom redirect set, getting the id from the request
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'move'      => [true],
					'getId'     => null,
					'ids'       => [3],
				],
			],
			[
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'getFromReq' => true,
				'msg'        => null,
				'type'       => null,
			],
		];

		// Everything works fine, custom redirect set, getting the id from the model
		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'move'      => [true],
					'getId'     => 3,
					'ids'       => [],
				],
			],
			[
				'url'        => 'http://www.example.com/index.php?view=custom',
				'getFromReq' => false,
				'msg'        => null,
				'type'       => null,
			],
		];

		// Move throws an error, no custom redirect set, getting the id from the model
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'move'      => ['throw'],
					'getId'     => 3,
					'ids'       => [],
				],
			],
			[
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'getFromReq' => false,
				'msg'        => 'Exception in move',
				'type'       => 'error',
			],
		];

		return $data;
	}

	public static function getTestRemove()
	{
		// Everything works fine, no custom redirect set
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'find'      => [true],
					'delete'    => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => 'COM_FAKEAPP_LBL_DUMMYCONTROLLER_DELETED',
				'type' => null,
			],
		];

		// Everything works fine, custom redirect set
		$data[] = [
			[
				'mock' => [
					'returnurl' => 'http://www.example.com/index.php?view=custom',
					'find'      => [true],
					'delete'    => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'http://www.example.com/index.php?view=custom',
				'msg'  => 'COM_FAKEAPP_LBL_DUMMYCONTROLLER_DELETED',
				'type' => null,
			],
		];

		// Delete throws an error
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'find'      => [true],
					'delete'    => ['throw'],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => 'Exception in delete',
				'type' => 'error',
			],
		];

		// Find throws an error
		$data[] = [
			[
				'mock' => [
					'returnurl' => '',
					'find'      => ['throw'],
					'delete'    => [true],
					'ids'       => [3],
				],
			],
			[
				'url'  => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'  => 'Exception in find',
				'type' => 'error',
			],
		];

		return $data;
	}

	public static function getTestGetModel()
	{
		$data[] = [
			[
				'name' => 'datafoobars',
				'mock' => [
					'modelname' => null,
				],
			],
			[
				'exception' => false,
			],
		];

		$data[] = [
			[
				'name' => null,
				'mock' => [
					'modelname' => 'datafoobars',
				],
			],
			[
				'exception' => false,
			],
		];

		$data[] = [
			[
				'name' => null,
				'mock' => [
					'modelname' => 'foobar',
				],
			],
			[
				'exception' => true,
			],
		];

		return $data;
	}

	public static function getTestGetIDsFromRequest()
	{
		$data[] = [
			[
				'load' => false,
				'mock' => [
					'cid' => [],
					'id'  => 0,
					'kid' => 0,
				],
			],
			[
				'case'   => 'Everything is empty, not asked for loading',
				'result' => [],
				'load'   => false,
				'loadid' => null,
			],
		];

		$data[] = [
			[
				'load' => true,
				'mock' => [
					'cid' => [],
					'id'  => 0,
					'kid' => 0,
				],
			],
			[
				'case'   => 'Everything is empty, asked for loading',
				'result' => [],
				'load'   => false,
				'loadid' => null,
			],
		];

		$data[] = [
			[
				'load' => false,
				'mock' => [
					'cid' => [3, 4, 5],
					'id'  => 0,
					'kid' => 0,
				],
			],
			[
				'case'   => 'Passed an array of id (cid), not asked for loading',
				'result' => [3, 4, 5],
				'load'   => false,
				'loadid' => null,
			],
		];

		$data[] = [
			[
				'load' => true,
				'mock' => [
					'cid' => [3, 4, 5],
					'id'  => 0,
					'kid' => 0,
				],
			],
			[
				'case'   => 'Passed an array of id (cid), asked for loading',
				'result' => [3, 4, 5],
				'load'   => true,
				'loadid' => ['id' => 3],
			],
		];

		$data[] = [
			[
				'load' => false,
				'mock' => [
					'cid' => [],
					'id'  => 3,
					'kid' => 0,
				],
			],
			[
				'case'   => 'Passed a single id (id) , not asked for loading',
				'result' => [3],
				'load'   => false,
				'loadid' => null,
			],
		];

		$data[] = [
			[
				'load' => true,
				'mock' => [
					'cid' => [],
					'id'  => 3,
					'kid' => 0,
				],
			],
			[
				'case'   => 'Passed a single id (id), asked for loading',
				'result' => [3],
				'load'   => true,
				'loadid' => ['id' => 3],
			],
		];

		$data[] = [
			[
				'load' => false,
				'mock' => [
					'cid' => [],
					'id'  => 0,
					'kid' => 3,
				],
			],
			[
				'case'   => 'Passed a single id (kid) , not asked for loading',
				'result' => [3],
				'load'   => false,
				'loadid' => null,
			],
		];

		$data[] = [
			[
				'load' => true,
				'mock' => [
					'cid' => [],
					'id'  => 0,
					'kid' => 3,
				],
			],
			[
				'case'   => 'Passed a single id (kid), asked for loading',
				'result' => [3],
				'load'   => true,
				'loadid' => ['id' => 3],
			],
		];

		$data[] = [
			[
				'load' => false,
				'mock' => [
					'cid' => [4, 5, 6],
					'id'  => 3,
					'kid' => 0,
				],
			],
			[
				'case'   => 'Passing an array of id (cid) and a single id (id), not asked for loading',
				'result' => [4, 5, 6],
				'load'   => false,
				'loadid' => null,
			],
		];

		$data[] = [
			[
				'load' => false,
				'mock' => [
					'cid' => [4, 5, 6],
					'id'  => 0,
					'kid' => 3,
				],
			],
			[
				'case'   => 'Passing an array of id (cid) and a single id (kid), not asked for loading',
				'result' => [4, 5, 6],
				'load'   => false,
				'loadid' => null,
			],
		];

		$data[] = [
			[
				'load' => false,
				'mock' => [
					'cid' => [],
					'id'  => 4,
					'kid' => 3,
				],
			],
			[
				'case'   => 'Passing a single id (id and kid), not asked for loading',
				'result' => [4],
				'load'   => false,
				'loadid' => null,
			],
		];

		$data[] = [
			[
				'load' => false,
				'mock' => [
					'cid' => [4, 5, 6],
					'id'  => 3,
					'kid' => 7,
				],
			],
			[
				'case'   => 'Passing everything, not asked for loading',
				'result' => [4, 5, 6],
				'load'   => false,
				'loadid' => null,
			],
		];

		return $data;
	}

	public static function getTestLoadHistory()
	{
		$data[] = [
			[
				'mock' => [
					'version'   => 1,
					'returnurl' => '',
					'history'   => '',
					'checkACL'  => true,
				],
			],
			[
				'case'       => 'Everything is going smooth',
				'version_id' => 1,
				'alias'      => 'com_fakeapp.dummycontroller',
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'        => 'JLIB_APPLICATION_SUCCESS_LOAD_HISTORY',
				'type'       => null,
				'result'     => true,
			],
		];

		$data[] = [
			[
				'mock' => [
					'version'   => 1,
					'returnurl' => 'www.example.com',
					'history'   => '',
					'checkACL'  => true,
				],
			],
			[
				'case'       => 'Everything is going smooth, custom redirect',
				'version_id' => 1,
				'alias'      => 'com_fakeapp.dummycontroller',
				'url'        => 'www.example.com',
				'msg'        => 'JLIB_APPLICATION_SUCCESS_LOAD_HISTORY',
				'type'       => null,
				'result'     => true,
			],
		];

		$data[] = [
			[
				'mock' => [
					'version'   => 1,
					'returnurl' => '',
					'history'   => 'exception',
					'checkACL'  => true,
				],
			],
			[
				'case'       => 'Load history throws an error',
				'version_id' => 1,
				'alias'      => 'com_fakeapp.dummycontroller',
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'        => 'Load history error',
				'type'       => 'error',
				'result'     => false,
			],
		];

		$data[] = [
			[
				'mock' => [
					'version'   => 1,
					'returnurl' => '',
					'history'   => '',
					'checkACL'  => false,
				],
			],
			[
				'case'       => 'Check ACL returns false',
				'version_id' => 1,
				'alias'      => 'com_fakeapp.dummycontroller',
				'url'        => 'index.php?option=com_fakeapp&view=dummycontrollers',
				'msg'        => 'JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED',
				'type'       => 'error',
				'result'     => false,
			],
		];

		return $data;
	}

	public static function getTestGetItemidURLSuffix()
	{
		$data[] = [
			[
				'mock' => [
					'frontend' => false,
					'itemid'   => 0,
				],
			],
			[
				'case'   => 'Backend, not Itemid set',
				'result' => '',
			],
		];

		$data[] = [
			[
				'mock' => [
					'frontend' => false,
					'itemid'   => 130,
				],
			],
			[
				'case'   => 'Backend, with Itemid set',
				'result' => '',
			],
		];

		$data[] = [
			[
				'mock' => [
					'frontend' => true,
					'itemid'   => 0,
				],
			],
			[
				'case'   => 'Frontend, not Itemid set',
				'result' => '',
			],
		];

		$data[] = [
			[
				'mock' => [
					'frontend' => true,
					'itemid'   => 130,
				],
			],
			[
				'case'   => 'Frontend, with Itemid set',
				'result' => '&Itemid=130',
			],
		];

		return $data;
	}
}
