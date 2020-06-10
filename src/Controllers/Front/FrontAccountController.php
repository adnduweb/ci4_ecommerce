<?php

namespace Adnduweb\Ci4_ecommerce\Controllers\Front;

use CodeIgniter\API\ResponseTrait;
use Adnduweb\Ci4_ecommerce\Entities\Cart;
use Adnduweb\Ci4_ecommerce\Entities\Order;
use Adnduweb\Ci4_ecommerce\Entities\Carrier;
use Adnduweb\Ci4_ecommerce\Entities\Customer;
use Adnduweb\Ci4_ecommerce\Models\CartModel;
use Adnduweb\Ci4_ecommerce\Models\OrderModel;
use Adnduweb\Ci4_ecommerce\Models\CarriersModel;
use Adnduweb\Ci4_ecommerce\Models\CustomerModel;

class FrontAccountController extends FrontEcommerceController
{

    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        //Silent
        $this->data['page'] = new Customer();
        // print_r($this->data['page']);

        $this->data['no_follow_no_index'] = 'index follow';
        $this->data['id']  = 'authentification';
        $this->data['class'] = $this->data['class'] . ' authentification';
        $this->data['meta_title'] = '';
        $this->data['meta_description'] = '';
        $this->data['config'] = $this->config;

        // On regarde si on est connecté

        //Si oui
        //On redirige

        //Si non connexion
        // $this->data = [];
        return view($this->get_current_theme_view('__template_part/ecommerce/my-account', 'default'), $this->data);
    }
}
