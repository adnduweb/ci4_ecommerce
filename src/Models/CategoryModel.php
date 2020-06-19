<?php

/*
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace Adnduweb\Ci4_ecommerce\Models;

use CodeIgniter\Model;
use Adnduweb\Ci4_ecommerce\Entities\Category;

/**
 * Class CategoryModel
 *
 * @package App\Models
 */
class CategoryModel extends Model
{
    /**
     * @var \CodeIgniter\Database\BaseBuilder
     */
    private $categories;

    use \Tatter\Relations\Traits\ModelTrait;
    use \Adnduweb\Ci4_logs\Traits\AuditsTrait;
    protected $afterInsert          = ['auditInsert'];
    protected $afterUpdate          = ['auditUpdate'];
    protected $afterDelete          = ['auditDelete'];
    protected $table                = 'ec_category';
    protected $tableLang            = 'ec_category_lang';
    protected $with                 = ['ec_category_lang'];
    protected $without              = [];
    protected $primaryKey           = 'id_category';
    protected $returnType           = Category::class;
    protected $useSoftDeletes       = true;
    protected $allowedFields        = ['id_parent', 'active', 'order'];
    protected $useTimestamps        = true;
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $id_category_default = 1;

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
        $this->ec_product        = $this->db->table('ec_product');
        $this->ec_category       = $this->db->table('ec_category');
        $this->ec_category_lang  = $this->db->table('ec_category_lang');
        $this->article_categorie = $this->db->table('ec_product_categories');
    }

    public function getAllCategoriesOptionParent()
    {
        $instance = [];
        $this->ec_category->select($this->table . '.id_category, slug, name, id_parent, created_at');
        $this->ec_category->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_category');
        $this->ec_category->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_bo_id_lang);
        $this->ec_category->orderBy($this->table . '.id_category DESC');
        $categoriess = $this->ec_category->get()->getResult();
        //echo $this->ec_category->getCompiledSelect(); exit;
        // var_dump($categoriess);
        // exit;
        if (!empty($categoriess)) {
            foreach ($categoriess as $categories) {
                $instance[] = new Category((array) $categories);
            }
        }
        return $instance;
    }

    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $this->ec_category->select();
        $this->ec_category->select('created_at as date_create_at');
        $this->ec_category->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_category');
        if (isset($query[0]) && is_array($query)) {
            $this->ec_category->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
            $this->ec_category->limit(0, $page);
        } else {
            $this->ec_category->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
            $page = ($page == '1') ? '0' : (($page - 1) * $perpage);
            $this->ec_category->limit($perpage, $page);
        }


        $this->ec_category->orderBy($sort['field'] . ' ' . $sort['sort']);

        $groupsRow = $this->ec_category->get()->getResult();

        //echo $this->ec_category->getCompiledSelect(); exit;
        return $groupsRow;
    }

    public function getAllCount(array $sort, array $query)
    {
        $this->ec_category->select($this->table . '.' . $this->primaryKey);
        $this->ec_category->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_category');
        if (isset($query[0]) && is_array($query)) {
            $this->ec_category->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
        } else {
            $this->ec_category->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
        }

        $this->ec_category->orderBy($sort['field'] . ' ' . $sort['sort']);

        $pages = $this->ec_category->get();
        //echo $this->ec_category->getCompiledSelect(); exit;
        return $pages->getResult();
    }



    /**
     * @return array
     */
    public function getlist(): array
    {
        $this->ec_category->select();
        $this->ec_category->where('id_parent', '0');
        $this->ec_category->orderBy('id_category', 'ASC');

        $categories =  $this->ec_category->get()->getResult('array');
        $instance  = [];
        if (!empty($categories)) {
            foreach ($categories as $categorie) {
                $instance[] = new Category($categorie);
            }
        }
        return $instance;
    }

    /**
     * @param int $id
     * @param string $column
     * @param string $data
     *
     * @return bool
     */
    public function UpdateCategories(int $id, string $column, string $data): bool
    {
        $this->ec_category->set($column, $data);
        $this->ec_category->where('id_category', $id);
        $this->ec_category->update();

        return true;
    }

    /**
     * @param string $title
     * @param string $content
     * @param string $slug
     * @param string $icon
     */
    public function AddCategories(string $title, string $content, string $slug, string $icon)
    {
        $data = [
            'title'       => $title,
            'description' => $content,
            'slug'        => $slug,
            'icon'        => $icon
        ];
        $this->ec_category->insert($data);
    }


    public function getNameCat(int $id_category, int $id_lang): string
    {
        $this->ec_category_lang->select('name');
        $this->ec_category_lang->where(['id_category' => $id_category, 'id_lang' => $id_lang]);
        return $this->ec_category_lang->get()->getRow()->name;
    }

    public function changeec_productIncat(int $id_category)
    {

        $this->article_categorie->select();
        $this->article_categorie->where(['id_category' => $id_category]);
        $article_categorie = $this->article_categorie->get()->getResult();

        // On met par default les relatiosn ec_category
        if (!empty($article_categorie)) {
            foreach ($article_categorie as $article) {
                // ON supprime cette ec_category des ec_product
                $this->article_categorie->delete(['id_post' => $article->id_post, 'id_category' => $id_category]);
                $this->article_categorie->delete(['id_post' => $article->id_post, 'id_category' => $this->id_category_default]);


                $this->article_categorie->set(['id_category' => $this->id_category_default]);
                $this->article_categorie->where('id_category', $id_category);

                $data = [
                    'id_post'   =>  $article->id_post,
                    'id_category' => $this->id_category_default,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                try {
                    $this->article_categorie->insert($data);
                } catch (\Exception $e) {
                    return $this->db->error()['code'];
                }
            }
        }

        $this->ec_product->select('id_category_default');
        $this->ec_product->where(['id_category_default' => $id_category]);
        $ec_product = $this->ec_category_lang->get()->getResult();

        // On met par default les relatiosn ec_category
        if (!empty($ec_product)) {
            foreach ($ec_product as $article) {
                $this->ec_product->set(['id_category_default' => $this->id_category_default]);
                $this->ec_product->where('id_category_default', $id_category);
                $this->ec_product->update();
            }
        }
    }


    public function getAllCat()
    {
        $this->ec_category->select($this->table . '.' . $this->primaryKey . ', name');
        $this->ec_category->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_category');
        $this->ec_category->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
        return $this->ec_category->get()->getResult();
    }
}
