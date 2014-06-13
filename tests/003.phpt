--TEST--
Test hs_compress() function : variation
--SKIPIF--
--FILE--
<?php
if (!extension_loaded('hs')) {
    dl('hs.' . PHP_SHLIB_SUFFIX);
}

include(dirname(__FILE__) . '/data.inc');

echo "*** Testing hs_compress() : variation ***\n";

echo "\n-- Testing multiple compression --\n";
$output = hs_compress($data);
var_dump( md5($output));
var_dump(md5(hs_compress($output)));

?>
===Done===
--EXPECTF--
*** Testing hs_compress() : variation ***

-- Testing multiple compression --
string(32) "14bbb34b7d3092f7f1ac35236e97c718"
string(32) "2fa425eb0a85d06d3366cd32fd7c8f53"
===Done===
