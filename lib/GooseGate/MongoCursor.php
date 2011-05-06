<?php

namespace GooseGate;

class MongoCursor implements \Iterator
{
	/**
	 * @var \GooseGate\MongoCollection
	 */
	protected $collection;
	
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
	protected $documents;
	
	protected $criteria;
	protected $fields;
	protected $sortFields;
	protected $skip;
	protected $limit;
	protected $batchSize = 20;
	
	public function __construct($collection, $criteria=null, $fields=null, $sortFields=null, $skip=null, $limit=null, $batch_size=20)
	{
		if ($criteria) $this->criteria = $criteria; 
		if ($fields) $this->fields = $fields;
		if ($sortFields) $this->sortFields = $sortFields;
		if ($skip) $this->skip = $skip;
		if ($limit) $this->limit = $limit;
		if ($batch_size) $this->batchSize = $batch_size;
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
		
	}
}