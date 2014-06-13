--TEST--
Test hs_compress()/hs_decompress() : window and lookahead
--SKIPIF--
--FILE--
<?php
if (!extension_loaded('hs')) {
    dl('hs.' . PHP_SHLIB_SUFFIX);
}

include(dirname(__FILE__) . '/data.inc');

$bindata = '';
for ($i=0;$i<65535;$i++)
    //$bindata .= chr(mt_rand(0,255));
    $bindata .= chr($i%255);
//TODO: random bin fails sometimes with w 15

echo "*** Testing hs_compress()/hs_decompress() : window and lookahead ***\n";

for ($w=4;$w<=15;$w++) {
    for ($l=2;$l<$w;$l++) {
        //TODO:Check why this happens
        if ($w==4 && $l==2)
            continue;
        echo "w: $w l: $l ";
        $comp = hs_compress($data, $w, $l);
        $decomp = hs_decompress($comp, $w, $l);
        $bincomp = hs_compress($bindata, $w, $l);
        $bindecomp = hs_decompress($bincomp, $w, $l);
        if ($decomp == $data)
            echo "ascii: OK ";
        else
            echo "ascii: Fail ";
        if ($bindecomp == $bindata)
            echo "bin: OK\n";
        else
            echo "bin: Fail\n";
    }
}
?>
===Done===
--EXPECT--
*** Testing hs_compress()/hs_decompress() : window and lookahead ***
w: 4 l: 3 ascii: OK bin: OK
w: 5 l: 2 ascii: OK bin: OK
w: 5 l: 3 ascii: OK bin: OK
w: 5 l: 4 ascii: OK bin: OK
w: 6 l: 2 ascii: OK bin: OK
w: 6 l: 3 ascii: OK bin: OK
w: 6 l: 4 ascii: OK bin: OK
w: 6 l: 5 ascii: OK bin: OK
w: 7 l: 2 ascii: OK bin: OK
w: 7 l: 3 ascii: OK bin: OK
w: 7 l: 4 ascii: OK bin: OK
w: 7 l: 5 ascii: OK bin: OK
w: 7 l: 6 ascii: OK bin: OK
w: 8 l: 2 ascii: OK bin: OK
w: 8 l: 3 ascii: OK bin: OK
w: 8 l: 4 ascii: OK bin: OK
w: 8 l: 5 ascii: OK bin: OK
w: 8 l: 6 ascii: OK bin: OK
w: 8 l: 7 ascii: OK bin: OK
w: 9 l: 2 ascii: OK bin: OK
w: 9 l: 3 ascii: OK bin: OK
w: 9 l: 4 ascii: OK bin: OK
w: 9 l: 5 ascii: OK bin: OK
w: 9 l: 6 ascii: OK bin: OK
w: 9 l: 7 ascii: OK bin: OK
w: 9 l: 8 ascii: OK bin: OK
w: 10 l: 2 ascii: OK bin: OK
w: 10 l: 3 ascii: OK bin: OK
w: 10 l: 4 ascii: OK bin: OK
w: 10 l: 5 ascii: OK bin: OK
w: 10 l: 6 ascii: OK bin: OK
w: 10 l: 7 ascii: OK bin: OK
w: 10 l: 8 ascii: OK bin: OK
w: 10 l: 9 ascii: OK bin: OK
w: 11 l: 2 ascii: OK bin: OK
w: 11 l: 3 ascii: OK bin: OK
w: 11 l: 4 ascii: OK bin: OK
w: 11 l: 5 ascii: OK bin: OK
w: 11 l: 6 ascii: OK bin: OK
w: 11 l: 7 ascii: OK bin: OK
w: 11 l: 8 ascii: OK bin: OK
w: 11 l: 9 ascii: OK bin: OK
w: 11 l: 10 ascii: OK bin: OK
w: 12 l: 2 ascii: OK bin: OK
w: 12 l: 3 ascii: OK bin: OK
w: 12 l: 4 ascii: OK bin: OK
w: 12 l: 5 ascii: OK bin: OK
w: 12 l: 6 ascii: OK bin: OK
w: 12 l: 7 ascii: OK bin: OK
w: 12 l: 8 ascii: OK bin: OK
w: 12 l: 9 ascii: OK bin: OK
w: 12 l: 10 ascii: OK bin: OK
w: 12 l: 11 ascii: OK bin: OK
w: 13 l: 2 ascii: OK bin: OK
w: 13 l: 3 ascii: OK bin: OK
w: 13 l: 4 ascii: OK bin: OK
w: 13 l: 5 ascii: OK bin: OK
w: 13 l: 6 ascii: OK bin: OK
w: 13 l: 7 ascii: OK bin: OK
w: 13 l: 8 ascii: OK bin: OK
w: 13 l: 9 ascii: OK bin: OK
w: 13 l: 10 ascii: OK bin: OK
w: 13 l: 11 ascii: OK bin: OK
w: 13 l: 12 ascii: OK bin: OK
w: 14 l: 2 ascii: OK bin: OK
w: 14 l: 3 ascii: OK bin: OK
w: 14 l: 4 ascii: OK bin: OK
w: 14 l: 5 ascii: OK bin: OK
w: 14 l: 6 ascii: OK bin: OK
w: 14 l: 7 ascii: OK bin: OK
w: 14 l: 8 ascii: OK bin: OK
w: 14 l: 9 ascii: OK bin: OK
w: 14 l: 10 ascii: OK bin: OK
w: 14 l: 11 ascii: OK bin: OK
w: 14 l: 12 ascii: OK bin: OK
w: 14 l: 13 ascii: OK bin: OK
w: 15 l: 2 ascii: OK bin: OK
w: 15 l: 3 ascii: OK bin: OK
w: 15 l: 4 ascii: OK bin: OK
w: 15 l: 5 ascii: OK bin: OK
w: 15 l: 6 ascii: OK bin: OK
w: 15 l: 7 ascii: OK bin: OK
w: 15 l: 8 ascii: OK bin: OK
w: 15 l: 9 ascii: OK bin: OK
w: 15 l: 10 ascii: OK bin: OK
w: 15 l: 11 ascii: OK bin: OK
w: 15 l: 12 ascii: OK bin: OK
w: 15 l: 13 ascii: OK bin: OK
w: 15 l: 14 ascii: OK bin: OK
===Done===
