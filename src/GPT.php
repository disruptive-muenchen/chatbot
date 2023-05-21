<?php

use Orhanerday\OpenAi\OpenAi;

/**
 * Class GPT
 *
 * This class provides an interface for interacting with OpenAI's GPT model.
 */
class GPT
{
    /**
     * @var OpenAi $open_ai Instance of OpenAi.
     */
    private $open_ai;

    /**
     * @var string $system_message System message to initiate the conversation with GPT.
     */
    private $system_message;

    /**
     * GPT constructor.
     *
     * Initialize the GPT class with an OpenAI API key.
     *
     * @param string $key OpenAI API key.
     * @throws Exception if the API key is empty.
     */
    public function __construct($key)
    {
        if (empty($key)) {
            throw new Exception("API key cannot be empty");
        }

        $this->open_ai = new OpenAi($key);
    }

    /**
     * Set the system message to initiate the conversation with GPT.
     *
     * @param string $message The system message.
     */
    public function setSystemMessage($message)
    {
        $this->system_message = $message;
    }

    /**
     * Complete the conversation using GPT model.
     *
     * The conversation is initiated with the system message and followed by a sequence of user/assistant messages.
     * The GPT model generates a response to the conversation.
     *
     * @param array $messages An array of associative arrays, each containing 'role' and 'content' of a message.
     * @return string The generated message content from the GPT model.
     * @throws Exception if there is an error in the OpenAI API response or if the response is unexpected.
     */
    public function complete($messages)
    {
        $payload = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->system_message
                ]
            ],
            'temperature' => 1.0,
            'max_tokens' => 1000,
            'frequency_penalty' => 0,
            'presence_penalty' => 0
        ];

        foreach ((array)$messages as $message) {
            $payload['messages'][] = [
                'role' => $message['role'],
                'content' => $message['content']
            ];
        }

        $response = json_decode($this->open_ai->chat($payload));

        if (isset($response->error)) {
            throw new Exception("OpenAI API Error: " . $response->error->message);
        } elseif (isset($response->choices[0]->message->content)) {
            return $response->choices[0]->message->content;
        } else {
            throw new Exception("Unexpected API response");
        }
    }
}
