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
		$this->client->handle_redirects = false;
		
		$this->connect();
	}
	
	public function authenticate($user, $password)
	{
		$this->client->username = $user;
		$this->client->password = $password;
	}
	
	protected function doRequest($method, $path, $data=null, $addConnectionName=true)
	{
		if (!$data) $data = array();
		if ($addConnectionName) $data['name'] = $this->connectionName;
		
		if ($method === 'get') 
		{
			$flag = $this->client->get($path, $data);
		} else if ($method === 'post') {
			$flag = $this->client->post($path, $data);
		}
		
		if (!$flag)
		{
			$msg = $this->client->getError();
			throw new \Exception("Could not contact server '" . $this->mongooseServer . "': $msg");
		}
		
		$result = $this->client->getContent();

		$json = json_decode($result);
		if (!$result)
		{
			throw new \Exception("Could not decode response json: '$reponse'");
		}
		
		if (property_exists($result, 'ok') && $result->ok === 0) 
		{
			throw new \Exception("API call $path failed. " + $result->errmsg);
		}
		
		return $json;
	}
	
	public function doGet($path, $data=null)
	{
		return $this->doRequest('get', $path, $data);
	}
	
	public function doPost($path, $data)
	{
		return $this->doRequest('post', $path, $data);
	}
	
	public function connect()
	{
		$this->connectionName = time();
		
		$result = $this->doRequest('post', '/_connect', array(
			'server' => $this->mongoServer,	
			'name' => $this->connectionName
		));
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