<?php
declare(strict_types=1);

namespace Enkap\OAuth\Http;

use Enkap\OAuth\Lib\Json;

/**
 * Class Response
 *
 * @author CamooSarl
 */
class Response
{
    /** @var int $statusCode */
    private $statusCode;

    /** @var string $content */
    private $content;

    /** @var string */
    const BAD_STATUS = 'KO';

    /** @var string */
    const GOOD_STATUS = 'OK';

    /** @var Json $jsonData */
    protected $jsonData;
    /**
     * @var array
     */
    private $headers;

    /**
     * @param string $content
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->jsonData = new Json($content);
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array|object
     */
    public function getJson()
    {
        if ($this->getStatusCode() !== 200) {
            $message = $this->content !== '' ? $this->content : 'request failed!';
            return (object)['status' => static::BAD_STATUS, 'message' => $message];
        }
       $result = $this->jsonData->decode();
        $result->status = self::GOOD_STATUS;
        return $result;
    }

}
