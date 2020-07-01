<?php

namespace Adnduweb\Ci4_ecommerce\Config;

use CodeIgniter\Config\BaseConfig;

class Ecommerce extends BaseConfig
{
    public $authGoogle = true;

    public $authFaceBook = true;

    public $authLayer = true;

    public $newProduct = 15 * DAY;

    public $precisionDecimal = 2;

    public $pagination = 10;

    public $add_to_cart_listing = false;

}
