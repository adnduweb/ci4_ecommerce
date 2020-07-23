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
use Faker\Generator;

/**
 * Class ProductModel
 *
 * @package App\Models
 */
class ProductModel extends Model
{
    use \Tatter\Relations\Traits\ModelTrait, \Adnduweb\Ci4_logs\Traits\AuditsTrait, \App\Models\BaseModel;

    /**
     * @var \CodeIgniter\Database\BaseBuilder
     */
    private $ec_products;


    protected $afterInsert     = ['auditInsert'];
    protected $afterUpdate     = ['auditUpdate'];
    protected $afterDelete     = ['auditDelete'];
    protected $table           = 'ec_products';
    protected $tableLang       = 'ec_products_langs';
    protected $tableCatLang    = 'ec_categories_langs';
    protected $primaryKeyLang  = 'product_id';
    protected $primaryKeyCLang = 'category_id';
    protected $with            = ['ec_products_langs'];
    protected $without         = [];
    protected $primaryKey      = 'id';
    protected $returnType      = Product::class;
    protected $localizeFile    = 'Adnduweb\Ci4_ecommerce\Models\ProductModel';
    protected $useSoftDeletes  = false;
    protected $allowedFields   = [
        'id_category_default', 'shop_id', 'supplier_id', 'brand_id', 'taxe_rules_group_id', 'isbn', 'ean13', 'upc', 'on_sale', 'ecotax', 'quantity', 'quantity_minimal', 'price', 'wholesale_price',
        'width', 'height', 'depth', 'weight', 'active'
    ];
    protected $useTimestamps      = true;
    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $searchKtDatatable  = ['name', 'description_short', 'created_at'];

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
        $this->builder             = $this->db->table('ec_products');
        $this->builder_lang        = $this->db->table('ec_products_langs');
        $this->builder_categories  = $this->db->table('ec_products_categories');
        $this->ec_categories       = $this->db->table('ec_categories');
        $this->ec_categories_langs = $this->db->table('ec_categories_langs');
    }

    /**
     * 
     * Generateur de Fake
     */
    public function fake(Generator &$faker)
    {
        return [
            'id_category_default' => 1,
            'shop_id'             => 1,
            'supplier_id'         => $faker->boolean(),
            'brand_id'            => $faker->boolean(),
            'taxe_rules_group_id' => $faker->boolean(),
            'isbn'                => $faker->word(1),
            'ean13'               => $faker->word(1),
            'upc'                 => $faker->word(1),
            'on_sale'             => $faker->boolean(),
            'ecotax'              => 1.000,
            'quantity'            => 100,
            'quantity_minimal'    => 1,
            'price'               => mt_rand(100, 1000) / 10.0,
            'wholesale_price'     => mt_rand(100, 1000) / 10.0,
            'width'               => 10,
            'height'              => 10,
            'depth'               => 10,
            'weight'              => 10,
            'active'              => $faker->boolean(),
            'created_at'          => date('Y-m-d H:i:s'),
        ];
    }

    public function fakelang(int $id)
    {
        $faker = \Faker\Factory::create();
        // print_r($faker);
        // exit;
        $data = [
            'product_id'           => $id,
            'id_lang'           => 1,
            'name'              => $faker->word(1),
            'sous_name'         => $faker->word(3),
            'description_short' => $faker->paragraph(1),
            'description'       => $faker->text,
            'meta_title'        => $faker->word(10),
            'meta_description'  => $faker->word(10),
            'tags'              => $faker->word(1),
            'slug'              => uniforme(trim($faker->word(2))),
        ];
        // Create the new participant
        $this->builder_lang->insert($data);

        $dataCat = [
            'product_id'      => $id,
            'category_id' => 1
        ];
        $this->builder_categories->insert($dataCat);
    }

    public function getAllList(int $page, int $perpage, array $sort, array $query)
    {

        $productResult = $this->getBaseAllList($page, $perpage, $sort, $query, $this->searchKtDatatable);

        // In va chercher les ec_categories
        if (!empty($productResult)) {
            $i = 0;
            foreach ($productResult as $article) {
                $productResult[$i]->ec_categories = $this->getCatByProduct($article->{$this->primaryKey});
                $LangueDisplay = [];
                foreach (service('switchlanguage')->getArrayLanguesSupported() as $k => $v) {
                    $LangueDisplay[$k] = $this->getLanguesDispo($article->{$this->primaryKey}, $v);
                }
                $productResult[$i]->languages = $LangueDisplay;
                $i++;
            }
        }

        //echo $this->builder->getCompiledSelect(); exit;
        return $productResult;
    }


    public function getCatArt(int $id_product): array
    {
        $this->builder_categories->select();
        $this->builder_categories->where('id_product', $id_product);
        return $this->builder_categories->get()->getResult();
    }

    public function getCatByProduct($id_product = null): array
    {
        $this->builder_categories->select();
        //$this->builder_categories->join('ec_categories_langs', 'ec_products_categories.id_category = ec_categories_langs.id_category');
        $this->builder_categories->where([$this->primaryKeyLang => $id_product]);
        $ec_products_categories =  $this->builder_categories->get()->getResult();
        $temp = [];
        if (!empty($ec_products_categories)) {
            $i = 0;
            foreach ($ec_products_categories as $art) {
                $temp[$art->{$this->primaryKeyCLang}] = $art;
                $temp[$art->{$this->primaryKeyCLang}]->name = $this->ec_categories_langs->where([$this->primaryKeyCLang => $art->{$this->primaryKeyCLang}, 'id_lang' => service('switchlanguage')->getIdLocale()])->get()->getRow()->name;
                $i++;
            }
        }
        return $temp;
    }

    public function getIdArticleBySlug($slug)
    {
        $this->builder->select($this->table . '.' . $this->primaryKey . ', active, type');
        $this->builder->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.' . $this->primaryKeyLang);
        $this->builder->where('deleted_at IS NULL AND ' . $this->tableLang . '.slug="' . $slug . '"');
        $ec_products = $this->builder->get()->getRow();
        // echo $this->builder->getCompiledSelect();
        // exit;
        if (!empty($ec_products)) {
            if ($ec_products->active == '1')
                return $ec_products;
        }
        return false;
    }

    public function getLast(int $id_lang)
    {
        $this->builder->select();
        $this->builder->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.' . $this->primaryKeyLang);
        $this->builder->where('deleted_at IS NULL AND ' . $this->tableLang . '.id_lang="' . $id_lang . '"');
        $this->builder->limit(1);
        $this->builder->orderBy($this->table . '.id_product ASC ');
        $ec_products = $this->builder->get()->getRow();
        return $ec_products;
    }


    public function getLink(int $id_product, int $id_lang)
    {
        $this->builder->select('slug');
        $this->builder->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.' . $this->primaryKeyLang);
        $this->builder->where([$this->table . '.id_product' => $id_product, 'id_lang' => $id_lang]);
        $ec_products = $this->builder->get()->getRow();
        return $ec_products;
    }

    public function dupliquer(int $id_product)
    {

        //Article
        $this->builder->select();
        $this->builder->where([$this->primaryKey  => $id_product]);
        $getArticle = $this->builder->get()->getRow();

        unset($getArticle->{$this->primaryKey});
        $getArticle->active = 0;
        $Article = new Product((array) $getArticle);
        $this->save($Article);
        $id_productNew = $this->insertID();

        // On enregistre les langues
        $this->builder_lang->select();
        $this->builder_lang->where([$this->primaryKeyLang => $id_product]);
        $getArticleLangs = $this->builder_lang->get()->getRow();

        unset($getArticleLangs->id_product_lang);
        $getArticleLangs->{$this->primaryKeyLang} = $id_productNew;
        $this->builder_lang->insert((array) $getArticleLangs);

        // On enregistre les ec_categories par default
        $this->builder_categories->insert([$this->primaryKeyLang => $id_productNew, $this->primaryKeyCLang => $getArticle->id_category_default]);


        // exit;
    }


    public function updatePostCategorie(array $data)
    {

        //print_r($data);
        foreach ($data as $subdata) {
            $this->builder_categories->delete([$this->primaryKeyLang => $subdata['id_product']]);

            $tab = ['id_category_default' => $subdata['new_categorie_id']];
            $this->builder->set($tab);
            $this->builder->where([$this->primaryKey =>  $subdata['id_product']]);
            //echo $this->builder->getCompiledUpdate();exit;
            $this->builder->update();

            $this->builder_categories->insert([$this->primaryKeyLang => $subdata['id_product'], $this->primaryKeyCLang => $subdata['new_categorie_id']]);
        }
    }

    public function getListProduct()
    {
    }


    public function getPaginate(int $paginate, $request = null, $id_category = null)
    {
        $this->select();
        $this->select($this->tableCatLang . '.name as name_categorie, ' . $this->tableLang . '.name as name_product');
        $this->join($this->tableLang, $this->table . '.' . $this->primaryKey . ' = ' . $this->tableLang . '.' . $this->primaryKeyLang);
        $this->join($this->tableCatLang, $this->tableCatLang . '.' . $this->primaryKeyCLang . ' = ' . $this->table . '.id_category_default');
        $this->where('active', 1);

        if (!is_null($id_category)) {
            $this->where($this->tableCatLang . '.' . $this->primaryKeyCLang, $id_category);
        }

        if (!empty($request)) {
            if (isset($request['orderby']) && $request['orderway']) {
                $orderby = $request['orderby'];
                $orderway = $request['orderway'];
                if (!empty($orderby) && !empty($orderway))
                    $this->builder->orderBy($orderby . ' ' . $orderway);
            }
        } else {
            $this->orderBy('id', 'DESC');
        }


        //echo $this->getCompiledSelect(); exit;
        return $this->paginate($paginate);
    }

    // /**
    //  * @param string $column
    //  * @param string $data
    //  *
    //  * @return mixed
    //  */
    // public function GetArticle(string $column, string $data)
    // {
    //     $this->builder->select("*, DATE_FORMAT(`created_at`,'Le %d-%m-%Y &agrave; %H:%i:%s') AS `created_at`, DATE_FORMAT(`updated_at`,'Le %d-%m-%Y &agrave; %H:%i:%s') AS `updated_at`");
    //     $this->builder->where($column, $data);

    //     return $this->builder->get()->getRow();
    // }

    // /**
    //  * @return array|mixed
    //  */
    // public function lastFive(): array
    // {
    //     $this->builder->select("*, DATE_FORMAT(`created_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `created_at`, DATE_FORMAT(`updated_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `updated_at`");
    //     $this->builder->limit('5');
    //     $this->builder->orderBy('id_product', 'DESC');

    //     return $this->builder->get()->getResult();
    // }

    // /**
    //  * @return int|void
    //  */
    // public function count_publied(): int
    // {
    //     $this->builder->select('COUNT(id_product) as id_product');
    //     $this->builder->where('published', 1);

    //     return $this->builder->get()->getRow()->id_product;
    // }

    // /**
    //  * @return int|void
    //  */
    // public function count_attCorrect(): int
    // {
    //     $this->builder->select('COUNT(id_product) as id_product');
    //     $this->builder->where('corriged', 0);
    //     $this->builder->where('published', 0);
    //     $this->builder->where('brouillon', 0);

    //     return $this->builder->get()->getRow()->id_product;
    // }

    // /**
    //  * @return int|void
    //  */
    // public function count_attPublished(): int
    // {
    //     $this->builder->select('COUNT(id_product) as id_product');
    //     $this->builder->where('corriged', 1);
    //     $this->builder->where('published', 0);

    //     return $this->builder->get()->getRow()->id_product;
    // }

    // /**
    //  * @return int|void
    //  */
    // public function count_brouillon(): int
    // {
    //     $this->builder->select('COUNT(id_product) as id_product');
    //     $this->builder->where('brouillon', 1);

    //     return $this->builder->get()->getRow()->id_product;
    // }

    // /**
    //  * @param string $title
    //  * @param string $link
    //  * @param string $content
    //  * @param string $tags
    //  * @param string $ec_category
    //  * @param string $pic
    //  * @param int $important
    //  *
    //  * @return int (Return id)
    //  */
    // public function Add(string $title, string $link, string $content, string $tags, string $ec_category, string $pic, int $important): int
    // {
    //     $data = [
    //         'title'          => $title,
    //         'content'        => $content,
    //         'author_created' => 1,
    //         'important'      => $important,
    //         'link'           => $link,
    //         'picture_one'    => $pic,
    //         'ec_categories'     => $ec_category,
    //         'tags'           => $tags
    //     ];
    //     $this->builder->insert($data);

    //     return $this->db->insertID();
    // }

    // /**
    //  * @param int $id
    //  * @param string $title
    //  * @param string $link
    //  * @param string $content
    //  * @param string $tags
    //  * @param string $ec_category
    //  * @param string $pic
    //  * @param int $important
    //  * @param int $type
    //  *
    //  * @return bool
    //  */
    // public function Edit(int $id, string $title, string $link, string $content, string $tags, string $ec_category, string $pic, int $important, int $type): bool
    // {
    //     $data = [
    //         'title'          => $title,
    //         'content'        => $content,
    //         'author_created' => 1,
    //         'author_update'  => 1,
    //         'important'      => $important,
    //         'link'           => $link,
    //         'picture_one'    => $pic,
    //         'ec_categories'     => $ec_category,
    //         'tags'           => $tagsy
    //     ];

    //     if ($type == 1) {
    //         $data['published'] = 1;
    //         $data['corriged'] = 1;
    //     } elseif ($type == 2) {
    //         $data['published'] = 0;
    //         $data['corriged'] = 0;
    //         $data['brouillon'] = 0;
    //     } elseif ($type == 3) {
    //         $data['published'] = 0;
    //         $data['corriged'] = 1;
    //         $data['brouillon'] = 0;
    //     }

    //     $this->builder->where('id_product', $id);
    //     $this->builder->set('updated_at', 'NOW()', false);
    //     $this->builder->update($data);

    //     return true;
    // }

    // /**
    //  * @param int $type (1 = publied, 2 = wait corrected, 3 = wait publied, 4 = brouillon)
    //  *
    //  * @return mixed
    //  */
    // public function getArticleListAdmin(int $type)
    // {
    //     $this->builder->select("*, DATE_FORMAT(`created_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `created_at`, DATE_FORMAT(`updated_at`,'<strong>%d-%m-%Y</strong> &agrave; <strong>%H:%i:%s</strong>') AS `updated_at`");

    //     if ($type == 1) {
    //         $this->builder->where('published', 1);
    //     } elseif ($type == 2) {
    //         $this->builder->where('corriged', 0);
    //         $this->builder->where('published', 0);
    //         $this->builder->where('brouillon', 0);
    //     } elseif ($type == 3) {
    //         $this->builder->where('corriged', 1);
    //         $this->builder->where('published', 0);
    //     } elseif ($type == 4) {
    //         $this->builder->where('brouillon', 1);
    //     }

    //     return $this->builder->get()->getResult();
    // }
}
