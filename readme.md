# Laravel Slack Blocks

This package is an extension of the official `laravel/slack-notification-channel` package.

## Usage

Instead of requiring the official package, you should require this one instead.

```
composer require nathanheffley/laravel-slack-blocks
```

Because this package is built on top of the official one, you'll have all the functionality found in the [official docs](https://laravel.com/docs/5.8/notifications#slack-notifications).

You can follow those instructions with the slight adjustment of requiring the classes from `NathanHeffley\LaravelSlackBlocks` instead of `Illuminate\Notifications`.

Everything supported in the base Illuminate Notifications classes is supported in these extended classes.

If you want to add a block to your Slack message, you need to add the block in an attachment.

```
use NathanHeffley\LaravelSlackBlocks\Messages\SlackMessage;

// ...

public function toSlack($notifiable)
{
    return (new SlackMessage)
        ->attachment(function ($attachment) {
            $attachment->block(function ($block) {
                $block
                    ->type('section')
                    ->text([
                        'type' => 'mrkdwn',
                        'text' => '*Hello World!*',
                    ]);
            });
        });
}
```

To see all the possible fields you can add to a block, check out the [official Slack Blocks documentation](https://api.slack.com/reference/messaging/blocks).

To help, some blocks have been given dedicated helper functions on the attachment model itself. Currently there are methods for adding dividers and images.

```
(new SlackMessage)->attachment(function ($attachment) {
    $attachment->imageBlock('http://placekitten.com/300/200', 'A cute kitten');
    $attachment->dividerBlock();
    $attachment->imageBlock('http://placekitten.com/300/200', 'A cute kitten', 'This is a titled cat image');
});
```
