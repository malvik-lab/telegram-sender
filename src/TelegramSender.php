<?php

namespace MalvikLab\TelegramSender;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\GuzzleException;
use MalvikLab\TelegramSender\Exceptions\InvalidMethodException;

class TelegramSender
{
    private string $botKey;
    private Client $client;

    public const NAME = 'TELEGRAM SENDER';
    public const VERSION = '1.0.0';

    public const GET_ME = 'getMe';
    public const SEND_MESSAGE = 'sendMessage';
    public const SEND_PHOTO = 'sendPhoto';
    public const SEND_LOCATION = 'sendLocation';
    public const SEND_DOCUMENT = 'sendDocument';

    /**
     * @param string $botKey
     * @param Client|null $client
     */
    public function __construct(string $botKey, Client $client = null)
    {
        $this->botKey = $botKey;

        if ( is_null($client) )
        {
            $this->client =new Client();
        } else {
            $this->client = $client;
        }
    }

    /**
     * @return mixed
     * @throws InvalidMethodException
     * @throws GuzzleException
     */
    public function getMe()
    {
        $uri = $this->uri(self::GET_ME);
        $request = $this->client->post($uri, []);

        return json_decode((string)$request->getBody(), true);
    }

    /**
     * @param int|string $chatId
     * @param string $text
     * @return array
     * @throws GuzzleException
     * @throws InvalidMethodException
     */
    public function sendMessage(int | string $chatId, string $text): array
    {
        $uri = $this->uri(self::SEND_MESSAGE, [
            'chat_id' => $chatId,
            'text' => $text
        ]);
        $request = $this->client->post($uri, []);

        return json_decode((string)$request->getBody(), true);
    }

    /**
     * @param int|string $chatId
     * @param string $photo
     * @param string|null $caption
     * @return array
     * @throws GuzzleException
     * @throws InvalidMethodException
     */
    public function sendPhoto(int | string $chatId, string $photo, string $caption = null): array
    {
        if ( is_file($photo) )
        {
            return $this->sendPhotoFromFile($chatId, $photo, $caption);
        }

        return $this->sendPhotoFromUrl($chatId, $photo, $caption);
    }

    /**
     * @param int|string $chatId
     * @param float $latitude
     * @param float $longitude
     * @return array
     * @throws GuzzleException
     * @throws InvalidMethodException
     */
    public function sendLocation(int | string $chatId, float $latitude, float $longitude): array
    {
        $uri = $this->uri(self::SEND_LOCATION, [
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
        $request = $this->client->post($uri, []);

        return json_decode((string)$request->getBody(), true);
    }

    /**
     * @param int|string $chatId
     * @param string $document
     * @param string|null $caption
     * @return array
     * @throws GuzzleException
     * @throws InvalidMethodException
     */
    public function sendDocument(int | string $chatId, string $document, string $caption = null): array
    {
        if ( is_file($document) )
        {
            return $this->sendDocumentFromFile($chatId, $document, $caption);
        }

        return $this->sendDocumentFromUrl($chatId, $document, $caption);
    }

    /**
     * @throws InvalidMethodException
     */
    private function uri(string $method, array $params = []): string
    {
        $constant = Utils\StringUtil::camelCaseToSnakeCase($method);

        if ( !defined(get_class($this) . '::' . $constant) )
        {
            throw new InvalidMethodException(Utils\StringUtil::exception(sprintf('"%s" is not a valid method', $method)));
        }

        return sprintf('https://api.telegram.org/bot%s/%s?%s',
            $this->botKey,
            $method,
            http_build_query($params)
        );
    }

    /**
     * @param int|string $chatId
     * @param string $photo
     * @param string|null $caption
     * @return array
     * @throws GuzzleException
     * @throws InvalidMethodException
     */
    private function sendPhotoFromFile(int | string $chatId, string $photo, string $caption = null): array
    {
        $uri = $this->uri(self::SEND_PHOTO, [
            'chat_id' => $chatId,
            'caption' => $caption
        ]);

        $response = $this->client->post($uri, [
            'multipart' => [
                [
                    'name' => 'photo',
                    'contents' => Psr7\Utils::tryFopen($photo, 'r'),
                ]
            ]
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * @param int|string $chatId
     * @param string $photo
     * @param string|null $caption
     * @return array
     * @throws GuzzleException
     * @throws InvalidMethodException
     */
    private function sendPhotoFromUrl(int | string $chatId, string $photo, string $caption = null): array
    {
        $uri = $this->uri(self::SEND_PHOTO, [
            'chat_id' => $chatId,
            'photo' => $photo,
            'caption' => $caption
        ]);

        $response = $this->client->post($uri, []);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * @param int|string $chatId
     * @param string $document
     * @param string|null $caption
     * @return array
     * @throws GuzzleException
     * @throws InvalidMethodException
     */
    private function sendDocumentFromFile(int | string $chatId, string $document, string $caption = null): array
    {
        $uri = $this->uri(self::SEND_DOCUMENT, [
            'chat_id' => $chatId,
            'caption' => $caption
        ]);

        $response = $this->client->post($uri, [
            'multipart' => [
                [
                    'name' => 'document',
                    'contents' => Psr7\Utils::tryFopen($document, 'r'),
                ]
            ]
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * @param int|string $chatId
     * @param string $document
     * @param string|null $caption
     * @return array
     * @throws GuzzleException
     * @throws InvalidMethodException
     */
    private function sendDocumentFromUrl(int | string $chatId, string $document, string $caption = null): array
    {
        $uri = $this->uri(self::SEND_DOCUMENT, [
            'chat_id' => $chatId,
            'document' => $document,
            'caption' => $caption
        ]);

        $response = $this->client->post($uri, []);

        return json_decode((string)$response->getBody(), true);
    }
}