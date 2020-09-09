<?php

namespace Adnduweb\Ci4_ecommerce\Config;

use CodeIgniter\Config\BaseConfig;

class Ecommerce extends BaseConfig
{
    //--------------------------------------------------------------------
    //Url de l'admin
    //--------------------------------------------------------------------
    public $urlMenuAdmin = 'ecommerce';

    //--------------------------------------------------------------------
    // Délai des Nouveaux produits sur le front
    //--------------------------------------------------------------------
    public $newProduct = 15 * DAY;

    //--------------------------------------------------------------------
    // Décimal ds tarifs des produits
    //--------------------------------------------------------------------
    public $precisionDecimal = 2;

    //--------------------------------------------------------------------
    // Nombre de produits par page
    //--------------------------------------------------------------------
    public $pagination = 12;

    //--------------------------------------------------------------------
    // Activation de l'ajout des produits sur les pages catégories
    //--------------------------------------------------------------------
    public $add_to_cart_listing = false;

}
