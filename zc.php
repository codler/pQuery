<?php
/**
 * pQuery
 * 
 * Makes PHP more powerful, inspired from Jquery, Python and Ruby!
 * 
 * Features
 * - Chain calls
 * - Better array functions
 * - Negative list arrays - $array[-1]
 * - more
 *
 * Code standard -> {@link http://codex.wordpress.org/WordPress_Coding_Standards Wordpress_Coding_Standards}
 * Documentation generator -> {@link http://www.phpdoc.org/ phpDocumentor} 
 *
 * @author Han Lin Yap
 * @copyright Copyright (c) 2010, Han Lin Yap
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @link http://www.zencodez.net/
 * @package pQuery
 * @version 0.1 - 2010-08-07
 */


function s($s) {
	return new s($s);
}

function a($a) {
	return new a($a);
}

function i($i) {
	return new i($i);
}

// string
class s extends zc_base {
	var $s;
	function __construct($s) {
		$this->s = $this->to_s($s, false);
	}
	
	function __toString() {
		return $this->s;
	}
}

// array
class a extends zc_base implements arrayaccess, IteratorAggregate {
	var $a;
	function __construct($a) {
		$this->a = $this->to_a($a, false);		
	}
	
	function __toString() {
		return print_r($this->a, true);
	}
	
	function add_first($s) {
		array_unshift($this->a, $s);
		
		return $this;
	}
	
	// implements arrayaccess
	function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->a[] = $value;
		} else {
			$this->a[$offset] = $value;
		}
	}
	
	// implements arrayaccess
	function offsetExists($offset) {
		return isset($this->a[$offset]);
	}
	
	// implements arrayaccess
	function offsetUnset($offset) {
		unset($this->a[$offset]);
	}
	
	// implements arrayaccess
	function offsetGet($offset) {
		return $this->item($offset);
	}
	
	// implements IteratorAggregate
	function getIterator() {
		$a = $this->a;
		foreach($a AS $k => $v) {
			$a[$k] = $this->zc_type($v);
		}
	
		return new ArrayIterator($a);
	}
	
	// slice selector
	function item($expr) {
		if (is_numeric($expr)) {
			if ($this->is_i($expr)) {
				if ($expr<0)
					$expr = $this->length($this->a) + $expr;
			}
			return isset($this->a[$expr]) ? $this->zc_type($this->a[$expr]) : null;
		} else {
			$part = explode(':', $expr);
			$start = intval($part[0]);
			$end = intval($part[1]);
			
			if ($end > count($this->a)) {
				$sliced = array_slice($this->a, $start);
			} elseif ($end<0) {
				$end = $this->length($this->a) + $end;
				if ($start<0) 
					$start = $this->length($this->a) + $start;
				
				if ($end > $start) {
				
					$sliced = array_slice($this->a, $start, $end - $start + 1);
				} else 					
					$sliced = array_slice($this->a, $end, $start - $end + 1);
				
			} else {
				if ($start<0) 
					$start = $this->length($this->a) + $start;
					
				$sliced = array_slice($this->a, $start, $end - $start + 1);
			
			}
			return a($sliced);
		}
	}
	
	// alias of add()
	function add_last($s) {
		return $this->add($s);
	}
	
	function add($s) {
		$this->a[] = $s;
		
		return $this;
	}
	
	function append($a) {
		if ($this->is_a($a)) {
			foreach($a AS $v) {
				$this->add($v);
			}
		} else {
			$this->add($a);
		}
		
		return $this;
	}
	
	function prepend($a) {
		if ($this->is_a($a)) {
			foreach(array_reverse($a) AS $v) {
				$this->add_first($v);
			}
		} else {
			$this->add_first($a);
		}
		
		return $this;
	}

	function remove_first( & $out=false) {
		$out = array_shift($this->a);
		
		return $this;
	}
	
	function remove_last( & $out=false) {
		return $this->remove($out);
	}
	
	function remove( & $out=false) {
		$out = array_pop($this->a);
		
		return $this;
	}
}

// integer
class i extends zc_base {
	var $i;
	function __construct($i) {
		$this->i = $this->to_i($i, false);
	}
	
	function __toString() {
		return (string)$this->i;
	}
}

class zc_base {

	function __get($prop) {
		$list_prop = array('is_a','is_i','is_s','get','length','methods','to_a','to_i','to_s');
		if (in_array($prop, $list_prop)) {
			return $this->$prop($this->{get_class($this)});
		} else {
			return null;
		}		
	}

	// is normal array?
	function _is_aa($a) {
		return is_array($a);
	}
	// is object array?
	function _is_ao($a) {
		return (is_object($a) && get_class($a) == 'a');
	}
	
