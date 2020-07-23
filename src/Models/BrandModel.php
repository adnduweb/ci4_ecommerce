<?php

/*
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace Adnduweb\Ci4_ecommerce\Models;

use CodeIgniter\Model;
use Adnduweb\Ci4_ecommerce\Entities\Brand;

/**
 * Class CategoryModel
 *
 * @package App\Models
 */
class BrandModel extends Model
{
    use \Tatter\Relations\Traits\ModelTrait, \Adnduweb\Ci4_logs\Traits\AuditsTrait, \App\Models\BaseModel;

    /**
     * @var \CodeIgniter\Database\BaseBuilder
     */
    private $brands;

    protected $afterInsert        = ['auditInsert'];
    protected $afterUpdate        = ['auditUpdate'];
    protected $afterDelete        = ['auditDelete'];
    protected $table              = 'ec_brands';
    protected $tableLang          = 'ec_brands_langs';
    protected $primaryKey         = 'id';
    protected $primaryKeyLang     = 'brand_id';
    protected $tableP             = 'ec_products';
    protected $tablePLang         = 'ec_products_langs';
    protected $primaryKeyP        = 'id';
    protected $primaryKeyPLang    = 'product_id';
    protected $with               = ['ec_brands_langs'];
    protected $without            = [];
    protected $returnType         = Brand::class;
    protected $localizeFile       = 'Adnduweb\Ci4_ecommerce\Models\BrandModel';
    protected $useSoftDeletes     = true;
    protected $allowedFields      = ['name', 'active'];
    protected $useTimestamps      = true;
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $searchKtDatatable  = ['name', 'description_short', 'created_at'];
 
    /**
     * Site constructor.
     *
     * @param array ...$params
     *
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function __construct(...$params)
    {
        parent::__construct(...$params);
        $this->builder           = $this->db->table('ec_brands');
        $this->builder_lang      = $this->db->table('ec_brands_langs');
        $this->ec_products       = $this->db->table('ec_products');
        $this->ec_products_langs = $this->db->table('ec_products_langs');
    }

    public function getAllCategoriesOptionParent()
    {
        $instance = [];
        $this->builder->select($this->table . '.' . $this->primaryKey . ', slug, name, created_at');
        $this->builder->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.' . $this->primaryKeyLang);
        $this->builder->where('deleted_at IS NULL AND id_lang = ' . service('switchlanguage')->getIdLocale());
        $this->builder->orderBy($this->table . '.' . $this->primaryKey . ' DESC');
        $categoriess = $this->builder->get()->getResult();
        //echo $this->builder->getCompiledSelect(); exit;
        // var_dump($categoriess);
        // exit;
        if (!empty($categoriess)) {
            foreach ($categoriess as $categories) {
                $instance[] = new Brand((array) $categories);
            }
        } 
        return $instance;
    }

    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $categoriesRow = $this->getBaseAllList($page, $perpage, $sort, $query, $this->searchKtDatatable);;

        // In va chercher les products
        if (!empty($categoriesRow)) {
            $i = 0;
            foreach ($categoriesRow as $category) {
                $categoriesRow[$i]->count_product = $this->changeItemIncat($category->{$this->primaryKey})->id;
                $LangueDisplay = [];
                foreach (service('switchlanguage')->getArrayLanguesSupported() as $k => $v) {
                    $LangueDisplay[$k] = $this->getLanguesDispo($category->{$this->primaryKey}, $v);
                }
                $categoriesRow[$i]->languages = $LangueDisplay;
                $i++;
            }
        }

        return $categoriesRow;
    }

    public function getAllCount(array $sort, array $query)
    {
        $this->builder->select($this->table . '.' . $this->primaryKey);
        $this->builder->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.' . $this->primaryKeyLang);
        if (isset($query[0]) && is_array($query)) {
            $this->builder->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('switchlanguage')->getIdLocale());
        } else {
            $this->builder->where('deleted_at IS NULL AND id_lang = ' . service('switchlanguage')->getIdLocale());
        }

        $this->builder->orderBy($sort['field'] . ' ' . $sort['sort']);

        $pages = $this->builder->get();
        //echo $this->builder->getCompiledSelect(); exit;
        return $pages->getResult();
    }

    /****
     *
     * Il ya des produits dans cette categories ?
     */
    public function changeItemIncat(int $id)
    {
        $this->ec_products->selectCount($this->tableP . '.' . $this->primaryKeyP);
        $this->ec_products->where('deleted_at IS NULL AND '.$this->primaryKeyLang.' = ' . $id);
        return $this->ec_products->get()->getRow();
    }



    /**
     * @return array
     */
    public function getlist(): array
    {
        $this->builder->select();
        $this->builder->where('id_parent', '0');
        $this->builder->orderBy('id', 'ASC');

        $categories =  $this->builder->get()->getResult('array');
        $instance  = [];
        if (!empty($categories)) {
            foreach ($categories as $categorie) {
                $instance[] = new Category($categorie);
            }
        }
        return $instance;
    }

