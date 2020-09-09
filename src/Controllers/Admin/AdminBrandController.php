<?php

namespace Adnduweb\Ci4_ecommerce\Controllers\Admin;

use App\Controllers\Admin\AdminController;
use App\Libraries\AssetsBO;
use App\Libraries\Tools;
use Adnduweb\Ci4_ecommerce\Entities\Brand;
use Adnduweb\Ci4_ecommerce\Models\ProductModel;
use Adnduweb\Ci4_ecommerce\Models\BrandModel;

/**
 * Class Article
 *
 * @package App\Controllers\Admin
 */
class AdminBrandController extends AdminController
{

    use \App\Traits\BuilderModelTrait, \App\Traits\ModuleTrait;


    /**
     *  Module Object
     */
    public $module = true;

    /**
     * name controller
     */
    public $controller = 'brand';

    /**
     * Localize slug
     */
    public $pathcontroller  = '/catalogue/brand';

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
    public $fieldList = 'name';

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
    public $fake = false;

    /**
     * Update item List
     */
    public $toolbarUpdate = true;

    /**
     *  Bool Export
     */
    public$toolbarExport   = false;

    /**
     * Change Categorie
     */
    public $changeCategorie = true;


    /**
     * @var \Adnduweb\Ci4_ecommerce\Models\BrandModel
     */
    public $tableModel;

    /**
     * @var \Adnduweb\Ci4_ecommerce\Models\ProductModel
     */
    private $product_model;

