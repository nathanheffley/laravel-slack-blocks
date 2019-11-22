<?php

namespace NathanHeffley\LaravelSlackBlocks\Contracts;

interface SlackBlockContract
{
    /**
     * Get the array representation of the block.
     *
     * @return array
     */
    public function toArray();
}
