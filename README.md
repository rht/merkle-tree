# Merkle Tree Implementation #

[![Build Status](https://travis-ci.org/pleonasm/merkle-tree.png?branch=master)](https://travis-ci.org/pleonasm/merkle-tree)
[![Coverage Status](https://coveralls.io/repos/github/pleonasm/merkle-tree/badge.svg?branch=master)](https://coveralls.io/github/pleonasm/merkle-tree?branch=master)

This is an implementation of a hash tree or [Merkle Tree](http://en.wikipedia.org/wiki/Merkle_Tree)
for PHP. 

## Install ##

Install via [Composer](http://getcomposer.org) (make sure you have composer in your path or in your project).

Put the following in your package.json:

```javascript
{
    "require": {
        "rht/merkle-tree": "*"
    }
}
```

Then run `composer install`.

## Usage For A Fixed Size Tree ##

Usage of a hash tree requires a user to provide a hashing function that each
node will use to do its hashing. This function must accept a single string
argument and must always return a string. It should never throw an exception.

If desired, you can also provide a callback for when the entire hash tree has
been actually finished, rather than checking for it each time yourself.

```php
<?php
use Rht\Merkle\FixedSizeTree;

require 'vendor/autoload.php';

// basically the same thing bitcoin merkle tree hashing does
$hasher = function ($data) {
    return hash('sha256', hash('sha256', $data, true), true);
};

$finished = function ($hash) {
    echo implode('', unpack('H*', $hash)) . "\n";
};

$tree = new FixedSizeTree(16, $hasher, $finished);

$tree->set(0, 'Science');
$tree->set(1, 'is');
$tree->set(2, 'made');
$tree->set(3, 'up');
$tree->set(4, 'of');
$tree->set(5, 'so');
$tree->set(6, 'many');
$tree->set(7, 'things');
$tree->set(8, 'that');
$tree->set(9, 'appear');
$tree->set(10, 'obvious');
$tree->set(11, 'after');
$tree->set(12, 'they');
$tree->set(13, 'are');
$tree->set(14, 'explained');
$tree->set(15, '.'); // this will echo the string 'c689102cdf2a5b30c2e21fdad85e4bb401085227aff672a7240ceb3410ff1fb6'
```

This fork of https://github.com/pleonasm/merkle-tree has a FixedSizeTree which implements a Merkle Patricia Tree the [same way Ethereum does](https://eth.wiki/fundamentals/patricia-tree). While pleonasm/merkle-tree implements it in the [same way bitcoins do](https://en.bitcoin.it/wiki/Protocol_specification#Merkle_Trees).
There are [other ways](http://web.archive.org/web/20080316033726/http://www.open-content.net/specs/draft-jchapweske-thex-02.html)
to actually deal with a tree width that is not a perfect square.

If there is a need for the other method, I would not be opposed to adding it.

## Upgrading from 1.0 ##

The difference between 1.0 and 2.0 from the outside should be very minimal. If
you're only using the public interface of FixedSizeTree minus a few error
handling changes, then you should be good to go.

The new implementation is greatly simplified and is **far** faster for large
trees. My latest benchmark put 1.0 taking 100 seconds to fill a 10000 item tree
while 2.0 takes 0.2 seconds.

That said, it is technically a backwards incompatible change so a major version
bump is in order.

## License ##

You can find the license for this code in the [LICENSE file](LICENSE)
