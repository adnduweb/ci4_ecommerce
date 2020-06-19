<?php

namespace Adnduweb\Ci4_blog\Controllers\Front;

use CodeIgniter\API\ResponseTrait;
use Adnduweb\Ci4_blog\Entities\Cart;
use Adnduweb\Ci4_blog\Entities\Order;
use Adnduweb\Ci4_blog\Entities\Carrier;
use Adnduweb\Ci4_blog\Models\CartModel;
use Adnduweb\Ci4_blog\Models\OrderModel;
use Adnduweb\Ci4_blog\Models\CarriersModel;

class FrontOrderController extends \App\Controllers\Front\FrontController
{
    use \App\Traits\BuilderModelTrait;
    use \App\Traits\ModuleTrait;

    public $name_module = 'blog';
    protected $idModule;

    public function __construct()
    {
        parent::__construct();
        $this->tableModel  = new PostModel();
        $this->idModule  = $this->getIdModule();
    }
    public function index()
    {
        //Silent
    }

    public function show($slug)
    {
    }
}
