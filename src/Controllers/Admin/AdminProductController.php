<?php

namespace Adnduweb\Ci4_ecommerce\Controllers\Admin;

use App\Controllers\Admin\AdminController;
use App\Libraries\AssetsBO;
use App\Libraries\Tools;
use Adnduweb\Ci4_ecommerce\Entities\Product;
use Adnduweb\Ci4_ecommerce\Models\ProductModel;
use Adnduweb\Ci4_ecommerce\Models\CategoryModel;

/**
 * Class Article
 *
 * @package App\Controllers\Admin
 */
class AdminProductController extends AdminController
{

    use \App\Traits\BuilderModelTrait;
    use \App\Traits\ModuleTrait;

    /**
     * @var \Adnduweb\Ci4_ecommerce\Models\ProductModel
     */
    public $tableModel;

    /**
     * @var \Adnduweb\Ci4_ecommerce\Models\CategoryModel
     */
    private $category_model;

    public $module = true;
    public $name_module = 'ecommerce';
    protected $idModule;
    public $controller = 'ecommerce';
    public $item = 'ecommerce';
    public $type = 'Adnduweb/Ci4_ecommerce';
    public $pathcontroller  = '/ecommerce/catalogue/product';
    public $fieldList = 'ec_product.id_product';
    public $add = true;
    public $multilangue = true;

    /**
     * Article constructor.
     *
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function __construct()
    {
        parent::__construct();
        $this->tableModel       = new ProductModel();
        $this->category_model = new CategoryModel();
        $this->module           = "blog";
        $this->idModule         = $this->getIdModule();
    }


    public function renderViewList()
    {
        AssetsBO::add_js([$this->get_current_theme_view('controllers/' . $this->controller . '/js/listProduct.js', 'default')]);
        $parent =  parent::renderViewList();
        if (is_object($parent) && $parent->getStatusCode() == 307) {
            return $parent;
        }

        return $parent;
    }


    public function ajaxProcessList()
    {
        $parent = parent::ajaxProcessList();
        return $this->respond($parent, 200, lang('Core.liste des articles'));
    }

    public function renderForm($id = null)
    {
        AssetsBO::add_js([$this->get_current_theme_view('plugins/custom/ckeditor/ckeditor-classic.bundle.js', 'default')]);
        AssetsBO::add_js([$this->get_current_theme_view('controllers/medias/js/manager.js', 'default')]);


        if (is_null($id)) {
            $this->data['form'] = new Product($this->request->getPost());
        } else {
            $this->data['form'] = $this->tableModel->where('id_product', $id)->first();
            if (empty($this->data['form'])) {
                Tools::set_message('danger', lang('Core.not_{0}_exist', [$this->item]), lang('Core.warning_error'));
                return redirect()->to('/' . env('CI_SITE_AREA') . '/' . $this->pathcontroller);
            }
        }
        $this->data['form']->allCategories = $this->category_model->getAllCategoriesOptionParent();
        $this->data['form']->categories =  $this->category_model->getlist();
        $this->data['form']->getCatByArt = $this->tableModel->getCatByArt($id);

        parent::renderForm($id);
        $this->data['edit_title'] = lang('Core.edit_article');
        return view($this->get_current_theme_view('form', 'Adnduweb/Ci4_ecommerce'), $this->data);
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
        $productBase->saveLang($this->lang, $productBase->id_product);

        // On enregistre le Builder si existe
        $this->saveBuilder($this->request->getPost('builder'));


        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . '/' . $this->pathcontroller,
            'action'                => 'edit',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $productBase->id_product,
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

        // Les images
        $productBase->picture_one = $this->getImagesPrep($productBase->getPictureOneAtt());
        $productBase->picture_header = $this->getImagesPrep($productBase->getPictureheaderAtt());

        // print_r($productBase);
        // exit;

        if (!$this->tableModel->save($productBase)) {
            Tools::set_message('danger', $this->tableModel->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }
        $id_product = $this->tableModel->insertID();
        $productBase->id_product = $id_product;

        // On enregistre les categories
        $productBase->saveCategorie($productBase);

        // On enregistre les langues
        $this->lang = $this->request->getPost('lang');
        $productBase->saveLang($this->lang, $id_product);

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . '/' . $this->pathcontroller,
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
                        'id_product' => $selected,
                        'active'     => $value['active'],
                    ];
                }
            }

            if ($this->tableModel->updateBatch($data, 'id_product')) {
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
}
