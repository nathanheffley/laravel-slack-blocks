<?php

namespace NathanHeffley\LaravelSlackBlocks\Messages;

use NathanHeffley\LaravelSlackBlocks\Contracts\SlackAttachmentBlockContract;

class SlackAttachmentBlockDivider implements SlackAttachmentBlockContract
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
