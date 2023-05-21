<?php

require 'configure.php';

if (php_sapi_name() === 'cli') {
    echo "This is a Slack chatbot.\n";
    exit;
} else {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
}

$payload = file_get_contents('php://input');
$payload = json_decode($payload, true);

if ($payload['type'] == 'url_verification') {
    echo $payload['challenge'];
    exit;
}

$logger = new Logger($config['log_file'], $config['log_file_lines']);

try {
    $loader = new PersonaLoader($config['persona_path']);
    $persona = $loader->load($payload['api_app_id']);

    $database = new DB($config['database_file']);

    $gpt = new GPT($config['openai_key']);
    $gpt->setSystemMessage($persona->getPrompt());

    $slack = new Slack($persona->getToken());

    $bot = new Bot($persona);
    $bot->setLogger($logger);
    $bot->setDatabase($database);
    $bot->setGpt($gpt);
    $bot->setSlack($slack);

    $bot->process($payload);
} catch (Exception $e) {
    $logger->log($e->getMessage());
}
