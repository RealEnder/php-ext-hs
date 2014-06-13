--TEST--
Test hs_compress() function : error conditions
--SKIPIF--
--FILE--
<?php 
if (!extension_loaded('hs')) {
    dl('hs.' . PHP_SHLIB_SUFFIX);
}

include(dirname(__FILE__) . '/data.inc');

echo "*** Testing hs_compress() : error conditions ***\n";

// Zero arguments
echo "\n-- Testing hs_compress() function with Zero arguments --\n";
var_dump(hs_compress());

//Test hs_compress with one more than the expected number of arguments
echo "\n-- Testing hs_compress() function with more than expected no. of arguments --\n";
$data = 'string_val';
$extra_arg = 10;
var_dump(hs_compress($data, false, false, $extra_arg));

class Tester {
    function Hello() {
        echo "Hello\n";
    }
}

echo "\n-- Testing with incorrect parameters --\n";
$testclass = new Tester();
var_dump(hs_compress($testclass));
?>
===Done===
--EXPECTF--
*** Testing hs_compress() : error conditions ***

-- Testing hs_compress() function with Zero arguments --

Warning: hs_compress() expects at least 1 parameter, 0 given in %s on line %d
bool(false)

-- Testing hs_compress() function with more than expected no. of arguments --

Warning: hs_compress() expects at most 3 parameters, 4 given in %s on line %d
bool(false)

-- Testing with incorrect parameters --

Warning: hs_compress : expects parameter to be string. in %s on line %d
bool(false)
===Done===
