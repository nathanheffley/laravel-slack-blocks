<?php

namespace NathanHeffley\LaravelSlackBlocks\Messages;

use Closure;
use Illuminate\Notifications\Messages\SlackAttachment as LaravelSlackAttachment;

class SlackAttachment extends LaravelSlackAttachment
{
    /**
     * The attachment's blocks.
     */
    public $blocks;

    /**
     * Add a block to the attachment.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function block(Closure $callback)
    {
        $this->blocks[] = $block = new SlackAttachmentBlock;

        $callback($block);

        return $this;
    }
}
