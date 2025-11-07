<?php

declare(strict_types=1);

namespace Enkap\OAuth\Lib;

use Enkap\OAuth\Exception\EnkapException as Exception;
use JsonException;
use stdClass;

final readonly class Json
{
    public function __construct(private ?string $json = null, private ?string $file = null)
    {
    }

    /** decode json string */
    public function decode(?string $sJSON = null, bool $asArray = true): array|stdClass
    {
        $data = $sJSON ?? $this->json;

        if (null === $data) {
            throw new Exception('Cannot decode on NULL');
        }
        if ($data === '') {
            return $asArray ? [] : new stdClass();
        }

        try {
            /** @var array|stdClass $decoded */
            $decoded = json_decode($data, $asArray, 512, JSON_THROW_ON_ERROR);

        } catch (JsonException $e) {
            throw new Exception(sprintf('JSON decode error: %s', $e->getMessage()), 0, $e);
        }

        return $decoded;
    }

    /**
     * Reads a json file
     *
     * @throws Exception
     */
    public function read(?string $file = null): array
    {
        $filePath = $file ?? $this->file;

        if ($filePath === null) {
            throw new Exception('Cannot read: file path is null.');
        }

        if (!is_file($filePath)) {
            throw new Exception(sprintf('File does not exist: %s', $filePath));
        }

        $contents = @file_get_contents($filePath);

        if ($contents === false) {
            throw new Exception(sprintf('Unable to read file contents: %s', $filePath));
        }

        return $this->decode($contents);
    }
}
