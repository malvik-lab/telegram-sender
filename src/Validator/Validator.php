<?php

namespace MalvikLab\TelegramSender\Validator;

use Opis\JsonSchema\{
    Validator as OpisValidator,
    Errors\ErrorFormatter,
};

class Validator
{
    private OpisValidator $validator;

    public function __construct()
    {
        $this->validator = new OpisValidator();
        $this->register();
    }

    /**
     * @param array $data
     * @param string $schema
     * @return bool|array
     */
    public function validate(array $data, string $schema): bool | array
    {
        $result = $this->validator->validate(
            json_decode(json_encode($data)),
            $schema
        );

        if ( $result->isValid() )
        {
            return true;
        }

        return (new ErrorFormatter())->format($result->error());
    }

    /**
     * @return void
     */
    private function register(): void
    {
        $basePath = sprintf('%s/schemas/', dirname(__FILE__));
        $schemas = array_diff(scandir($basePath), ['..', '.']);

        foreach ( $schemas as $schema )
        {
            $this->validator->resolver()->registerFile(
                sprintf('https://example.com/%s', $schema),
                sprintf('%s/%s', $basePath, $schema)
            );
        }
    }
}