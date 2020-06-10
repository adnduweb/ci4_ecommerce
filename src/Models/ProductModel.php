<?php

/*
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace Adnduweb\Ci4_ecommerce\Models;

use CodeIgniter\Model;
use Adnduweb\Ci4_ecommerce\Entities\Product;

/**
 * Class ProductModel
 *
 * @package App\Models
 */
class ProductModel extends Model
{
    /**
     * @var \CodeIgniter\Database\BaseBuilder
     */
    private $ec_product;

    use \Tatter\Relations\Traits\ModelTrait;
    use \Adnduweb\Ci4_logs\Traits\AuditsTrait;
    protected $afterInsert        = ['auditInsert'];
    protected $afterUpdate        = ['auditUpdate'];
    protected $afterDelete        = ['auditDelete'];
    protected $table              = 'ec_product';
    protected $tableLang          = 'ec_product_lang';
    protected $with               = ['ec_product_lang'];
    protected $without            = [];
    protected $primaryKey         = 'id_product';
    protected $returnType         = Product::class;
    protected $useSoftDeletes     = false;
    protected $allowedFields      = [
        'id_category_default', 'id_location', 'id_supplier', 'id_manufacturer', 'id_taxe', 'sku', 'ean13', 'on_sale', 'quantity', 'quantity_minimal', 'price', 'wholesale_price',
        'width', 'height', 'depth', 'weight', 'active'
    ];
    protected $useTimestamps      = true;
    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * ProductModel constructor.
     *
     * @param array ...$params
     *
     * @throws \CodeIgniter\Database\Exceptions\DatabaseException
     */
    public function __construct(...$params)
    {
        parent::__construct();
        $this->ec_product          = $this->db->table('ec_product');
        $this->ec_product_lang     = $this->db->table('ec_product_lang');
        $this->ec_product_category = $this->db->table('ec_product_category');
        $this->ec_category         = $this->db->table('ec_category');
        $this->ec_category_lang    = $this->db->table('ec_category_lang');
    }

    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {
        $this->ec_product->select();
        $this->ec_product->select('created_at as date_create_at');
        $this->ec_product->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_product');
        if (isset($query[0]) && is_array($query)) {
            $this->ec_product->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
            $this->ec_product->limit(0, $page);
        } else {
            $this->ec_product->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
            $page = ($page == '1') ? '0' : (($page - 1) * $perpage);
            $this->ec_product->limit($perpage, $page);
        }

        $this->ec_product->orderBy($sort['field'] . ' ' . $sort['sort']);
        $productResult = $this->ec_product->get()->getResult();

        // In va chercher les ec_category
        if (!empty($productResult)) {
            $i = 0;
            foreach ($productResult as $article) {
                $productResult[$i]->ec_category = $this->getCatByArt($article->id_product);
                $i++;
            }
        }

        //echo $this->ec_product->getCompiledSelect(); exit;
        return $productResult;
    }

    public function getAllCount(array $sort, array $query)
    {
        $this->ec_product->select($this->table . '.' . $this->primaryKey);
        $this->ec_product->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_product');
        if (isset($query[0]) && is_array($query)) {
            $this->ec_product->where('deleted_at IS NULL AND (name LIKE "%' . $query[0] . '%" OR description_short LIKE "%' . $query[0] . '%") AND id_lang = ' . service('settings')->setting_id_lang);
        } else {
            $this->ec_product->where('deleted_at IS NULL AND id_lang = ' . service('settings')->setting_id_lang);
        }

        $this->ec_product->orderBy($sort['field'] . ' ' . $sort['sort']);

        $pages = $this->ec_product->get();
        //echo $this->ec_product->getCompiledSelect(); exit;
        return $pages->getResult();
    }

    public function getCatArt(int $id_product): array
    {
        $this->ec_product_category->select();
        $this->ec_product_category->where('id_product', $id_product);
        return $this->ec_product_category->get()->getResult();
    }

