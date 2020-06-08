<?php

namespace Adnduweb\Ci4_ecommerce\Config;


use CodeIgniter\Config\Services as CoreServices;
use CodeIgniter\Config\BaseConfig;

class Services extends CoreServices
{
    public static function cart($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cart');
        }

        // If no config was injected then load one
        // Prioritizes app/Config if found
        if (empty($config))
            $config = config('Visits');

        return new \Adnduweb\Ci4_ecommerce\Cart();
    }
}
