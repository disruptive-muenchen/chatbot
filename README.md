# OpenAI Slack Bot

This is a PHP-based Slack bot that utilizes OpenAI language models to generate responses to messages.

## Installation

Use git to clone the repository.

```bash
git clone https://github.com/tony3dmc/ai-slackbot.git
```

Navigate into the project directory and install the PHP dependencies.

```bash
cd ai-slackbot
composer install
```

## Configuration

1. Create a Slack app and obtain an OAuth token and the App ID.
2. Get an OpenAI API key.
3. Create an empty file named `database.db` in the `data/` directory.
4. Ensure the entire `data/` folder has write permissions for your web server.

## Slack Setup

1. **Create a Slack App:**
   - Go to https://api.slack.com/apps.
   - Click "Create New App" and choose "From an app manifest" to get started quickly.
   - Choose "YAML" for the manifest format.
   - Paste the following YAML code into the editor, replacing "your-bot-name" with your desired bot name:

```YAML
_metadata:
  major_version: 1
  minor_version: 1
display_information:
  name: your-bot-name
  description: A useful description
features:
  bot_user:
    display_name: your-bot-name
    always_online: true
oauth_config:
  scopes:
    bot:
      - channels:history
      - chat:write
```

   - Click "Next" and then "Create".

2. **Install the App in Your Workspace:**
   - After the app is created, install the app into your workspace by clicking on "Install App to Workspace".
   - You'll need to allow the requested permissions.

3. **Save the Details**
   - Save the Bot User OAuth Token and the App ID. You will need these for the persona files.

4. **Setup Event Subscriptions:**
   - In the app settings under "Event Subscriptions", toggle the switch to "On".
   - In the "Request URL" field, enter the URL where your PHP script is hosted. It must be publicly accessible and SSL protected.
   - After verifying your request URL, under "Subscribe to bot events", click "Add Bot User Event" and select `message.channels`.
   - Save the changes.

5. **Install the App in a Channel:**
   - Now, go to your workspace and invite the bot to the channel where you want it to listen and respond.


## Usage

To use the bot, create a persona YAML file in the `data/personas/` folder. See the `examples/` folder for some starting points.

1. Open the `data/personas/` directory in your file system.

2. In this directory, create a new YAML file for your persona. The filename can be anything you want, but it should end with the `.yml` extension. For instance, you might name your file `tony.yml`.

3. The persona file should follow the structure shown in the example. Here is an explanation of each attribute:

   - `name`: The persona's name.
   
   - `app_id`: The Slack app's ID.
   
   - `slack_oauth_token`: The OAuth token of the Slack app.
   
   - `system_prompt`: A block of text that describes the persona. This text is also passed to OpenAI models to influence how it generates responses.
   
   - `response_rules`: A list of rules that determine when the persona will respond. Each rule has a `rule_type`, `value`, and sometimes a `chance` (the probability that the rule will be applied).

Here's an example as a guide

:

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

Creating multiple personas is encouraged. Generate AI bots that interact with each other!

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.
