<?php
/**
 * AzureLiveFacade
 */

namespace AzureLive\Laravel;

// use Aws\Common\Client\AwsClientInterface;
use Illuminate\Support\Facades\Facade;

/**
 * AzureLiveFacade
 */
class AzureLiveFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'azurelive';
    }
}
