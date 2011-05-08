<?php

namespace GooseGate;

class MongoCursor implements \Iterator, \Countable
{
	/**
	 * @var \GooseGate\MongoCollection
	 */
	protected $collection;
	
	/**
	 * @var \GooseGate\Mongo
	 */
	protected $connection;
	
	/**
	 * @var string
	 */
	protected $cursorId;
	
	/**
	 * @var integer
	 */
	protected $index;
	
	/**
	 * @var array with retrieved documents
	 */
	protected $documents = array();
	
	protected $criteria;
	protected $fields;
	protected $sortFields;
	protected $skip;
	protected $limit;
	protected $batchSize = 20;
	
	public function __construct(MongoCollection $collection, $criteria=null, $fields=null, $sortFields=null, $skip=null, $limit=null, $batch_size=20)
	{
		if ($criteria) $this->criteria = $criteria; 
		if ($fields) $this->fields = $fields;
		if ($sortFields) $this->sortFields = $sortFields;
		if ($skip) $this->skip = $skip;
		if ($limit) $this->limit = $limit;
		if ($batch_size) $this->batchSize = $batch_size;
		
		$this->collection = $collection;
		$this->connection = $collection->getDatabase()->getConnection();
		
		$this->fetch();
	}
	
	protected function fetch()
	{
		$path = '/' . $this->collection->getDatabase()->getName() . '/' . $this->collection->getName() . '/_find';
		$data = array();
		
		if ($this->criteria) $data['criteria'] = json_encode($this->criteria); 
		if ($this->fields) $data['fields'] = json_encode($this->fields);
		if ($this->sortFields) $data['sort'] = json_encode($this->sortFields);
		if ($this->skip) $data['skip'] = $this->skip;
		if ($this->limit) $data['limit'] = $this->limit;
		
		$data['batch_size'] = $this->batchSize;
		
		$result = $this->connection->doGet($path, $data);
		
		$this->cursorId = $result->id;
		array_splice($this->documents, count($this->documents), 0, $result->results);
		
		return count($result->results);
	}
	
	protected function fetchMore()
	{
		$path = '/' . $this->collection->getDatabase()->getName() . '/' . $this->collection->getName() . '/_more';
		$data = array(
			'batch_size' => $this->batchSize,
			'id' => $this->cursorId
		);
		
		$result = $this->connection->doGet($path, $data);
		array_splice($this->documents, count($this->documents), 0, $result->results);
				
		return count($result->results);
	}
	
	protected function fetchAll()
	{
		while (true)
		{
			$count = $this->fetchMore();
			if ($count === 0) break;
		}
	}
	
	public function rewind()
	{
		$this->index = 0;
	}
	
	public function key()
	{
		return $this->index;
	}
	
	public function current()
	{
		return $this->documents[$this->index];
	}
	
	public function next()
	{
		++$this->index;	
	}
	
	public function valid()
	{
			while (!isset($this->documents[$this->index]))
			{
				$count = $this->fetchMore();
				if ($count === 0) return false;
			}
			
			return true;
	}
	
	public function count()
	{
		$this->fetchAll();
		return count($this->documents);
	}
}