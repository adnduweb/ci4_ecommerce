<?php

namespace Adnduweb\Ci4_ecommerce\Entities;

use CodeIgniter\Entity;
use CodeIgniter\I18n\Time;
use Adnduweb\Ci4_ecommerce\Models\CategoryModel;

class Product extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;
    use \App\Traits\BuilderEntityTrait;
    protected $table        = 'ec_products';
    protected $tableLang    = 'ec_products_langs';
    protected $tablecArtCat = 'ec_products_categories';
    protected $primaryKey   = 'id';
    protected $primaryKeyLang  = 'product_id';
    protected $primaryKeyCLang = 'category_id';

    protected $datamap = [];
    /**
     * Define properties that are automatically converted to Time instances.
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    /**
     * Array of field names and the type of value to cast them as
     * when they are accessed.
     */
    protected $casts = [];



    /** 
     *
     * Nom de la categorie 
     */
    public function getBNameCategorie()
    {
        if (isset($this->{$this->tableLang})) {
            return $this->attributes['name_categorie'] ?? null;
        }
    }





    public function getPictureOneAtt()
    {
        if (!empty($this->attributes['picture_one'])) {
            return json_decode($this->attributes['picture_one']);
        }
        return null;
    }

    public function getImageOneAtt($id_lang, $format = false)
    {
        $image = null;

        if (!empty($this->attributes['picture_one'])) {

            $getAttrOptions = json_decode($this->attributes['picture_one']);;
            if (empty($getAttrOptions))
                return $image;

            $mediasModel = new \App\Models\mediasModel();
            $image = $mediasModel->getMediaById($getAttrOptions->media->id_media, $id_lang);
            if (empty($image)) {
                $image = $mediasModel->where('id_media', $getAttrOptions->media->id_media)->get()->getRow();
            }
            if (is_object($image)) {
                if ($format == true) {
                    $getAttrOptions->media->filename =  base_url() . '/uploads/' . $format . '/' . $image->namefile;
                    list($width, $height, $type, $attr) =  getimagesize($getAttrOptions->media->filename);
                    $getAttrOptions->media->dimensions = (object) ['width' => $width, 'height' => $height];
                    $getAttrOptions->media->format = $format;
                }
                $image->class = 'adw_lazyload ';
                $image->options = $getAttrOptions;
            }
        }

        return $image;
    }

    public function getBPrice()
    {
        return $this->price;
    }

    public function isNew()
    {
        $time1   = Time::parse(date($this->created_at, config('Ecommerce')->newProduct));
        $time2 = new Time('now');
        return $time1->isBefore($time2);
    }

    public function getPictureheaderAtt()
    {
        if (!empty($this->attributes['picture_header'])) {
            return json_decode($this->attributes['picture_header']);
        }
        return null;
    }

    public function _prepareLang()
    {
        $lang = [];
        if (!empty($this->{$this->primaryKey})) {
            foreach ($this->{$this->tableLang} as $tabs_lang) {
                $lang[$tabs_lang->id_lang] = $tabs_lang;
            }
        }
        return $lang;
    }

    public function saveLang(array $data, int $key)
    {
        //print_r($data);
        $db      = \Config\Database::connect();
        $builder = $db->table($this->tableLang);
        foreach ($data as $k => $v) {
            $this->tableLang =  $builder->where(['id_lang' => $k, $this->primaryKeyLang => $key])->get()->getRow();
            // print_r($this->tableLang);
            if (empty($this->tableLang)) {
                $data = [
                    $this->primaryKeyLang => $key,
                    'id_lang'             => $k,
                    'name'                => $v['name'],
                    'sous_name'           => $v['sous_name'],
                    'description_short'   => $v['description_short'],
                    'description'         => $v['description'],
                    'meta_title'          => $v['meta_title'],
                    'meta_description'    => $v['meta_description'],
                    'tags'                => isset($v['tags']) ? $v['tags'] : '',
                    'slug'                => uniforme(trim($v['slug'])),
                ];
                // Create the new participant
                $builder->insert($data);
            } else {
                $data = [
                    $this->primaryKeyLang => $this->tableLang->{$this->primaryKeyLang},
                    'id_lang'             => $this->tableLang->id_lang,
                    'name'                => $v['name'],
                    'sous_name'           => $v['sous_name'],
                    'description_short'   => $v['description_short'],
                    'description'         => $v['description'],
                    'meta_title'          => $v['meta_title'],
                    'meta_description'    => $v['meta_description'],
                    'tags'                => isset($v['tags']) ? $v['tags'] : '',
                    'slug'                => uniforme(trim($v['slug'])),
                ];
                print_r($data);
                $builder->set($data);
                $builder->where([$this->primaryKeyLang => $this->tableLang->{$this->primaryKeyLang}, 'id_lang' => $this->tableLang->id_lang]);
                $builder->update();
            }
        }
    }

    public function saveCategorie($data)
    {
        // print_r($data); exit;
        $db         = \Config\Database::connect();
        $builder    = $db->table($this->tablecArtCat);
        $id_product = $data->{$this->primaryKey};

        $builder->delete([$this->primaryKeyLang => $id_product]);

        foreach ($data->id_category as $k => $v) {

            $this->tablecArtCat =  $builder->where([$this->primaryKeyCLang => $v, $this->primaryKeyLang => $id_product])->get()->getRow();
            if (empty($this->tablecArtCat)) {
                $data = [
                    $this->primaryKeyLang  => $id_product,
                    $this->primaryKeyCLang => $v
                ];
                //print_r($data); 
                $builder->insert($data);
            }
        }
    }
}
