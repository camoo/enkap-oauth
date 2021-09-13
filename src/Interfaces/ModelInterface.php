<?php
declare(strict_types=1);

namespace Enkap\OAuth\Interfaces;

interface ModelInterface
{

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName() : string;

}
