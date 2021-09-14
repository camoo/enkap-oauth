<?php
declare(strict_types=1);

namespace Enkap\OAuth\Model;

use Enkap\OAuth\Http\Client;

/**
 * @property string $access_token
 * @property int $expires_in
 * @property string $token_type
 * @property string $scope
 */
class Token extends BaseModel
{
    private const MODEL_NAME = 'Token';

    /**
     * Get the supported methods.
     */
    public static function getSupportedMethods(): array
    {
        return [
            Client::POST_REQUEST,
        ];
    }

    /**
     * Get the properties of the object.  Indexed by constants
     *  [0] - Mandatory
     *  [1] - Type
     *  [2] - PHP type
     *  [3] - Is an Array
     *  [4] - Saves directly.
     *
     * @return array
     */
    public static function getProperties(): array
    {
        return [
            'access_token' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'expires_in' => [false, self::PROPERTY_TYPE_INT, null, false, false],
            'token_type' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'scope' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
        ];
    }

    public function getExpiresIn(): int
    {
        return $this->_data['expires_in'];
    }

    public function getAccessToken(): string
    {
        return $this->_data['access_token'];
    }

    public function getTokenType(): ?string
    {
        return $this->_data['token_type'];
    }

    public function getScope(): string
    {
        return $this->_data['scope'];
    }

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    public function getResourceURI(): string
    {
        return '/token';
    }
}
