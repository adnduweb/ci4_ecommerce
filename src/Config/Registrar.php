<?php

namespace Adnduweb\Ci4_ecommerce\Config;

use Config\Services;

/**
 * Helper class that will register our bulk plugins
 * and filters with the View Parser class.
 *
 * Called automatically by Config\View as long as
 * this file is setup as a Registrar:
 *
 *      protected $registrars = [
 *          \Myth\Template\Registrar::class
 *      ];
 *
 * @package Myth\Template
 */
class Registrar
{
    public static function View()
    {
        return [
            'plugins' => [
                'customer_logged_in' => [function ($str, array $params = []) {
                    return Services::authenticationcustomer()->check() ? $str : '';
                }],
                'customer_logged_out' => [function ($str, array $params = []) {
                    return !Services::authenticationcustomer()->check() ? $str : '';
                }],
            ]
        ];
    }
}
