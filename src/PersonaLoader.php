<?php

use Symfony\Component\Yaml\Yaml;

/**
 * Class PersonaLoader
 *
 * Load persona configuration based on the given Slack payload.
 */
class PersonaLoader
{
    /**
     * @var string $persona_directory Directory where persona configuration files are stored.
     */
    private $persona_directory;

    /**
     * PersonaLoader constructor.
     *
     * @param string $persona_directory Directory where persona configuration files are stored.
     */
    public function __construct($persona_directory)
    {
        $this->persona_directory = $persona_directory;
    }

    /**
     * Load a Persona based on the app_id from the Slack payload.
     *
     * Parse each persona configuration file in the directory, and if a configuration's app_id matches the given
     * app_id create and return a new Persona object.
     *
     * @param string $target_app_id The App ID from the Slack payload.
     * @return Persona|bool A new Persona object if a matching configuration is found, false otherwise.
     */
    public function load($target_app_id)
    {
        foreach (glob($this->persona_directory . '/*.yml') as $file) {
            $configuration = Yaml::parseFile($file);
            if ($configuration['app_id'] == $target_app_id) {
                return new Persona($configuration);
            }
        }

        return false;
    }
}
