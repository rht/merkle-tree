<?php
/**
 * @copyright 2013 Matthew Nagi
 * @license http://opensource.org/licenses/BSD-2-Clause BSD 2-Clause License
 */
declare(strict_types=1);

namespace Rht\Merkle;

use UnexpectedValueException;

/**
 * Builds a merkle tree of a given width
 *
 * This is used to pre-build the total amount of nodes equal to the total
 * hashes that you would need to calculate a merkle tree without having the
 * data that is going to be hashed yet.
 *
 * For example: say you are downloading chunks of a file out of order. If you
 * know the length of the file, and decide on an appropriate chunk size, you
 * would know the merkle tree width ahead of time. You could construct the
 * tree and start adding data components one at a time. Any subtree that could
 * be hashed will be hashed as soon as possible and references to the
 * underlying nodes broken.
 */
class FixedSizeTree
{
    private $tree;
    private $hasher;
    private $finished;
    private $inputIsHash;

    /**
     * @param int $width
     * @param callable $hasher
     * @param callable|null $finished
     * @aparam bool $inputIsHash
     */
    public function __construct(int $width, callable $hasher, callable $finished = null, bool $inputIsHash = false)
    {
        if ($width < 1) {
            throw new UnexpectedValueException('width cannot be less than 1');
        }

        $tree = new GrowableBinaryTree($hasher);
        for ($i = 0; $i < $width; $i++) {
            $tree->addLeafNode();
        }
        $tree->lock();
        $this->tree = $tree;
        $this->hasher = $hasher;
        $this->finished = $finished;
        $this->inputIsHash = $inputIsHash;
    }

    /**
     * @return string|false
     */
    public function hash()
    {
        return $this->tree->root();
    }

    /**
     * @param int $i
     * @param string $v
     * @return null
     */
    public function set(int $i, string $v)
    {
        if ($this->inputIsHash) {
            $leaf = $v;
        } else {
            $leaf = call_user_func($this->hasher, $v);
        }
        $result = $this->tree->set($i, $leaf);
        if ($result === null) {
            return null;
        }
        if ($this->finished !== null) {
            call_user_func($this->finished, $result);
        }
    }
}
