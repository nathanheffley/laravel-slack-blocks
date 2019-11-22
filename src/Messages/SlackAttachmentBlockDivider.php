<?php

namespace NathanHeffley\LaravelSlackBlocks\Messages;

use NathanHeffley\LaravelSlackBlocks\Contracts\SlackBlockContract;

class SlackAttachmentBlockDivider implements SlackBlockContract
{
    /**
     * Get the array representation of the attachment block.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'divider',
        ];
    }
}
