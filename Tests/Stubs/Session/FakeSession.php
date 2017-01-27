<?php

class JSessionHandlerFake implements JSessionHandlerInterface
{
	private $id = 'FAKE_ID';

	private $name = 'FAKE_SESSION_NAME';

	public function start()
	{
		return true;
	}

	public function isStarted()
	{
		return true;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		return $this->name;
	}

	public function regenerate($destroy = false, $lifetime = null)
	{
		return true;
	}

	public function save()
	{
	}

	public function clear()
	{
	}
}