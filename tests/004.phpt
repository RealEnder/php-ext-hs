--TEST--
Test hs_decompress() function : basic functionality
--SKIPIF--
--FILE--
<?php
if (!extension_loaded('hs')) {
    dl('hs.' . PHP_SHLIB_SUFFIX);
}

include(dirname(__FILE__) . '/data.inc');

echo "*** Testing hs_decompress() : basic functionality ***\n";

// Initialise all required variables
$compressed = hs_compress($data);

echo "\n-- Basic decompress --\n";
var_dump(strcmp($data, hs_decompress($compressed)));
?>
===DONE===
--EXPECT--
*** Testing hs_decompress() : basic functionality ***

-- Basic decompress --
int(0)
===DONE===
