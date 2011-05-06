<?php

namespace GooseGate;

class Mongo
{
	
	/**
	 * @var boolean
	 */
	public $connected = false;
	
	/**
	 * @var string
	 */
	protected $mongooseServer;
	
	/**
	 * @var string
	 */
	protected $mongoServer;
	
	/**
	 * name of the sleepy mongoose connectian
	 * @var string
	 */
	protected $connectionName;
	
	/**
	 * @var \GooseGate\HttpClient
	 */
	protected $client;
	
	/**
	 * @param string $server like domain.com:27080
	 */
	public function __construct($mongooseServer='localhost:27080', $mongoServer='localhost:27017')
	{
		$this->mongooseServer = $mongooseServer;
		$this->mongoServer = $mongoServer;
		
		$data = explode(':', $mongooseServer);
		
		if (!count($data) === 2)
		{
			throw new \Exception("Invalid server '$server'. Correct format: 'localhost:27080'");
		}
		
		$this->client = new HttpClient($data[0], $data[1]);
	}
	
	public function doRequest($path, $data=null, $forceGet=false)
	{
		$result = ($data && !$forceGet) ? $this->client->post($path, $data) : $this->client->get($path);
		
		if (!$result)
		{
			$msg = $this->client->getError();
			throw new \Exception("Could not contact server '{$this->mongooseServer}': $msg");
		}
		
		$json = json_decode($result);
		if (!$result)
		{
			throw new \Exception("Could not decode response json: '$reponse'");
		}
		
		return $json;
	}
	
	public function connect()
	{
		$this->connectionName = time();
		
		$result = $this->doRequest('/_connect', array(
			'server' => $this->mongoServer,	
			'name' => $this->connectionName
		));
		
		if (!array_key_exists('ok', $result) && $result['ok'] === 1)
		{
			throw new \Exception('Could not connect: ' . json_encode($result));
		}
	}
	
	/**
	 * @param string $dbname
	 * @return GooseGate\MongoDB $dbname
	 */
	public function __get($dbname)
	{
		return $this->selectDB($dbname);
	}
	
	/**
	 * @param string $name
	 * @return GooseGate\MongoDB $name
	 */
	public function selectDB($name)
	{
		return new MongoDB($this, $name);
	}
}