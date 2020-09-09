<?php

namespace Adnduweb\Ci4_ecommerce\Controllers\Admin;

use App\Controllers\Admin\AdminController;
use App\Libraries\AssetsBO;
use App\Libraries\Tools;
use Adnduweb\Ci4_ecommerce\Entities\Product;
use Adnduweb\Ci4_ecommerce\Models\ProductModel;
use Adnduweb\Ci4_ecommerce\Models\CategoryModel;
use Adnduweb\Ci4_ecommerce\Models\BrandModel;
use Adnduweb\Ci4_ecommerce\Models\SupplierModel;
use App\Models\CurrencyModel;
use App\Models\TaxeModel;
use \CodeIgniter\Test\Fabricator;

/**
 * Class Article
 *
 * @package App\Controllers\Admin
 */
class AdminProductController extends AdminController
{

    use \App\Traits\BuilderModelTrait, \App\Traits\ModuleTrait;


        /**
     *  Module Object
     */
    public $module = true;

    /**
     * name controller
     */
    public $controller = 'product';

    /**
     * Localize slug
     */
    public $pathcontroller  = '/catalogue/product';

    /**
     * Localize namespace
     */
    public $namespace = 'Adnduweb/Ci4_ecommerce';

    /**
     * Id Module
     */
    protected $idModule;

    /**
     * Localize slug
     */
    public $dirList  = 'ecommerce';

    /**
     * Display default list column
     */
    public $fieldList = 'ec_products.id';

    /**
     * Bouton add
     */
    public $add = true;

    /**
     * Display Multilangue
     */
    public $multilangue = true;

    /**
     * Event fake data
     */
    public $fake = true;

    /**
     * Update item List
     */
    public $toolbarUpdate = true;

    /**
     * Change Categorie
     */
    public $changeCategorie = true;


    /**
     * @var Adnduweb\Ci4_ecommerce\Models\ProductModel
     */
    public $tableModel;

    /**
     * @var Adnduweb\Ci4_ecommerce\Models\CategoryModel
     */
    public $category_model;


