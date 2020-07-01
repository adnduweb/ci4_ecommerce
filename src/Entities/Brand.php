<?php

namespace Adnduweb\Ci4_ecommerce\Entities;

use CodeIgniter\Entity;

class Brand extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;
    use \App\Traits\BuilderEntityTrait;
    protected $table          = 'ec_brands';
    protected $tableLang      = 'ec_brands_langs';
    protected $primaryKey     = 'id';
    protected $primaryKeyLang = 'brand_id';

    protected $attributes = [
        'id' => null,
        'name' => null,   
        'created_at' => null,
        'updated_at' => null,
        'deleted_at' => null,
    ];


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

    
    public function saveLang(array $data, int $key)
    {
        //print_r($data);
        $db      = \Config\Database::connect();
        $builder = $db->table($this->tableLang);
        foreach ($data as $k => $v) {
            $this->tableLang =  $builder->where(['id_lang' => $k, $this->primaryKeyLang => $key])->get()->getRow();
            if (empty($this->tableLang)) {
                $data = [
                    $this->primaryKeyLang => $key,
                    'id_lang'             => $k,
                    'description'         => $v['description'],
                    'description_short'   => $v['description_short'],
                    'meta_title'          => $v['meta_title'],
                    'meta_description'    => $v['meta_description'],
                    'slug'                => strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s+/', '-', trim($v['slug']))))
                ];
                $builder->insert($data);
            } else {
                $data = [
                    $this->primaryKeyLang => $this->tableLang->{$this->primaryKeyLang},
                    'id_lang'             => $this->tableLang->id_lang,
                    'description'                => $v['description'],
                    'description_short'   => $v['description_short'],
                    'meta_title'          => $v['meta_title'],
                    'meta_description'    => $v['meta_description'],
                    'slug'                => strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s+/', '-', trim($v['slug']))))
                ];
                $builder->set($data);
                $builder->where([$this->primaryKeyLang => $this->tableLang->{$this->primaryKeyLang}, 'id_lang' => $this->tableLang->id_lang]);
                $builder->update();
            }
        }
    }
}