    public function updatePostCategorie(array $data)
    {

        //print_r($data);
        foreach ($data as $subdata) {

            $this->ec_products->select($this->tableP . '.' . $this->primaryKeyP);
            $this->ec_products->where('deleted_at IS NULL AND  id_category_default = ' . $subdata['old_categorie']);
            $listProducts = $this->ec_products->get()->getResult();
            if (!empty($listProducts)) {
                foreach ($listProducts as $post) {
                    //print_r([$this->primaryKeyPLang => $post->{$this->primaryKeyPLang}]); 
                    // print_r($post);
                    // exit;
                    $this->ec_products_categories->delete([$this->primaryKeyPLang => $post->{$this->primaryKey}]);
                    // print_r([$this->primaryKeyPLang => $post->{$this->primaryKey}]); 
                    // exit;

                    $tab = ['id_category_default' => $subdata['new_categorie_id']];
                    $this->ec_products->set($tab);
                    $this->ec_products->where([$this->primaryKeyP => $post->{$this->primaryKeyP}]);
                    //echo $this->ec_products->getCompiledUpdate();
                    $this->ec_products->update();

                    $this->ec_products_categories->insert([$this->primaryKeyPLang => $post->{$this->primaryKey}, $this->primaryKeyLang => $subdata['new_categorie_id']]);
                }
            }
        }
    }

    //     /**
    //      * @param int $id
    //      * @param string $column
    //      * @param string $data
    //      *
    //      * @return bool
    //      */
    //     public function UpdateCategories(int $id, string $column, string $data): bool
    //     {
    //         $this->builder->set($column, $data);
    //         $this->builder->where('id_category', $id);
    //         $this->builder->update();

    //         return true;
    //     }

    //     /**
    //      * @param string $title
    //      * @param string $content
    //      * @param string $slug
    //      * @param string $icon
    //      */
    //     public function AddCategories(string $title, string $content, string $slug, string $icon)
    //     {
    //         $data = [
    //             'title'       => $title,
    //             'description' => $content,
    //             'slug'        => $slug,
    //             'icon'        => $icon
    //         ];
    //         $this->builder->insert($data);
    //     }


    //     public function getNameCat(int $id_category, int $id_lang): string
    //     {
    //         $this->builder_lang->select('name');
    //         $this->builder_lang->where(['id_category' => $id_category, 'id_lang' => $id_lang]);
    //         return $this->builder_lang->get()->getRow()->name;
    //     }

    //     public function changeec_productsIncat(int $id_category)
    //     {

    //         $this->article_categorie->select();
    //         $this->article_categorie->where(['id_category' => $id_category]);
    //         $article_categorie = $this->article_categorie->get()->getResult();

    //         // On met par default les relatiosn ec_brands
    //         if (!empty($article_categorie)) {
    //             foreach ($article_categorie as $article) {
    //                 // ON supprime cette ec_brands des ec_products
    //                 $this->article_categorie->delete(['id_post' => $article->id_post, 'id_category' => $id_category]);
    //                 $this->article_categorie->delete(['id_post' => $article->id_post, 'id_category' => $this->id_category_default]);


    //                 $this->article_categorie->set(['id_category' => $this->id_category_default]);
    //                 $this->article_categorie->where('id_category', $id_category);

    //                 $data = [
    //                     'id_post'   =>  $article->id_post,
    //                     'id_category' => $this->id_category_default,
    //                     'created_at' => date('Y-m-d H:i:s'),
    //                 ];
    //                 try {
    //                     $this->article_categorie->insert($data);
    //                 } catch (\Exception $e) {
    //                     return $this->db->error()['code'];
    //                 }
    //             }
    //         }

    //         $this->ec_products->select('id_category_default');
    //         $this->ec_products->where(['id_category_default' => $id_category]);
    //         $ec_products = $this->builder_lang->get()->getResult();

    //         // On met par default les relatiosn ec_brands
    //         if (!empty($ec_products)) {
    //             foreach ($ec_products as $article) {
    //                 $this->ec_products->set(['id_category_default' => $this->id_category_default]);
    //                 $this->ec_products->where('id_category_default', $id_category);
    //                 $this->ec_products->update();
    //             }
    //         }
    //     }


    //     public function getAllCat()
    //     {
    //         $this->builder->select($this->table . '.' . $this->primaryKey . ', name');
    //         $this->builder->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.' . $this->primaryKeyLang);
    //         $this->builder->where('deleted_at IS NULL AND id_lang = ' . service('switchlanguage')->getIdLocale());
    //         return $this->builder->get()->getResult();
    //     }
    // }
}
