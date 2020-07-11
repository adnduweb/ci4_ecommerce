<?php

namespace Adnduweb\Ci4_ecommerce\Config;


use CodeIgniter\Config\Services as CoreServices;
use CodeIgniter\Config\BaseConfig;

use CodeIgniter\Model;
use Michalsn\Uuid\UuidModel;
use Adnduweb\Ci4_ecommerce\Authorization\FlatAuthorization;
use Adnduweb\Ci4_ecommerce\Models\CustomerModel;
use Adnduweb\Ci4_ecommerce\Models\LoginModel;
use Adnduweb\Ci4_ecommerce\Authorization\GroupModel;
use Adnduweb\Ci4_ecommerce\Authentication\Passwords\PasswordValidator;
use Adnduweb\Ci4_ecommerce\Authentication\Activators\CustomerActivator;
use Adnduweb\Ci4_ecommerce\Authentication\Resetters\CustomerResetter;

class Services extends CoreServices
{
    public static function cart($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cart');
        }

        //  If no config was injected then load one
        // Prioritizes app/Config if found
        if (empty($config))
            $config = config('Visits');

        return new \Adnduweb\Ci4_ecommerce\Cart();
    }
}
