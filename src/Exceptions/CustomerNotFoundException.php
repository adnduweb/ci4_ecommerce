<?php

namespace Adnduweb\Ci4_ecommerce\Exceptions;

class CustomerNotFoundException extends \RuntimeException implements ExceptionInterface
{
    public static function forCustomerID(int $id)
    {
        return new self(lang('Authcustomer.customerrNotFound', [$id]), 404);
    }
}
