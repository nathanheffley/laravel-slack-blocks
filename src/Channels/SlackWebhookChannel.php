<?php

namespace NathanHeffley\LaravelSlackBlocks\Channels;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\Messages\SlackMessage;
use NathanHeffley\LaravelSlackBlocks\Messages\SlackAttachment;
use NathanHeffley\LaravelSlackBlocks\Messages\SlackAttachmentBlock;
use Illuminate\Notifications\Channels\SlackWebhookChannel as LaravelSlackWebhookChannel;

class SlackWebhookChannel extends LaravelSlackWebhookChannel
{
    /**
     * Create a new Slack channel instance.
     *
     * @param  \GuzzleHttp\Client  $http
     * @return void
     */
    public function __construct(HttpClient $http)
    {
        parent::__construct($http);
    }

    /**
     * Format the message's attachments.
     *
     * @param  \Illuminate\Notifications\Messages\SlackMessage  $message
     * @return array
     */
    protected function attachments(SlackMessage $message)
    {
        return collect($message->attachments)->map(function ($attachment) use ($message) {
            return array_filter([
                'actions' => $attachment->actions,
                'author_icon' => $attachment->authorIcon,
                'author_link' => $attachment->authorLink,
                'author_name' => $attachment->authorName,
                'color' => $attachment->color ?: $message->color(),
                'fallback' => $attachment->fallback,
                'fields' => $this->fields($attachment),
                'blocks' => $this->blocks($attachment),
                'footer' => $attachment->footer,
                'footer_icon' => $attachment->footerIcon,
                'image_url' => $attachment->imageUrl,
                'mrkdwn_in' => $attachment->markdown,
                'pretext' => $attachment->pretext,
                'text' => $attachment->content,
                'thumb_url' => $attachment->thumbUrl,
                'title' => $attachment->title,
                'title_link' => $attachment->url,
                'ts' => $attachment->timestamp,
            ]);
        })->all();
    }

    /**
     * Format the attachment's blocks.
     *
     * @param  \NathanHeffley\LaravelSlackBlocks\Messages\SlackAttachment  $attachment
     * @return array
     */
    protected function blocks(SlackAttachment $attachment)
    {
        return collect($attachment->blocks)->map(function (SlackAttachmentBlock $value) {
            return $value->toArray();
        })->values()->all();
    }
}