    /**
     * Marque constructor.
     *
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function __construct()
    {
        parent::__construct();
        $this->tableModel     = new BrandModel();
        $this->product_model = new ProductModel();
        $this->idModule       = $this->getIdModule();

        $this->data['paramJs']['baseSegmentAdmin'] = config('Ecommerce')->urlMenuAdmin;
        $this->pathcontroller  = '/' . config('Ecommerce')->urlMenuAdmin . $this->pathcontroller;
    }


    public function renderViewList()
    {
        AssetsBO::add_js([$this->get_current_theme_view('controllers/' . $this->dirList . '/js/listBrand.js', 'default')]);
        helper('form');

        if (!has_permission(ucfirst($this->controller) . '::views', user()->id)) {
            Tools::set_message('danger', lang('Core.not_acces_permission'), lang('Core.warning_error'));
            return redirect()->to('/' . CI_SITE_AREA . '/dashboard');
        }
        $this->data['nameController']    = lang('Core.' . $this->controller);
        $this->data['addPathController'] = $this->pathcontroller . '/add';
        $this->data['toolbarUpdate']     = $this->toolbarUpdate;
        $this->data['changeCategorie']   = $this->changeCategorie;
        $this->data['fakedata']          = $this->fake;
        $this->data['toolbarExport']     = $this->toolbarExport;
        if (isset($this->add) && $this->add == true)
            $this->data['add'] = lang('Core.add_' . $this->controller);
        $this->data['countList'] = $this->tableModel->getAllCount(['field' => $this->fieldList, 'sort' => 'ASC'], []);
        $this->data['categories'] = $this->tableModel->getAllCategoriesOptionParent();

        return view($this->get_current_theme_view('brand/index', $this->namespace), $this->data);
    }


    public function ajaxProcessList()
    {
        $parent = parent::ajaxProcessList();
        return $this->respond($parent, 200, lang('Core.liste des categories'));
    }

    public function renderForm($id = null)
    {
        if (is_null($id)) {
            $this->data['form'] = new Brand($this->request->getPost());
        } else {
            $this->data['form'] = $this->tableModel->where('id', $id)->first();
            if (empty($this->data['form'])) {
                Tools::set_message('danger', lang('Core.not_{0}_exist', [$this->controller]), lang('Core.warning_error'));
                return redirect()->to('/' . env('CI_SITE_AREA') . '/public/blog/categories');
            }
        }
        AssetsBO::add_js([$this->get_current_theme_view('plugins/custom/ckeditor/ckeditor-classic.bundle.js', 'default')]);
        $this->data['form']->id_module = $this->idModule;
        $this->data['form']->id_item = $id;

        parent::renderForm($id);
        $this->data['edit_title'] = lang('Core.edit_categorie');
        return view($this->get_current_theme_view('brand/form', $this->namespace), $this->data);
    }

    public function postProcessEdit($param)
    {

        // Try to create the user
        $categorieBase = new Brand();
        $categorieBase->fill($this->request->getPost());
        $this->lang = $this->request->getPost('lang');
        $categorieBase->active = isset($categorieBase->active) ? 1 : 0;
       
        if (!$this->tableModel->save($categorieBase)) {
            Tools::set_message('danger', $this->tableModel->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }
        $categorieBase->saveLang($this->lang, $categorieBase->{$this->tableModel->primaryKey});

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . $this->pathcontroller,
            'action'                => 'edit',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $categorieBase->{$this->tableModel->primaryKey},
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    public function postProcessAdd($param)
    {

        $this->validation->setRules(['lang.1.slug' => 'required']);
        if (!$this->validation->run($this->request->getPost())) {
            Tools::set_message('danger', $this->validation->getErrors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }
        
        // Try to create the user
        $categorieBase = new Brand();
        $categorieBase->fill($this->request->getPost());
        $this->lang = $this->request->getPost('lang');

        if (!$this->tableModel->save($categorieBase)) {
            Tools::set_message('danger', $this->tableModel->errors(), lang('Core.warning_error'));
            return redirect()->back()->withInput();
        }
        $id_category = $this->tableModel->insertID();
        $categorieBase->saveLang($this->lang, $id_category);

        // Success!
        Tools::set_message('success', lang('Core.save_data'), lang('Core.cool_success'));
        $redirectAfterForm = [
            'url'                   => '/' . env('CI_SITE_AREA') . $this->pathcontroller,
            'action'                => 'add',
            'submithandler'         => $this->request->getPost('submithandler'),
            'id'                    => $id_category,
        ];
        $this->redirectAfterForm($redirectAfterForm);
    }

    public function ajaxProcessUpdate()
    {
        if ($value = $this->request->getPost('value')) {
            $data = [];
            if (isset($value['selected']) && !empty($value['selected'])) {
                foreach ($value['selected'] as $selected) {

                    $data[] = [
                        'id'     => $selected,
                        'active' => $value['active'],
                    ];
                }
            }
            if ($this->tableModel->updateBatch($data, 'id')) {
                return $this->respond(['status' => true, 'type' => 'success', 'message' => lang('Js.your_seleted_records_statuses_have_been_updated')], 200);
            } else {
                return $this->respond(['status' => false, 'database' => true, 'display' => 'modal', 'message' => lang('Js.aucun_enregistrement_effectue')], 200);
            }
        }
    }

    public function ajaxProcessDelete()
    {
        if ($value = $this->request->getPost('value')) {
            if (!empty($value['selected'])) {
                $default = false;

                foreach ($value['selected'] as $id) {
                    if ($id == '1') {
                        $default = true;
                        break;
                    } else {
                        // On regarde si le groupe est déja affecté
                        //print_r($this->tableModel->changeItemIncat($id)); exit;
                        if ($this->tableModel->changeItemIncat($id) == '0') {
                            $this->tableModel->delete($id);
                        } else {
                            return $this->respond(['status' => false, 'type' => 'warning', 'message' => lang('Js.not_action_because_item_cat')], 200);
                        }
                    }
                }
                if ($default == true) {
                    return $this->respond(['status' => false, 'type' => 'warning', 'message' => lang('Js.not_delete_group')], 200);
                } else {
                    return $this->respond(['status' => true, 'type' => 'success', 'message' => lang('Js.your_selected_records_have_been_deleted')], 200);
                }
            }
        }
        return $this->failUnauthorized(lang('Js.not_autorized'), 400);
    }

}
