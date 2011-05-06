<?php

namespace GooseGate;

class MongoCollection
{
	/**
	 * @var \GooseGate\MongoDB
	 */
	protected $db;
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @param \GooseGate\MongoDB $db
	 * @param string $name
	 */
	public function __construct(MongoDB $db, $name)
	{
		$this->db = $db;
		$this->name = $name;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	public function find($criteria=null, $fields=null, $sortFields=null, $skip=null, $limit=null, $batch_size=20)
	{
		$path = '/' . $this->db->getName() . '/' . $this->name . '/_find';
		$data = array();
		
		if ($criteria) $data['criteria'] = json_encode($criteria); 
		if ($fields) $data['fields'] = json_encode($fields);
		if ($sortFields) $data['sort'] = json_encode($sortFields);
		if ($skip) $data['skip'] = $skip;
		if ($limit) $data['limit'] = $limit;
		
		$data['batch_size'] = $batch_size;
		
		$result = $this->db->getConnection()->doRequest($path, $data, true);
		
		if ($result['ok'] !== 1)
		{
			throw new \Exception('Update failed');
		}
	}
	
	/**
	 * @param array $a document to insert
	 */
	public function insert(array $a)
	{
		$this->batchInsert(array($a));
	}
	
	/**
	 * @param array $a array of documents to insert
	 */
	public function batchInsert(array $a)
	{
		$path = '/' . $this->db->getName() . '/' . $this->name . '/_insert';
		$data = array('docs' => json_encode($a));
		
		$result = $this->db->getConnection()->doRequest($path, $data);
		
		if ($result['status'] !== 1)
		{
			throw new \Exception('Insert failed: ' . $result['status']['err']);
		}
	}
	
	/**
	 * @param array $a array of documents to insert
	 */
	public function update(array $criteria, array $newData)
	{
		$path = '/' . $this->db->getName() . '/' . $this->name . '/_update';
		$data = array(
			'criteria' => json_encode($criteria), 
			'newobj' => json_encode($newData));
		
		$result = $this->db->getConnection()->doRequest($path, $data);
		
		if ($result['ok'] !== 1)
		{
			throw new \Exception('Update failed');
		}
	}
	
	/**
	 * @param array $criteria
	 */
	public function remove(array $criteria=array())
	{
		$path = '/' . $this->db->getName() . '/' . $this->name . '/_remove';
		$data = array('criteria' => json_encode($criteria));
		
		$result = $this->db->getConnection()->doRequest($path, $data);
		
		if ($result['ok'] !== 1)
		{
			throw new \Exception('Remove failed');
		}
	}
}