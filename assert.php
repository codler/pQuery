<?php
require_once('core.php');

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
#echo print_r($a["3:-1"]->get) ;
#echo print_r($a["3:-3"]->get) ;
assert($a["1:3"]->get == array("c1","a","b2"));
assert($a["-3:3"]->get == array("a","b2"));
assert($a["3:-1"]->get == array("b2","c2"));
assert($a["3:-3"]->get == array("a","b2"));
#echo $a->methods;
?>