PHP Base85
==========

A pure PHP extension to encode and decode data with [base85](http://en.wikipedia.org/wiki/Ascii85).

Usage:
------
```PHP
require("/path/to/base85.class.php");

$encoded = base85::encode('Base85 is pretty cool');
$decoded = base85::decode(':e4D*;K$&\Er');

print "Encoded: $encoded\n";
print "Decoded: $decoded\n";
```

Should output:

```
Encoded: 6=FqH3&MgmF!,FBATW$>+Cf>.C]
Decoded: PHP Rocks
```

**Note:** There is a PHP extension written in C also named [php-base85](https://github.com/raducu/php-base85).
That library is most likely faster, but may be harder to install (requires
the ability to load extensions).