    public function getCatByArt($id_product = null): array
    {
        $this->ec_product_category->select();
        //$this->ec_product_category->join('ec_category_lang', 'ec_product_category.id_category = ec_category_lang.id_category');
        $this->ec_product_category->where(['id_product' => $id_product]);
        $ec_product_category =  $this->ec_product_category->get()->getResult();
        $temp = [];
        if (!empty($ec_product_category)) {
            $i = 0;
            foreach ($ec_product_category as $art) {
                $temp[$art->id_category] = $art;
                $temp[$art->id_category]->name = $this->ec_category_lang->where(['id_category' => $art->id_category, 'id_lang' => service('settings')->setting_id_lang])->get()->getRow()->name;
                $i++;
            }
        }
        return $temp;
    }

    public function getIdArticleBySlug($slug)
    {
        $this->ec_product->select($this->table . '.' . $this->primaryKey . ', active, type');
        $this->ec_product->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_product');
        $this->ec_product->where('deleted_at IS NULL AND ' . $this->tableLang . '.slug="' . $slug . '"');
        $ec_product = $this->ec_product->get()->getRow();
        // echo $this->ec_product->getCompiledSelect();
        // exit;
        if (!empty($ec_product)) {
            if ($ec_product->active == '1')
                return $ec_product;
        }
        return false;
    }

    public function getLast(int $id_lang)
    {
        $this->ec_product->select();
        $this->ec_product->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_product');
        $this->ec_product->where('deleted_at IS NULL AND ' . $this->tableLang . '.id_lang="' . $id_lang . '"');
        $this->ec_product->limit(1);
        $this->ec_product->orderBy($this->table . '.id_product ASC ');
        $ec_product = $this->ec_product->get()->getRow();
        return $ec_product;
    }


    public function getLink(int $id_product, int $id_lang)
    {
        $this->ec_product->select('slug');
        $this->ec_product->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.id_product');
        $this->ec_product->where([$this->table . '.id_product' => $id_product, 'id_lang' => $id_lang]);
        $ec_product = $this->ec_product->get()->getRow();
        return $ec_product;
    }

    public function dupliquer(int $id_product)
    {

        //Article
        $this->ec_product->select();
        $this->ec_product->where(['id_product' => $id_product]);
        $getArticle = $this->ec_product->get()->getRow();

        unset($getArticle->id_product);
        $getArticle->type = 4;
        $Article = new Article((array) $getArticle);
        $this->save($Article);
        $id_productNew = $this->insertID();

        // On enregistre les langues
        $this->ec_product_lang->select();
        $this->ec_product_lang->where(['id_product' => $id_product]);
        $getArticleLangs = $this->ec_product_lang->get()->getRow();
        $getArticleLangs->id_product = $id_productNew;
        $this->ec_product_lang->insert((array) $getArticleLangs);

        // On enregistre les ec_category par default
        $this->ec_product_category->insert(['id_product' => $id_productNew, 'id_category' => $getArticle->id_category_default]);


        // exit;
    }

    /**
     * @param string $column
     * @param string $data
     *
     * @return mixed
     */
    public function GetArticle(string $column, string $data)
    {
        $this->ec_product->select("*, DATE_FORMAT(`created_at`,'Le %d-%m-%Y &agrave; %H:%i:%s') AS `created_at`, DATE_FORMAT(`updated_at`,'Le %d-%m-%Y &agrave; %H:%i:%s') AS `updated_at`");
        $this->ec_product->where($column, $data);

        return $this->ec_product->get()->getRow();
    }

    /**
     * @return array|mixed
     */
    public function lastFive(): array
    {
        $this->ec_product->select("*, DATE_FORMAT(`created_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `created_at`, DATE_FORMAT(`updated_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `updated_at`");
        $this->ec_product->limit('5');
        $this->ec_product->orderBy('id_product', 'DESC');

        return $this->ec_product->get()->getResult();
    }

