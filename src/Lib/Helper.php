<?php

declare(strict_types=1);

namespace Enkap\OAuth\Lib;

use Composer\InstalledVersions;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\BadFormatException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\Key;
use Enkap\OAuth\Exception\EnkapException;
use JetBrains\PhpStorm\NoReturn;
use Throwable;

/**
 * Class Helper
 *
 * @author CamooSarl
 */
final class Helper
{
    private const ENKAP_CLIENT_VERSION = '1.0.6';

    private const PACKAGE_NAME = 'camoo/enkap-oauth';

    public static function satanise(mixed $str, bool $keep_newlines = false): array|string
    {
        if (is_object($str) || is_array($str)) {
            return '';
        }
        $filtered = (string)$str;
        if (!mb_check_encoding($filtered, 'UTF-8')) {
            return '';
        }
        if (str_contains($filtered, '<')) {
            $callback = function ($match) {
                if (!str_contains($match[0], '>')) {
                    return htmlentities($match[0], ENT_QUOTES | ENT_IGNORE, 'UTF-8');
                }

                return $match[0];
            };
            $filtered = preg_replace_callback('%<[^>]*((?=<)|>|$)%', $callback, $filtered);
            $filtered = self::stripAllTags($filtered);
            $filtered = str_replace("<\n", "&lt;\n", $filtered);
        }
        if (!$keep_newlines) {
            $filtered = preg_replace('/[\r\n\t ]+/', ' ', $filtered);
        }
        $filtered = trim($filtered);
        $found = false;
        while (preg_match('/%[a-f0-9]{2}/i', $filtered, $match)) {
            $filtered = str_replace($match[0], '', $filtered);
            $found = true;
        }
        if ($found) {
            $filtered = trim(preg_replace('/ +/', ' ', $filtered));
        }

        return $filtered;
    }

    /**
     * @throws EnvironmentIsBrokenException
     * @throws BadFormatException
     */
    public static function encrypt(string $plaintext, bool $raw_binary = false): string
    {
        $key = Key::loadFromAsciiSafeString($_ENV['CRYPTO_SALT']);

        return Crypto::encrypt($plaintext, $key, $raw_binary);
    }

    /**
     * @throws EnvironmentIsBrokenException
     * @throws BadFormatException
     * @throws WrongKeyOrModifiedCiphertextException
     */
    public static function decrypt(string $ciphertext, bool $raw_binary = false): string
    {
        $key = Key::loadFromAsciiSafeString($_ENV['CRYPTO_SALT']);

        return Crypto::decrypt($ciphertext, $key, $raw_binary);
    }

    /** @param string $destination URL to redirect to */
    public static function redirect(string $destination, bool $permanent): void
    {
        if (mb_strpos($destination, '://') === false) {
            if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
                $protocol = 'http';
            } else {
                $protocol = 'https';
            }
            $destination = $protocol . '://' . $_SERVER['HTTP_HOST'] . $destination;
        }
        if ($permanent) {
            $code = 301;
            $message = $code . ' Moved Permanently';
        } else {
            $code = 302;
            $message = $code . ' Found';
        }
        header('HTTP/' . $_SERVER['SERVER_PROTOCOL'] . ' ' . $message, true, $code);
        header('Status: ' . $message, true, $code);
        header('Location: ' . $destination);
    }

    /** Exit function */
    #[NoReturn]
    public static function exitOrDie(): void
    {
        exit(0);
    }

    /** @throws EnvironmentIsBrokenException */
    public static function createEnvFile(): void
    {
        $key = Key::createNewRandomKey();
        $salt = $key->saveToAsciiSafeString();
        $content = sprintf('CRYPTO_SALT="%s"' . PHP_EOL, $salt);

        $envFile = dirname(__DIR__, 2) . '/config/.env';

        $result = file_put_contents($envFile, $content);

        if ($result === false) {
            throw new EnkapException('.env File could not be created');
        }
    }

    public static function getPhpVersion(): string
    {
        return 'PHP/' . PHP_VERSION;
    }

    public static function isAssoc(array $array): bool
    {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * Generic function to flatten an associative array into an arbitrarily
     * delimited string.
     *
     * @return array|string if no glue provided, it won't be imploded
     */
    public static function flattenAssocArray(
        array $array,
        string $format,
        ?string $glue = null,
        bool $escape = true
    ) {
        $pairs = [];
        foreach ($array as $key => $val) {
            if ($escape) {
                $key = self::escape($key);
                $val = self::escape($val);
            }
            $pairs[] = sprintf($format, $key, $val);
        }

        //Return an array if no glue provided
        if ($glue === null) {
            return $pairs;
        }

        return implode($glue, $pairs);
    }

    public static function escape(string $string): string
    {
        return rawurlencode($string);
    }

    public static function getPackageVersion(): string
    {
        if (!is_callable('\\Composer\\InstalledVersions::getPrettyVersion')) {
            return self::ENKAP_CLIENT_VERSION;
        }
        try {
            $version = InstalledVersions::getPrettyVersion(self::PACKAGE_NAME);
        } catch (Throwable) {
            $version = self::ENKAP_CLIENT_VERSION;
        }

        return $version;
    }

    public static function camelize(string $string, bool $capitalizeFirstCharacter = false): string
    {
        /** @var string $str */
        $str = str_replace('_', '', ucwords($string, '_'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }

    public static function getOderMerchantIdFromUrl(): string
    {
        if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
            $protocol = 'http';
        } else {
            $protocol = 'https';
        }
        $url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $urlPath = rtrim(parse_url($url, PHP_URL_PATH), '/');
        $urlExploded = explode('/', $urlPath);
        $referenceId = array_pop($urlExploded);

        return self::satanise($referenceId);
    }

    private static function stripAllTags(string $string): string
    {
        $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
        $string = strip_tags($string);

        return trim($string);
    }
}
