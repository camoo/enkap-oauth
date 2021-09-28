<?php
declare(strict_types=1);

namespace Enkap\OAuth\Lib;

use Enkap\OAuth\Exception\EnkapException as Exception;

class Json
{

    /** @var string $file */
    private $file;

    /** @var string $json */
    private $json;

    public function __construct(?string $json = null, ?string $file = null)
    {
        $this->file = $file;
        $this->json = $json;
    }

    /**
     * decode json string
     * @param string|null $sJSON
     * @param bool $bAsHash
     *
     * @return array|object
     */
    public function decode(?string $sJSON = null, bool $bAsHash = true)
    {
        $jsonData = $sJSON ?? $this->json;


        if (null === $jsonData) {
            throw new Exception('Cannot decode on NULL');
        }
        if ($jsonData === '') {
            return [];
        }

        if (($xData = json_decode($jsonData, $bAsHash)) !== null
            && (json_last_error() === JSON_ERROR_NONE)) {
            return $xData;
        }

        throw new Exception(json_last_error_msg());
    }

    /**
     * Reads a json file
     *
     * @param null|string $sFile
     *
     * @return array
     * @throws Exception
     *
     */
    public function read(?string $sFile = null): array
    {
        $fileName = $sFile ?? $this->file;

        if (null === $fileName || !is_file($fileName)) {
            throw new Exception(sprintf('%s does not exist !', $fileName));
        }

        $sData = file_get_contents($fileName);

        if ($sData === false) {
            throw new Exception(sprintf('Content of file %s cannot be read', $fileName));
        }

        return $this->decode($sData);
    }
}
