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
	
	/**
	 * @return \GooseGate\MongoDB
	 */
	public function getDatabase()
	{
		return $this->db;
	}
	
	public function find($criteria=null, $fields=null, $sortFields=null, $skip=null, $limit=null, $batch_size=20)
	{
		return new MongoCursor($this, $criteria, $fields, $sortFields, $skip, $limit, $batch_size);
	}
	
	/**
	 * @param array $a document to insert
	 */
	public function insert(array $a)
	{
		$oids = $this->batchInsert(array($a));
		return $oids[0];
	}
	
	/**
	 * @param array $a array of documents to insert
	 * @retun array list of oids of the inserted documents
	 */
	public function batchInsert(array $a)
	{
		$path = '/' . $this->db->getName() . '/' . $this->name . '/_insert';
		$data = array('docs' => json_encode($a));
		
		$result = $this->db->getConnection()->doPost($path, $data);
		
		if (!(property_exists($result, 'oids') && count($result->oids) === count($a)))
		{
			throw new \Exception('Insert failed.');
		}
		
		$oids = array();
		
		foreach ($result->oids as $oid) 
		{
			$oids[] = $oid; 
		}
		
		return $oids;
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
		
		$result = $this->db->getConnection()->doPost($path, $data);
	}
	
	/**
	 * @param array $criteria
	 */
	public function remove(array $criteria=array())
	{
		$path = '/' . $this->db->getName() . '/' . $this->name . '/_remove';
		$data = array('criteria' => json_encode($criteria));
		
		$result = $this->db->getConnection()->doPost($path, $data);
	}
}