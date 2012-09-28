<?php

class Acp_Bib_Collection implements Countable, Iterator {
	public function __construct() {
		$this->clear();
	}
	
	public function clear() {
		$this->data = array();
	}
	
	public function add($args) {
		$this->data[$this->getHash($args)] = $args;
		return $this;
	}
	
	public function remove($args) {
		unset($this->data[$this->getHash($args)]);
		return $this;
	}
	
	public function sortBy($attributes) {
		Acp::loadClass('Acp_Utils_Spl');
		$this->data = Acp_Utils_Spl::array_multisort($this->data, $attributes);
		return $this;
	}
	
	public function find($args, $target = null) {
		$o = $target!=null ? $target : new Acp_Bib_Collection();
		foreach ($this as $d) {
			foreach ($args as $k=>$v)
				if (!isset($d[$k]) || $d[$k] != $v )
					continue 2;
			$o->add($d);
		}
		return $o;
	}
	
	private function getHash($args) {
		if (isset($args['id']))
			return $args['id'];
		else if (isset($args['reference']))
			return $args['reference'];
		else 
			return md5(array_merge($args));
	}
	
	/* Set operations */
	public function union($collection, $target = null) {		
		$o = $target!=null ? $target : new Acp_Bib_Collection();
		$o->data = array_merge($this->data, $collection->data);
		return $o;
	}
	
	public function diff($collection, $target = null) {
		$o = $target!=null ? $target : new Acp_Bib_Collection();
		$o->data = array_diff_key($this->data, $collection->data);
		return $o;
	}
	
	public function intersect($collection, $target = null) {
		$o = $target!=null ? $target : new Acp_Bib_Collection();
		$o->data = array_intersect_key($this->data, $collection->data);
		return $o;
	}
	
	public function copy($target = null) {
		$o = $target!=null ? $target : new Acp_Bib_Collection();
		$o->data = $this->data;
		return $o;
	}
	
	/* Networking operations */
	
	/* Convert operations */
	public function asString() {
		$s = array();
		foreach ($this as $o) {
			$t = array();
			foreach ($o as $k=>$v)
				$t[] = " $k=\"$v\""; 
			$s[] = '['. implode(',', $t) .' ]';
		}
		return '['. implode(',', $s) .']';
	}
	
	/* Countable */
	public function count() {
		return sizeof($this->data);
	}
	
	/* Iterator */
	function rewind() {
		reset($this->data);
	}
	
	function current() {
		return current($this->data);
	}
	
	function key() {
		return key($this->data);
	}
	
	function next() {
		next($this->data);
	}
	
	function valid() {
		if (current($this->data) == false)
			return false;
		//prev($this->data);
		return true;
	}
	
	private $data;
}