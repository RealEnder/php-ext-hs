--TEST--
Test hs_compress() function : basic functionality
--SKIPIF--
--FILE--
<?php 
if (!extension_loaded('hs')) {
    dl('hs.' . PHP_SHLIB_SUFFIX);
}

include(dirname(__FILE__) . '/data.inc');

echo "*** Testing hs_compress() : basic functionality ***\n";

// Initialise all required variables

$smallstring = "A small string to compress\n";


// Calling gzcompress() with all possible arguments

// Compressing a big string
echo "-- Compression --\n";
$output = hs_compress($data);
var_dump(md5($output));
var_dump(strcmp(hs_decompress($output), $data));

// Compressing a smaller string
echo "-- Compression --\n";
$output = hs_compress($smallstring);
var_dump(bin2hex($output));
var_dump(strcmp(hs_decompress($output), $smallstring));

// Calling hs_compress() with mandatory arguments
echo "\n-- Testing with no specified window and lookahead --\n";
var_dump( bin2hex(hs_compress($smallstring) ));

?>
===Done===
--EXPECT--
*** Testing hs_compress() : basic functionality ***
-- Compression --
string(32) "14bbb34b7d3092f7f1ac35236e97c718"
int(0)
-- Compression --
string(62) "a0c82e76db0db2d920b9dd2e569b759e4174b7c82c76fb6dc2e565b9dce140"
int(0)

-- Testing with no specified window and lookahead --
string(62) "a0c82e76db0db2d920b9dd2e569b759e4174b7c82c76fb6dc2e565b9dce140"
===Done===
