<?php

namespace NathanHeffley\LaravelSlackBlocks\Channels;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\Messages\SlackMessage;
use NathanHeffley\LaravelSlackBlocks\Messages\SlackAttachment;
use NathanHeffley\LaravelSlackBlocks\Contracts\SlackBlockContract;
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
     * Build up a JSON payload for the Slack webhook.
     *
     * @param  \Illuminate\Notifications\Messages\SlackMessage  $message
     * @return array
     */
    protected function buildJsonPayload(SlackMessage $message)
    {
        $optionalFields = array_filter([
            'channel' => data_get($message, 'channel'),
            'icon_emoji' => data_get($message, 'icon'),
            'icon_url' => data_get($message, 'image'),
            'link_names' => data_get($message, 'linkNames'),
            'unfurl_links' => data_get($message, 'unfurlLinks'),
            'unfurl_media' => data_get($message, 'unfurlMedia'),
            'username' => data_get($message, 'username'),
        ]);

        $result = array_merge([
            'json' => array_merge([
                'text' => $message->content,
                'blocks' => $this->blocks($message),
                'attachments' => $this->attachments($message),
            ], $optionalFields),
        ], $message->http);

        return $result;
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
     * Format the message's blocks.
     *
     * @param  SlackMessage|SlackAttachment  $message
     * @return array
     */
    protected function blocks($message)
    {
        if (!property_exists($message, 'blocks')) {
            return [];
        }

        return collect($message->blocks)->map(function (SlackBlockContract $value) {
            return $value->toArray();
        })->values()->all();
    }
}
