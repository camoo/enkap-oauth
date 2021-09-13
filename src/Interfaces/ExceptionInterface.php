<?php

namespace Enkap\OAuth\Interfaces;

use Symfony\Component\VarExporter\Exception\ExceptionInterface as BaseExceptionInterface;


/**
 * Class ExceptionInterface
 * @author CamooSarl
 */
interface ExceptionInterface extends BaseExceptionInterface
{

    /**
     * The HTTP status code
     *
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * Return headers
     *
     * @return array
     */
    public function getHttpHeaders(): array;

    /**
     * Data corresponding to the error
     *
     * @return array
     */
    public function getErrorData(): array;

    /**
     * A user-friendly error description
     *
     * @return string
     */
    public function getUserMessage(): string;
}
