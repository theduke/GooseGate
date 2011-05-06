<?php

namespace GooseGate;

class MongoDB
{
	/**
	 * @var \GooseGate\Mongo
	 */
	protected $connection;
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @param \GooseGate\Mongo $conn
	 * @param string $name
	 */
	public function __construct(Mongo $conn, $name) 
	{
		$this->connection = $conn;
		$this->name = $name;
	}
	
	/**
	 * @return \GooseGate\Mongo
	 */
	public function getConnection()
	{
		return $this->connection;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param string $name
	 * @return \GooseGate\MongoCollection
	 */
	public function __get($name)
	{
		return $this->selectCollection($name);
	}
	
	/**
	 * @param string $name
	 * @return \GooseGate\MongoCollection
	 */
	public function selectCollection($name)
	{
		return new MongoCollection($this, $name);
	}
}