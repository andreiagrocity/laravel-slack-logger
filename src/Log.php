<?php

namespace Ioanandrei\SlackLogger;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log as LogFacade;

class Log
{
    protected string $message;
    protected string $level;

    public function __construct(string $message, string $level = 'info')
    {

        $this->message = $message;
        $this->level   = $level;
    }

    /**
     * Log a message to a given channel.
     *
     * @param string|null $message
     * @param string|null $level
     * @param string      $channel
     * @param array       $context
     *
     * @return self
     */
    public function log(string $message = null, ?string $level = null, string $channel = 'daily', array $context = []): self
    {
        // get and format the necessary variables
        $channel  = $this->formatInput($channel);
        $logLevel = $level ?: $this->level;

        // log the message normally
        LogFacade::channel($channel)->withContext($context)->$logLevel($message ?: $this->message);

        // return this instance, so it can be used with other chained methods
        return $this;
    }

    /**
     * Send the message to Slack as well.
     *
     * @param string|null $level
     *
     * @return void
     */
    public function toSlack(string $level = null): void
    {
        // get the level
        $logLevel = $level ?: $this->level;

        // send the message to slack
        LogFacade::channel('slack')->$logLevel($this->message);

        // reset the slack url
        $this->resetSlackUrl();
    }

    /**
     * Set the Slack channel URL that you want to use.
     *
     * @param string $url
     *
     * @return self
     */
    public function withSlackUrl(string $url): self
    {
        Config::set('logging.channels.slack.url', $url);

        return $this;
    }

    /**
     * Log a message.
     *
     * @param string $message
     * @param string $level
     *
     * @return static
     */
    public static function send(string $message, string $level = 'info'): self
    {
        return (new static($message, $level))->log();
    }

    /**
     * @param string $input
     *
     * @return string
     */
    protected function formatInput(string $input): string
    {
        return strtolower(str_replace(' ', '', $input));
    }

    /**
     * Reset the Slack URL to the default value.
     *
     * @return void
     */
    protected function resetSlackUrl(): void
    {
        Config::set('logging.channels.slack.url', config('slack_logger.channels.default'));
    }
}