<?php

use Symfony\Component\Yaml\Yaml;

/**
 * Class Persona
 *
 * This class represents a Persona, containing properties and behavior defined in a configuration.
 */
class Persona
{
    /**
     * @var string $name Name of the persona.
     */
    private $name;

    /**
     * @var string $token Slack OAuth token.
     */
    private $token;

    /**
     * @var string $prompt System prompt.
     */
    private $prompt;

    /**
     * @var array $rules Response rules for the persona.
     */
    private $rules;

    /**
     * Persona constructor.
     *
     * @param array $configuration Configuration data for the persona.
     */
    public function __construct($configuration)
    {
        $this->name = $configuration['name'];
        $this->token = $configuration['slack_oauth_token'];
        $this->rules = $configuration['response_rules'];
        $this->prompt = trim($configuration['system_prompt']);
    }

    /**
     * Get the name of the persona.
     *
     * @return string The name of the persona.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the Slack OAuth token of the persona.
     *
     * @return string The Slack OAuth token.
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get the system prompt of the persona.
     *
     * @return string The system prompt.
     */
    public function getPrompt()
    {
        return $this->prompt;
    }

    /**
     * Check if the persona wants to respond based on text and speaker.
     *
     * Iterate the various response rules of the persona to see if this persona wants to respond
     *
     * @param string $text The input text.
     * @param string $speaker The speaker's name.
     * @return bool True if the persona wants to respond, false otherwise.
     */
    public function wantsToRespond($text, $speaker)
    {
        // Don't respond to yourself
        if ($speaker == $this->getName()) {
            return false;
        }

        // Check if the persona's name is mentioned
        if (strpos(strtolower($text), strtolower($this->getName())) !== false) {
            return true;
        }

        foreach ($this->rules as $rule) {
            if ($this->evaluateRule($rule, strtolower($text), strtolower($speaker))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Evaluate a rule based on text and speaker.
     *
     * There are various types of rule, each with their own evaluation logic. This method will evaluate a rule based on
     * the given text and speaker. Rules can also have an optional chance property, which is a float between 0 and 1.
     *
     * @param array $rule The rule to be evaluated.
     * @param string $text The input text.
     * @param string $speaker The speaker's identifier.
     * @return bool True if the rule is satisfied, false otherwise.
     */
    private function evaluateRule($rule, $text, $speaker)
    {
        if (isset($rule['chance']) && mt_rand() / mt_getrandmax() > $rule['chance']) {
            return false;
        }
        switch ($rule['rule_type']) {
            case 'default':
                return true;
            case 'text_ends_with':
                return substr($text, -strlen($rule['value'])) == $rule['value'];
            case 'text_starts_with':
                return substr($text, 0, strlen($rule['value'])) == $rule['value'];
            case 'speaker_is':
                return $speaker == $rule['value'];
        }
        return false;
    }
}
