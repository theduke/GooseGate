<?php

/**
 * GooseGate
 *
 * LICENSE
 *
 * This source file is subject to the GPLv3 license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 * 
 * @package    GooseGate
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt     GPLv3
 * 
 * @author Christoph Herzog chris@theduke.at
 */

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
	
	public function runCommand($spec)
	{
		$path = '/' . $this->name . '/_cmd';
		$data = array('cmd' => json_encode($spec));
		
		$response = $this->connection->doPost($path, $data);
		
		return $response;
	}
	
	public function authenticate($username, $password)
	{
		$path = '/' . $this->name . '/_authenticate';
		$data = array(
			'username' => $username,
			'password' => $password
		);
		
		$response = $this->connection->doPost($path, $data);
		
		return $response;
	}
}