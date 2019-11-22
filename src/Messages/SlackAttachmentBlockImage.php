<?php

namespace NathanHeffley\LaravelSlackBlocks\Messages;

use NathanHeffley\LaravelSlackBlocks\Contracts\SlackBlockContract;

class SlackAttachmentBlockImage implements SlackBlockContract
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
     * The image's title.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Create a new block image.
     *
     * @param string $imageUrl
     * @param string $altText
     * @param string|null
     */
    public function __construct($imageUrl, $altText, $title)
    {
        $this->imageUrl = $imageUrl;
        $this->altText = $altText;
        $this->title = $title;
    }

    /**
     * Get the array representation of the attachment block.
     *
     * @return array
     */
    public function toArray()
    {
        $titleData = $this->title ? [
            'type' => 'plain_text',
            'text' => $this->title,
        ] : null;

        return array_filter([
            'type' => 'image',
            'title' => $titleData,
            'image_url' => $this->imageUrl,
            'alt_text' => $this->altText,
        ]);
    }
}
