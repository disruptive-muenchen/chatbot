# OpenAI Slack Bot

This is a PHP-based Slack bot that uses OpenAI language models for generating responses to messages.

## Installation

Use git to clone the repository.

```bash
git clone https://github.com/tony3dmc/ai-slackbot.git
```

Navigate into the project directory and install PHP dependencies.

```bash
cd repository
composer install
```

## Configuration

1. Create a Slack app and obtain an OAuth token and the App ID.
2. Get an OpenAI API key.
3. Create and empty data/database.db file.
4. Ensure the whole data/ folder is writable by your webserver

## Usage

To use the bot, create a persona YAML file in the data/personas/ folder. See the examples/ folder for a couple of examples to get started.

1. Open the `data/personas/` directory in your file system.

2. In this directory, create a new YAML file for your persona. The filename can be anything you want, but should end with the `.yml` extension. For instance, you might name your file `tony.yml`.

3. The persona file should follow the structure shown in the example. Here is an explanation for each attribute:

   - `name`: The persona's name.
   
   - `app_id`: The Slack app's ID.
   
   - `slack_oauth_token`: The OAuth token of the Slack app.
   
   - `system_prompt`: A block of text that describes the persona. This text is also passed to OpenAI models to influence how it generates responses.
   
   - `response_rules`: A list of rules that determine when the persona wants to respond. Each rule has a `rule_type`, `value`, and sometimes a `chance` (the probability that the rule will apply).

Here's an example as a guide:

```yaml
name: 'Tony'
app_id: '...'
slack_oauth_token: 'xoxb-...'
system_prompt: |
  I want you to act as a Slack chat bot.
  You are Tony, a nerd with an obsession with fact-checking people.
  You are surly and abrasive.
  You look like a grumpy nerd with glasses.
  You love science fiction.
  Your responses are short.
response_rules:
  - rule_type: 'text_starts_with'
    value: 'How do I'
  - rule_type: 'default'
    chance: 0.1
```

Remember to always use valid YAML syntax and to follow the structure demonstrated in the example.

Multiple personas are encouraged. Create AI bots that interact with each other!

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.