	function is_a($a) {
		return ($this->_is_aa($a) || $this->_is_ao($a));
		#return (isset($this->a)) ? ($this->_is_aa($a) || $this->_is_ao($a)) : false;
	}
	
	// is normal float?
	function _is_ii($i) {
		return is_float($i) || is_int($i);
	}
	// is object float?
	function _is_io($i) {
		return (is_object($i) && get_class($i) == 'i');
	}
	
	function is_i($i) {
		return ($this->_is_ii($i) || $this->_is_io($i));
	}
	
	// is normal string?
	function _is_ss($s) {
		return is_string($s);
	}
	// is object string?
	function _is_so($s) {
		return (is_object($s) && get_class($s) == 's');
	}
	
	function is_s($s) {
		return ($this->_is_ss($s) || $this->_is_so($s));
	}
	
	function get() {
		switch (get_class($this)) {
			case 'a' :
				return (array)$this->a;
				break;
			case 'i' :
				return (double)$this->i;
				break;
			case 's' :
				return (string)$this->s;
				break;
		}
	}
	
	function length($v) {
		$func = (get_class($this)=='a') ? 'count' : 'strlen';
		return $func($v);
	}

	function methods() {
		return a(get_class_methods($this));
	}
	
	function to_a($a, $obj=true) {
		return ($obj) ? a((array)$a) : (array)$a;
	}
	
	function to_i($i, $obj=true) {
		return ($obj) ? i((double)$i) : (double)$i;
	}
	
	function to_s($s, $obj=true) {
		if ($this->_is_aa($s))
			$s = $this->to_a($s);
		return ($obj) ? s((string)$s) : (string)$s;
	}
	
	// Make to a zc type (a,i or s)
	function zc_type($v) {
		if ($this->_is_aa($v)) {
			return a($v);
		} elseif ($this->_is_ii($v)) {
			return i($v);
		} elseif ($this->_is_ss($v)) {
			return s($v);
		}
		return $v;
	}
}


// Assert
assert_options(ASSERT_BAIL, true);

// String assert
assert(s("string") == "string");
assert(s("string")->to_a == a(array("string")));
assert(s("string")->to_a->to_s == print_r(array("string"), true));
assert(s("string")->to_i == i(0));
assert(s("string")->to_i == i("0"));
assert(s("string")->to_i == "0");
assert(s("string")->to_i->get == 0);
assert(s("string")->length == 6);

// Integer assert
assert(i(10) == "10");
assert(i(10) == i("10"));
assert(i(10)->get == 10);
assert(i(10)->to_s == "10");
assert(i(10)->to_a == a(10));
assert(i(10)->to_a->to_i == 1);
assert(i(10)->length == 2);

// Array assert
assert(a("a")->get == array("a"));
assert(a("a") == print_r(array("a"), true));
assert(a(array("a")) == print_r(array("a"), true));
assert(a(array(array("a"))) == print_r(array(array("a")), true));
$a = a("a");
assert($a->add_first("a1") == print_r(array("a1","a"), true));
assert($a->add_last("a2") == print_r(array("a1","a","a2"), true));
// alias of add_last
assert($a->add("a3") == print_r(array("a1","a","a2","a3"), true));
$b = clone $a;
$c = clone $a;
assert($b->add(array("a4",$a)) == print_r(array("a1","a","a2","a3",array("a4", a(array("a1","a","a2","a3")))), true));
assert($c->add(array("a4",$a)) == print_r(array("a1","a","a2","a3",array("a4", $a)), true));
unset($b);
unset($c);
assert($a->remove_first()->remove_last() == print_r(array("a","a2"), true));
assert($a->length == 2);
// alias of remove_last
$a->remove($r); // param ref of deleted object
assert($r == "a2");
// adds several in an array
assert($a->prepend(array(a("b1"), "c1")) == print_r(array(a("b1"), "c1","a"), true));
assert($a->append(array("b2","c2")) == print_r(array(a("b1"), "c1","a","b2","c2"), true));

foreach($a AS $k => $v) {
	if ($v->is_a) {
		assert($v[0] == "b1");
		assert($v[0] == s("b1"));
		assert($v->to_s == print_r(array("b1"), true));
	}
}
echo print_r($a["3:-1"]->get) ;
echo print_r($a["3:-3"]->get) ;
assert($a["1:3"]->get == array("c1","a","b2"));
assert($a["-3:3"]->get == array("a","b2"));
assert($a["3:-1"]->get == array("b2","c2"));
assert($a["3:-3"]->get == array("a","b2"));
echo $a->methods;

?>