    /**
     * Product constructor.
     *
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function __construct()
    {
        helper('number');
        parent::__construct();
        $this->tableModel       = new ProductModel();
        $this->category_model = new CategoryModel();
        $this->idModule         = $this->getIdModule();
        $this->data['currencyDefault']  = (new CurrencyModel())->find(service('settings')->setting_devise_default);
        $this->data['taxes']  = (new TaxeModel())->findAll();

        $this->data['paramJs']['baseSegmentAdmin'] = config('Ecommerce')->urlMenuAdmin;
        $this->pathcontroller  = '/' . config('Ecommerce')->urlMenuAdmin . $this->pathcontroller;

    }


    public function renderViewList()
    {
        AssetsBO::add_js([$this->get_current_theme_view('controllers/' . $this->dirList . '/js/listProduct.js', 'default')]);
        $this->data['categories'] = $this->category_model->getAllCategoriesOptionParent();
        $parent =  parent::renderViewList();
        if (is_object($parent) && $parent->getStatusCode() == 307) {
            return $parent;
        }

        return $parent;
    }


    public function ajaxProcessList()
    {
        $parent = parent::ajaxProcessList();
        return $this->respond($parent, 200, lang('Core.liste des produits'));
    }

    public function renderForm($id = null)
    {
        AssetsBO::add_js([$this->get_current_theme_view('plugins/custom/ckeditor/ckeditor-classic.bundle.js', 'default')]);
        AssetsBO::add_js([$this->get_current_theme_view('controllers/medias/js/manager.js', 'default')]);
      //  AssetsBO::add_js(['https://cdn.jsdelivr.net/npm/vue/dist/vue.js']);
        AssetsBO::add_js(['admin/vuejs/products/dist/app.js'], 'vueJs'); 
        //AssetsBO::add_js(['admin/vuejs/src/controllers/ecommerce/product.js'], 'vueJs');


        if (is_null($id)) {
            $this->data['form'] = new Product($this->request->getPost());
        } else {
            $this->data['form'] = $this->tableModel->where('id', $id)->first();
            if (empty($this->data['form'])) {
                Tools::set_message('danger', lang('Core.not_{0}_exist', [$this->controller]), lang('Core.warning_error'));
                return redirect()->to('/' . env('CI_SITE_AREA') . $this->pathcontroller);
            }
        }
        $this->data['form']->allCategories = $this->category_model->getAllCategoriesOptionParent();
        $this->data['form']->categories    = $this->category_model->getlist();
        $this->data['brands']              = (new BrandModel())->findAll();
        $this->data['suppliers']           = (new SupplierModel())->findAll();
        //$this->data['form']->getCatByProduct = $this->tableModel->getCatByProduct($id);

        parent::renderForm($id);
        $this->data['edit_title'] = lang('Core.edit_product');
        return view($this->get_current_theme_view('form', $this->namespace), $this->data);
    }

    public function postProcessEdit($param)
    {
        
        $this->validation->setRules(['lang.1.slug' => 'required']);
        if (!$this->validation->run($this->request->getPost())) {
            Tools::set_message('danger', $this->validation->getErrors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // Try to create the user
        $productBase = new Product($this->request->getPost());
        $this->lang = $this->request->getPost('lang');
        $productBase->id_category = $productBase->id_category_default;
        $productBase->id_category_default = $productBase->id_category_default[0];
        $productBase->shop_id = 1;
        $productBase->price = ps_round($productBase->price,2);
        //$productBase->price = number_to_currency($productBase->price, 'USD', null, 2);

        // Les images
        $productBase->picture_one = $this->getImagesPrep($productBase->getPictureOneAtt());
        $productBase->picture_header = $this->getImagesPrep($productBase->getPictureheaderAtt());

        //print_r($productBase); exit;

        if (!$this->tableModel->save($productBase)) {
            Tools::set_message('danger', $this->tableModel->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // On enregistre les categories
        $productBase->saveCategorie($productBase);

        // On enregistre les langues
        $productBase->saveLang($this->lang, $productBase->{$this->tableModel->primaryKey});

        // On enregistre le Builder si existe
        $this->saveBuilder($this->request->getPost('builder'));


        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . $this->pathcontroller,
            'action'                => 'edit',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $productBase->{$this->tableModel->primaryKey},
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    public function postProcessAdd()
    {

        $this->validation->setRules(['lang.1.slug' => 'required']);
        if (!$this->validation->run($this->request->getPost())) {
            Tools::set_message('danger', $this->validation->getErrors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }

        // Try to create the user
        $productBase = new Product($this->request->getPost());
        $this->lang = $this->request->getPost('lang');
        $productBase->id_category = $productBase->id_category_default;
        $productBase->id_category_default = $productBase->id_category_default[0];
        $productBase->shop_id = 1;

        // Les images
        $productBase->picture_one = $this->getImagesPrep($productBase->getPictureOneAtt());
        $productBase->picture_header = $this->getImagesPrep($productBase->getPictureheaderAtt());

        // print_r($this->tableModel->save($productBase));
        // print_r($productBase);
        // exit;

        if (!$this->tableModel->save($productBase)) {
            Tools::set_message('danger', $this->tableModel->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }
        $id_product = $this->tableModel->insertID();
        $productBase->id = $id_product;

        // On enregistre les categories
        $productBase->saveCategorie($productBase);

        // On enregistre les langues
        $this->lang = $this->request->getPost('lang');
        $productBase->saveLang($this->lang, $id_product);

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . $this->pathcontroller,
            'action'                => 'add',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $id_product,
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    public function getImagesPrep($imageJson)
    {
        $options  = [];
        if (!empty($imageJson) || !is_null($imageJson)) {

            if (!in_array($imageJson->media->format, ['thumbnail', 'small', 'medium', 'large'])) {
                if (strpos($imageJson->media->filename, 'custom') === false) {
                    $oldName = pathinfo($imageJson->media->filename);
                    $imageJson->media->filename = base_url() . '/uploads/custom/' . $imageJson->media->format;
                    $imageJson->media->class = $oldName['filename'];
                    $imageJson->media->format = 'custom';
                }
            } else {
                $imageJson->media->class = $imageJson->media->format;
            }

            try {
                $client = \Config\Services::curlrequest();
                $response = $client->request('GET', $imageJson->media->filename);
                list($width, $height, $type, $attr) =  getimagesize($imageJson->media->filename);
                $imageJson->media->dimensions = ['width' => $width, 'height' => $height];
                $options = json_encode($imageJson);
            } catch (\Exception $e) {
                $options = '';
            }
        } else {
            $options = '';
        }
        return $options;
    }

    public function dupliquer(int $id_product)
    {
        try {
            $this->tableModel->dupliquer($id_product);
            Tools::set_message('success', lang('Core.save_data_dupliquer'), lang('Core.cool_success'));
            return redirect()->to('/' . CI_SITE_AREA . $this->pathcontroller);
        } catch (\Exception $e) {
            // print_r($e);
            // exit;
            Tools::set_message('danger', str_replace('::', '->', $e->getMessage()), lang('Core.warning_error'));
            return redirect()->to('/' . CI_SITE_AREA . $this->pathcontroller);
        }
    }

    public function ajaxProcessUpdate()
    {
        if ($value = $this->request->getPost('value')) {
            $data = [];
            if (isset($value['selected']) && !empty($value['selected'])) {
                foreach ($value['selected'] as $selected) {

                    $data[] = [
                        'id' => $selected,
                        'active'     => $value['active'],
                    ];
                }
            }

            if ($this->tableModel->updateBatch($data, 'id')) {
                return $this->respond(['status' => true, 'message' => lang('Js.your_seleted_records_statuses_have_been_updated')], 200);
            } else {
                return $this->respond(['status' => false, 'database' => true, 'display' => 'modal', 'message' => lang('Js.aucun_enregistrement_effectue')], 200);
            }
        }
    }


    public function ajaxProcessDelete()
    {
        if ($value = $this->request->getPost('value')) {
            $data = [];
            if (isset($value['selected']) && !empty($value['selected'])) {
                foreach ($value['selected'] as $selected) {
                    $this->tableModel->delete(['id_product' => $selected]);
                }
                return $this->respond(['status' => true, 'type' => 'success', 'message' => lang('Js.your_selected_records_have_been_deleted')], 200);
            }
        }
        die(1);
    }

    public function ajaxProcessChangeCategorie()
    {
        if ($value = $this->request->getPost('value')) {
            $data = [];
            // print_r($value); exit;

            $categorie         = $value['categorie'];
            $categorieOriginal = $value['categorieOriginal'];
            $newCategorieOriginal = [];
            if (strpos($categorieOriginal, ',') == true) {
                $newCategorieOriginal = explode(',', $categorieOriginal);
            } else {
                $newCategorieOriginal = (array) $categorieOriginal;
            }
            // print_r($newCategorieOriginal); exit;
            if (is_array($newCategorieOriginal)) {
                foreach ($newCategorieOriginal as $categorieO) {
                    $data[] = [
                        'id_product'    => $categorieO,
                        'new_categorie_id' => $categorie,
                    ];
                }
            }
            //print_r($data); exit;
            $this->tableModel->updatePostCategorie($data);
            return $this->respond(['status' => true, 'type' => 'success', 'message' => lang('Js.your_seleted_records_statuses_have_been_updated')], 200);
        }
    }


    /**
     * 
     * Fake Product
     */
    public function fake(int $num = 10)
    {

        $fabricator = new Fabricator($this->tableModel);
        $makeArticle   = $fabricator->make($num);
        $products = $fabricator->create($num);
        if (!empty($products)) {
            foreach ($products as $product) {
                $this->tableModel->fakelang($product->id);
            }
        }
        Tools::set_message('success', lang('Core.fakedata'), lang('Core.cool_success'));
        return redirect()->to('/' . CI_SITE_AREA . $this->pathcontroller);
    }
}
