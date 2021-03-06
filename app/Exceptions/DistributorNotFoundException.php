<?php

namespace App\Exceptions;

/**
 * Class DistributorNotFoundException
 *
 * @author  D3lph1 <d3lph1.contact@gmail.com>
 *
 * @package App\Exceptions
 */
class DistributorNotFoundException extends UnexpectedSettingsValueException
{
    public function __construct($distributor)
    {
        parent::__construct("Distributor with name '$distributor' not found");
    }
}
