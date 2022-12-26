<?php declare(strict_types=1);

namespace Integration;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use MalvikLab\TelegramSender\TelegramSender;
use MalvikLab\TelegramSender\Validator\Validator;

## BOT_KEY=yourBotKey CHAT_ID=yourChatId vendor/bin/phpunit tests/integration --testdox

final class TelegramSenderTest extends TestCase
{
    protected TelegramSender $telegramSender;
    protected Validator $validator;
    protected FakerGenerator $faker;

    protected function setUp(): void
    {
        $this->telegramSender = new TelegramSender(getenv('BOT_KEY'));
        $this->validator = new Validator();
        $this->faker = FakerFactory::create();
    }

    public function testGetMe(): void
    {
        $res = $this->telegramSender->getMe();
        $validate = $this->validator->validate($res,'https://example.com/get-me.json');
        $this->assertTrue($validate);
    }

    public function testSendMessage(): void
    {
        $res = $this->telegramSender->sendMessage(
            getenv('CHAT_ID'),
            $this->faker->sentence()
        );

        $validate = $this->validator->validate($res,'https://example.com/send-message.json');
        $this->assertTrue($validate);
    }

    public function testSendPhoto(): void
    {
        $res = $this->telegramSender->sendPhoto(
            getenv('CHAT_ID'),
            'https://source.unsplash.com/random?r=' . $this->faker->md5(),
            $this->faker->sentence()
        );

        $validate = $this->validator->validate($res,'https://example.com/send-photo.json');
        $this->assertTrue($validate);

        $tmpFilePath = sprintf('%s/tmp/img.jpg', dirname(__DIR__));

        file_put_contents(
            $tmpFilePath,
            file_get_contents('https://source.unsplash.com/random?r=' . $this->faker->md5())
        );

        $res = $this->telegramSender->sendPhoto(
            getenv('CHAT_ID'),
            $tmpFilePath,
            $this->faker->sentence()
        );

        unlink($tmpFilePath);

        $validate = $this->validator->validate($res,'https://example.com/send-photo.json');
        $this->assertTrue($validate);
    }

    public function testSendLocation(): void
    {
        $res = $this->telegramSender->sendLocation(
            getenv('CHAT_ID'),
            $this->faker->latitude(),
            $this->faker->longitude()
        );

        $validate = $this->validator->validate($res,'https://example.com/send-location.json');
        $this->assertTrue($validate);
    }

    public function testSendDocument(): void
    {
        $res = $this->telegramSender->sendDocument(
            getenv('CHAT_ID'),
            'https://jsonplaceholder.typicode.com/users',
            $this->faker->sentence()
        );

        $validate = $this->validator->validate($res,'https://example.com/send-document.json');
        $this->assertTrue($validate);

        $tmpFilePath = sprintf('%s/tmp/document.txt', dirname(__DIR__));

        file_put_contents(
            $tmpFilePath,
            $this->faker->sentence()
        );

        $res = $this->telegramSender->sendDocument(
            getenv('CHAT_ID'),
            $tmpFilePath,
            $this->faker->sentence()
        );

        unlink($tmpFilePath);

        $validate = $this->validator->validate($res,'https://example.com/send-document.json');
        $this->assertTrue($validate);
    }
}