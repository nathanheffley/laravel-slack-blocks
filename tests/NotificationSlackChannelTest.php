<?php

namespace NathanHeffley\Tests\LaravelSlackBlocks;

use Mockery as m;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use NathanHeffley\LaravelSlackBlocks\Messages\SlackMessage;
use NathanHeffley\LaravelSlackBlocks\Channels\SlackWebhookChannel;

class NotificationSlackChannelTest extends TestCase
{
    /**
     * @var \NathanHeffley\LaravelSlackBlocks\Channels\SlackWebhookChannel
     */
    private $slackChannel;

    /**
     * @var \Mockery\MockInterface|\GuzzleHttp\Client
     */
    private $guzzleHttp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guzzleHttp = m::mock(Client::class);

        $this->slackChannel = new SlackWebhookChannel($this->guzzleHttp);
    }

    public function tearDown(): void
    {
        m::close();
    }

    /**
     * @dataProvider payloadDataProvider
     * @param \Illuminate\Notifications\Notification $notification
     * @param array $payload
     */
    public function testCorrectPayloadIsSentToSlack(Notification $notification, array $payload)
    {
        $this->guzzleHttp->shouldReceive('post')->andReturnUsing(function ($argUrl, $argPayload) use ($payload) {
            $this->assertEquals('url', $argUrl);
            $this->assertEquals($payload, $argPayload);

            return new Response();
        });

        $this->slackChannel->send(new NotificationSlackChannelTestNotifiable, $notification);
    }

    public function payloadDataProvider()
    {
        return [
            'payloadWithAttachmentBlockBuilder' => $this->getPayloadWithMessageAndAttachmentBlockBuilder(),
            'payloadWithAttachmentSpecialtyBlockBuilder' => $this->getPayloadWithAttachmentSpecialtyBlockBuilder(),
            'payloadWithBaseSlackMessage' => $this->getPayloadWithBaseSlackMessage(),
        ];
    }

    public function getPayloadWithMessageAndAttachmentBlockBuilder()
    {
        $blocks = [
            [
                'type' => 'section',
                'text' => [
                    'type' => 'plain_text',
                    'text' => 'Laravel',
                ],
                'block_id' => 'block-one',
                'fields' => [
                    [
                        'type' => 'mrkdwn',
                        'text' => 'Block',
                    ],
                    [
                        'type' => 'mrkdwn',
                        'text' => 'Attachments',
                    ],
                ],
                'accessory' => [
                    'type' => 'datepicker',
                ],
            ],
            [
                'type' => 'divider',
            ],
            [
                'type' => 'image',
                'image_url' => 'https://placekitten.com/400/600',
                'alt_text' => 'A cute little kitten',
                'title' => [
                    'type' => 'plain_text',
                    'text' => 'Kitten picture',
                ],
            ],
            [
                'type' => 'actions',
                'elements' => [
                    'type' => 'button',
                    'text' => [
                        'type' => 'plain_text',
                        'text' => 'Cancel',
                    ],
                ],
            ],
        ];

        return [
            new NotificationSlackChannelWithAttachmentBlockBuilderTestNotification,
            [
                'json' => [
                    'text' => 'Content',
                    'blocks' => $blocks,
                    'attachments' => [
                        [
                            'title' => 'Laravel',
                            'text' => 'Attachment Content',
                            'title_link' => 'https://laravel.com',
                            'blocks' => $blocks,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getPayloadWithAttachmentSpecialtyBlockBuilder()
    {
        return [
            new NotificationSlackChannelWithAttachmentSpecialtyBlockBuilderTestNotification,
            [
                'json' => [
                    'text' => 'Content',
                    'blocks' => [],
                    'attachments' => [
                        [
                            'title' => 'Specialty',
                            'blocks' => [
                                [
                                    'type' => 'image',
                                    'title' => [
                                        'type' => 'plain_text',
                                        'text' => 'Here is a kitten picture!',
                                    ],
                                    'image_url' => 'https://placekitten.com/400/600',
                                    'alt_text' => 'A cute little kitten',
                                ],
                                [
                                    'type' => 'image',
                                    'image_url' => 'https://placekitten.com/600/400',
                                    'alt_text' => 'Another cute cat',
                                ],
                                [
                                    'type' => 'divider',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getPayloadWithBaseSlackMessage()
    {
        return [
            new NotificationSlackChannelWithBaseSlackMessage,
            [
                'json' => [
                    'text' => 'Content',
                    'blocks' => [],
                    'attachments' => []
                ],
            ],
        ];
    }

}

class NotificationSlackChannelTestNotifiable
{
    use Notifiable;

    public function routeNotificationForSlack()
    {
        return 'url';
    }
}

class NotificationSlackChannelWithAttachmentBlockBuilderTestNotification extends Notification
{
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->content('Content')
            ->block(function ($block) {
                $block
                    ->type('section')
                    ->text([
                        'type' => 'plain_text',
                        'text' => 'Laravel',
                    ])
                    ->id('block-one')
                    ->fields([
                        [
                            'type' => 'mrkdwn',
                            'text' => 'Block',
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => 'Attachments',
                        ],
                    ])
                    ->accessory([
                        'type' => 'datepicker',
                    ]);
            })
            ->block(function ($block) {
                $block->type('divider');
            })
            ->block(function ($block) {
                $block
                    ->type('image')
                    ->imageUrl('https://placekitten.com/400/600')
                    ->altText('A cute little kitten')
                    ->title([
                        'type' => 'plain_text',
                        'text' => 'Kitten picture'
                    ]);
            })
            ->block(function ($block) {
                $block
                    ->type('actions')
                    ->elements([
                        'type' => 'button',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => 'Cancel',
                        ],
                    ]);
            })
            ->attachment(function ($attachment) {
                $attachment->title('Laravel', 'https://laravel.com')
                    ->content('Attachment Content')
                    ->block(function ($attachmentBlock) {
                        $attachmentBlock
                            ->type('section')
                            ->text([
                                'type' => 'plain_text',
                                'text' => 'Laravel',
                            ])
                            ->id('block-one')
                            ->fields([
                                [
                                    'type' => 'mrkdwn',
                                    'text' => 'Block',
                                ],
                                [
                                    'type' => 'mrkdwn',
                                    'text' => 'Attachments',
                                ],
                            ])
                            ->accessory([
                                'type' => 'datepicker',
                            ]);
                    })
                    ->block(function ($attachmentBlock) {
                        $attachmentBlock->type('divider');
                    })
                    ->block(function ($attachmentBlock) {
                        $attachmentBlock
                            ->type('image')
                            ->imageUrl('https://placekitten.com/400/600')
                            ->altText('A cute little kitten')
                            ->title([
                                'type' => 'plain_text',
                                'text' => 'Kitten picture'
                            ]);
                    })
                    ->block(function ($attachmentBlock) {
                        $attachmentBlock
                            ->type('actions')
                            ->elements([
                                'type' => 'button',
                                'text' => [
                                    'type' => 'plain_text',
                                    'text' => 'Cancel',
                                ],
                            ]);
                    });
            });
    }
}

class NotificationSlackChannelWithAttachmentSpecialtyBlockBuilderTestNotification extends Notification
{
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->content('Content')
            ->attachment(function ($attachment) {
                $attachment->title('Specialty')
                    ->imageBlock('https://placekitten.com/400/600', 'A cute little kitten', 'Here is a kitten picture!')
                    ->imageBlock('https://placekitten.com/600/400', 'Another cute cat')
                    ->dividerBlock();
            });
    }
}


class NotificationSlackChannelWithBaseSlackMessage extends Notification
{
    public function toSlack($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\SlackMessage)->content('Content');
    }
}