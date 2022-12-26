# Telegram Sender

Simple library to use the official Telegram API.

## Available methods
- getMe
- sendMessage
- sendPhoto
- sendLocation
- sendDocument

## Installation (with Composer)
```
composer require malvik-lab/telegram-sender
```

## Use
```php
<?php

use GuzzleHttp\Client;
use MalvikLab\TelegramSender\TelegramSender;

require 'vendor/autoload.php';

$telegramSender = new TelegramSender('YOUR_BOT_KEY', new Client());

$telegramSender->sendMessage('CHAT_ID', 'Message');

$telegramSender->sendPhoto('CHAT_ID', 'PHOTO', 'Caption'); // PHOTO: image path or external url

$telegramSender->sendLocation('CHAT_ID', 'LATITUDE', 'LONGITUDE');

$telegramSender->sendDocument('CHAT_ID', 'DOCUMENT', 'Caption'); // DOCUMENT: document path or external url
```

## Running Test
```sh
BOT_KEY=yourBotKey CHAT_ID=yourChatId vendor/bin/phpunit tests/integration --testdox
```

