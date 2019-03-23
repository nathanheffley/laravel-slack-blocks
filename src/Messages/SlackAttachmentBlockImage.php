<?php

namespace NathanHeffley\LaravelSlackBlocks\Messages;

use NathanHeffley\LaravelSlackBlocks\Contracts\SlackAttachmentBlockContract;

class SlackAttachmentBlockImage implements SlackAttachmentBlockContract
{
    /**
     * The image's URL.
     *
     * @var string
     */
    protected $imageUrl;

    /**
     * The image's alt text.
     *
     * @var string
     */
    protected $altText;

    /**
     * Create a new block image.
     *
     * @param string $imageUrl
     * @param string $altText
     */
    public function __construct($imageUrl, $altText)
    {
        $this->imageUrl = $imageUrl;
        $this->altText = $altText;
    }

    /**
     * Get the array representation of the attachment block.
     *
     * @return array
     */
    public function toArray()
    {
        return array_filter([
            'type' => 'image',
            'image_url' => $this->imageUrl,
            'alt_text' => $this->altText,
        ]);
    }
}
