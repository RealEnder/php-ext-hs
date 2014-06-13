# heatshrink Extension for PHP #

This extension allows heatshrink compression/decompression in PHP.

Heatshrink is data compression/decompression library for embedded/real-time systems [» https://github.com/atomicobject/heatshrink](https://github.com/atomicobject/heatshrink).

## Build ##

    % phpize
    % ./configure
    % make
    % make install

## Configration ##

hs.ini:

    extension=hs.so

## Function ##

* hs\_compress — heatshrink compression
* hs\_decompress — heatshrink decompression

### hs\_compress — heatshrink compression ###

#### Description ####

string **hs\_compress** ( string _$data_ [ , int _$window_ = 8 , int _$lookahead_ = 4 ] )

heatshrink compression. You must use same window and lookahead values for compression/decompression.

#### Pameters ####

* _data_

  The string to compress.

* _window_

  2^n size of window

* _lookahead_

  2^n size of lookahead

#### Return Values ####

Returns the compressed data or FALSE if an error occurred.


### hs\_uncompress — heatshrink decompression ###

#### Description ####

string **hs\_decompress** ( string _$data_ [ , int _$window_ = 8 , int _$lookahead_ = 4 ] )

heatshrink decompression. You must use same window and lookahead values for compression/decompression.

#### Pameters ####

* _data_

  The string to compress.

* _window_

  2^n size of window

* _lookahead_

  2^n size of lookahead

#### Return Values ####

Returns the decompressed data or FALSE if an error occurred.

## Examples ##

    $data = hs_compress('test');

    hs_decompress($data);

## Authors ##

Alex Stanev, Plamen Baruh, 2004

heatshrink by Scott Vokes

extension code based on [» https://github.com/kjdev/php-ext-lz4](https://github.com/kjdev/php-ext-lz4)