--TEST--
Test hs_decompress() function : error conditions
--SKIPIF--
--FILE--
<?php
if (!extension_loaded('hs')) {
    dl('hs.' . PHP_SHLIB_SUFFIX);
}

echo "*** Testing hs_decompress() : error conditions ***\n";

// Zero arguments
echo "\n-- Testing hs_decompress() function with Zero arguments --\n";
var_dump( hs_decompress() );

//Test hs_decompress with one more than the expected number of arguments
echo "\n-- Testing hs_decompress() function with more than expected no. of arguments --\n";
$data = 'string_val';
$extra_arg = 10;
var_dump( hs_decompress($data, -1, -1, $extra_arg) );


echo "\n-- Testing with incorrect arguments --\n";
var_dump(hs_decompress(123));

class Tester
{
    function Hello()
    {
        echo "Hello\n";
    }
}

$testclass = new Tester();
var_dump(hs_decompress($testclass));
?>
===DONE===
--EXPECTF--
*** Testing hs_decompress() : error conditions ***

-- Testing hs_decompress() function with Zero arguments --

Warning: hs_decompress() expects at least 1 parameter, 0 given in %s on line %d
bool(false)

-- Testing hs_decompress() function with more than expected no. of arguments --

Warning: hs_decompress() expects at most 3 parameters, 4 given in %s on line %d
bool(false)

-- Testing with incorrect arguments --

Warning: hs_decompress : expects parameter to be string. in %s on line %d
bool(false)

Warning: hs_decompress : expects parameter to be string. in %s on line %d
bool(false)
===DONE===