    /**
     * @return int|void
     */
    public function count_publied(): int
    {
        $this->ec_product->select('COUNT(id_product) as id_product');
        $this->ec_product->where('published', 1);

        return $this->ec_product->get()->getRow()->id_product;
    }

    /**
     * @return int|void
     */
    public function count_attCorrect(): int
    {
        $this->ec_product->select('COUNT(id_product) as id_product');
        $this->ec_product->where('corriged', 0);
        $this->ec_product->where('published', 0);
        $this->ec_product->where('brouillon', 0);

        return $this->ec_product->get()->getRow()->id_product;
    }

    /**
     * @return int|void
     */
    public function count_attPublished(): int
    {
        $this->ec_product->select('COUNT(id_product) as id_product');
        $this->ec_product->where('corriged', 1);
        $this->ec_product->where('published', 0);

        return $this->ec_product->get()->getRow()->id_product;
    }

    /**
     * @return int|void
     */
    public function count_brouillon(): int
    {
        $this->ec_product->select('COUNT(id_product) as id_product');
        $this->ec_product->where('brouillon', 1);

        return $this->ec_product->get()->getRow()->id_product;
    }

    /**
     * @param string $title
     * @param string $link
     * @param string $content
     * @param string $tags
     * @param string $ec_category
     * @param string $pic
     * @param int $important
     *
     * @return int (Return id)
     */
    public function Add(string $title, string $link, string $content, string $tags, string $ec_category, string $pic, int $important): int
    {
        $data = [
            'title'          => $title,
            'content'        => $content,
            'author_created' => 1,
            'important'      => $important,
            'link'           => $link,
            'picture_one'    => $pic,
            'ec_category'     => $ec_category,
            'tags'           => $tags
        ];
        $this->ec_product->insert($data);

        return $this->db->insertID();
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $link
     * @param string $content
     * @param string $tags
     * @param string $ec_category
     * @param string $pic
     * @param int $important
     * @param int $type
     *
     * @return bool
     */
    public function Edit(int $id, string $title, string $link, string $content, string $tags, string $ec_category, string $pic, int $important, int $type): bool
    {
        $data = [
            'title'          => $title,
            'content'        => $content,
            'author_created' => 1,
            'author_update'  => 1,
            'important'      => $important,
            'link'           => $link,
            'picture_one'    => $pic,
            'ec_category'     => $ec_category,
            'tags'           => $tagsy
        ];

        if ($type == 1) {
            $data['published'] = 1;
            $data['corriged'] = 1;
        } elseif ($type == 2) {
            $data['published'] = 0;
            $data['corriged'] = 0;
            $data['brouillon'] = 0;
        } elseif ($type == 3) {
            $data['published'] = 0;
            $data['corriged'] = 1;
            $data['brouillon'] = 0;
        }

        $this->ec_product->where('id_product', $id);
        $this->ec_product->set('updated_at', 'NOW()', false);
        $this->ec_product->update($data);

        return true;
    }

    /**
     * @param int $type (1 = publied, 2 = wait corrected, 3 = wait publied, 4 = brouillon)
     *
     * @return mixed
     */
    public function getArticleListAdmin(int $type)
    {
        $this->ec_product->select("*, DATE_FORMAT(`created_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `created_at`, DATE_FORMAT(`updated_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `updated_at`");

        if ($type == 1) {
            $this->ec_product->where('published', 1);
        } elseif ($type == 2) {
            $this->ec_product->where('corriged', 0);
            $this->ec_product->where('published', 0);
            $this->ec_product->where('brouillon', 0);
        } elseif ($type == 3) {
            $this->ec_product->where('corriged', 1);
            $this->ec_product->where('published', 0);
        } elseif ($type == 4) {
            $this->ec_product->where('brouillon', 1);
        }

        return $this->ec_product->get()->getResult();
    }
}
