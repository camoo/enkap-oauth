<?php

declare(strict_types=1);

namespace Enkap\OAuth\Interfaces;

interface ModelInterface
{
    /** The original name of the model. */
    public function getModelName(): string;

    public function getResourceURI(): string;

    /**
     * Returns the properties of the model.
     * The keys are the property names, and the values are their corresponding values.
     *
     * @return array<string, mixed>
     */
    public static function getProperties(): array;

    /**
     * Returns the list of supported HTTP methods for this model.
     * The keys are the HTTP methods (e.g., 'GET', 'POST', 'PUT', 'DELETE'),
     *  and the values are the descriptions of what each method does.
     *                         For example:
     *                         [
     *                         'GET' => 'Retrieve a resource',
     *                         'POST' => 'Create a new resource',
     *                         'PUT' => 'Update an existing resource',
     *                         'DELETE' => 'Delete a resource',
     *                         ]
     *
     * @return array|string[]
     */
    public static function getSupportedMethods(): array;
}
