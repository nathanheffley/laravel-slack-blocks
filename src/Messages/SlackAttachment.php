<?php

namespace NathanHeffley\LaravelSlackBlocks\Messages;

use Closure;
use Illuminate\Notifications\Messages\SlackAttachment as LaravelSlackAttachment;

class SlackAttachment extends LaravelSlackAttachment
{
    /**
     * The attachment's blocks.
     *
     * @var array
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

    /**
     * Add a divider block to the attachment.
     *
     * @return $this
     */
    public function dividerBlock()
    {
        $this->blocks[] = new SlackAttachmentBlockDivider;

        return $this;
    }

    /**
     * Add an image block to the attachment.
     *
     * @param string $imageUrl
     * @param string $altText
     * @return $this
     */
    public function imageBlock($imageUrl, $altText)
    {
        $this->blocks[] = new SlackAttachmentBlockImage($imageUrl, $altText);

        return $this;
    }
}
