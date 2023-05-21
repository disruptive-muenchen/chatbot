<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require 'vendor/autoload.php';

require 'src/Bot.php';
require 'src/DB.php';
require 'src/GPT.php';
require 'src/Slack.php';
require 'src/Logger.php';
require 'src/Persona.php';
require 'src/PersonaLoader.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config = [
    'openai_key' => $_ENV['OPENAI_KEY'],
    'log_file' => 'data/app.log',
    'log_file_lines' => 100,
    'database_file' => 'data/database.db',
    'persona_path' => 'data/personas/',
];
