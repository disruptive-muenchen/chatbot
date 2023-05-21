<?php

/**
 * Class Bot
 *
 * This class encapsulates the behavior of the chat bot.
 */
class Bot
{
    /**
     * @var Logger $logger Instance of the Logger class for event logging.
     */
    private $logger;

    /**
     * @var Persona $persona The persona the bot will assume.
     */
    private $persona;

    /**
     * @var DB $db Instance of the DB class for database operations.
     */
    private $db;

    /**
     * @var GPT $gpt Instance of the GPT class for language model interactions.
     */
    private $gpt;

    /**
     * @var Slack $slack Instance of the Slack class for interacting with the Slack API.
     */
    private $slack;
    
    /**
     * Bot constructor.
     *
     * Initialise the Bot class with the provided persona.
     *
     * @param Persona $persona The persona the bot will assume.
     */
    public function __construct($persona)
    {
        $this->persona = $persona;
    }

    /**
     * Give the bot a logger to record events.
     *
     * @param Logger $logger The logger to use.
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Give the bot a database to work with.
     *
     * @param DB $database The database instance.
     */
    public function setDatabase($database)
    {
        $this->db = $database;
    }

    /**
     * Set up the OpenAI GPT API.
     *
     * @param GPT $gpt The OpenAI GPT model to request responses from.
     */
    public function setGpt($gpt)
    {
        $this->gpt = $gpt;
    }

    /**
     * Set up the Slack API model.
     *
     * @param Slack $slack The Slack API instance to use for sending messages.
     */
    public function setSlack($slack)
    {
        $this->slack = $slack;
    }

    /**
     * Processes an incoming payload (request).
     *
     * @param array $payload The incoming payload to process.
     */
    public function process($payload)
    {
        $this->logger->setEvent($payload['event_id']);

        // Check for duplicate events
        if ($this->db->eventExists($payload['event_id'], $this->persona->getName())) {
            $this->logger->log("Skipping duplicate event");
            exit;
        }
        $this->db->addEvent($payload['event_id'], $this->persona->getName());

        if ($payload['type'] == 'event_callback' && $payload['event']['type'] == 'message') {
            $me = $payload['authorizations'][0]['user_id'];

            $user = $payload['event']['user'];
            $speaker = $payload['event']['bot_profile']['name'] ?: $user;

            $channel = $payload['event']['channel'];
            $thread_ts = isset($payload['event']['thread_ts']) ? $payload['event']['thread_ts'] : null;

            $text = $payload['event']['text'];
            $text = str_replace('<@' . $me . '>', $this->persona->getName(), $text);

            $this->logger->log(sprintf("%s received message: %s", $this->persona->getName(), $text));
    
            $this->db->addMessage($speaker, $text);

            $text = str_replace('<@' . $me . '>', $this->persona->getName(), $text);

            if ($this->persona->wantsToRespond($text, $speaker)) {
                $this->logger->log(sprintf("%s wants to respond", $this->persona->getName()));
                $response = $this->gpt->complete([
                    [
                        'role' => 'user',
                        'content' => $text
                    ]
                ]);
                $this->logger->log(sprintf("%s responding with: %s", $this->persona->getName(), $response));
                $this->slack->sendMessage($channel, $response, $thread_ts);
            } else {
                $this->logger->log(sprintf("%s does not want to respond", $this->persona->getName()));
            }
        }
    }

    /**
     * Send a test message to a given Slack channel.
     *
     * @param string $channel The ID of the Slack channel to send the message to.
     * @param string $message The message text.
     */
    public function sendTestMessage($channel, $message)
    {
        $this->slack->sendMessage($channel, $message);
    }

    /**
     * Generate a test response based on a provided message.
     *
     * @param string $message The input message based on which to generate a response.
     * @return string The generated response.
     */
    public function generateTestResponse($message)
    {
        $this->gpt->setSystemMessage($this->persona->getPrompt());
        $response = $this->gpt->complete([
            [
                'role' => 'user',
                'content' => $message
            ]
        ]);
        return $response;
    }
}
