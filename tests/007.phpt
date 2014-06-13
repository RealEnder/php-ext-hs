--TEST--
Test phpinfo() displays hs info
--SKIPIF--
--FILE--
<?php
if (!extension_loaded('hs')) {
    dl('hs.' . PHP_SHLIB_SUFFIX);
}

phpinfo();
--EXPECTF--
%a
hs

heatshrink support => enabled
Extension Version => %d.%d.%d
%a
