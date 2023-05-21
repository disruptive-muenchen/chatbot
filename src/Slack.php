<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

/**
 * Class Slack
 *
 * This class provides an interface for Slack's API.
 */
class Slack
{
    /**
     * @var string $token Slack API token.
     */
    private $token;

    /**
     * @var Client $http GuzzleHttp client instance.
     */
    private $http;

    /**
     * @var string $base_uri Slack API base URI.
     */
    private $base_uri = 'https://slack.com/api/';

    /**
     * Slack constructor.
     *
     * Initialize the Slack class with the provided API token.
     *
     * @param string $token Slack API token.
     */
    public function __construct($token)
    {
        $this->token = $token;
        $this->http = new Client(['base_uri' => $this->base_uri]);
    }

    /**
     * Sends a message to a Slack channel.
     *
     * The message can be sent as a new message or as a reply to a thread.
     *
     * @param string $channel The ID of the Slack channel to send the message to.
     * @param string $message The message text.
     * @param string|null $thread_ts The timestamp of the thread to reply to. If null, the message is sent as a new message.
     *
     * @throws Exception if there is an error in the Slack API response or if the request to Slack API fails.
     */
    public function sendMessage($channel, $message, $thread_ts = null)
    {
        $data = [
            'form_params' => [
                'token' => $this->token,
                'channel' => $channel,
                'text' => $message,
                'thread_ts' => $thread_ts,
            ]
        ];

        try {
            $response = $this->http->post('chat.postMessage', $data);
            $response = json_decode($response->getBody(), true);
            
            if (isset($response['ok']) && $response['ok'] === false) {
                throw new Exception('Error from Slack API: ' . $response['error']);
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $exceptionMessage = Psr7\Message::toString($e->getResponse());
            } else {
                $exceptionMessage = $e->getMessage();
            }
            throw new Exception('Request to Slack API failed: ' . $exceptionMessage);
        }
    }
}
