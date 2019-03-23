<?php

namespace NathanHeffley\LaravelSlackBlocks\Contracts;

interface SlackAttachmentBlockContract
{
    /**
     * Get the array representation of the attachment block.
     *
     * @return array
     */
    public function toArray();
}