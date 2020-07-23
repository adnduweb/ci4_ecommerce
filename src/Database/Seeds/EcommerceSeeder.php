<?php

namespace Adnduweb\Ci4_ecommerce\Database\Seeds;

use joshtronic\LoremIpsum;

class EcommerceSeeder extends \CodeIgniter\Database\Seeder
{
    //\\Adnduweb\\Ci4_ecommerce\\Database\\Seeds\\BlogSeeder
    /**
     * @return mixed|void
     */
    function run()
    {


        $lipsum = new LoremIpsum();
        // Define default project setting templates
        $rowsCat = [
            [
                'id' => 1,
                'id_parent'    => 0,
                'order'        => 1,
                'active'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
            ]

        ];
        $rowsCatLang = [
            [
                'category_id'      => 1,
                'id_lang'           => 1,
                'name'              => 'Défaut',
                'description_short' => $lipsum->sentence(),
                'meta_title'        => $lipsum->sentence(),
                'meta_description'  => $lipsum->sentence(),
                'slug'              => 'default'
            ]

        ];

        // Check for and create project setting templates
        //$pages = new PageModel();
        $db = \Config\Database::connect();
        foreach ($rowsCat as $row) {
            $article = $db->table('ec_categories')->where('id', $row['id'])->get()->getRow();
            //print_r($article); exit;
            if (empty($article)) {
                // No setting - add the row
                $db->table('ec_categories')->insert($row);
            }
        }

        foreach ($rowsCatLang as $rowLang) {
            $articlelang = $db->table('ec_categories_langs')->where('category_id', $rowLang['category_id'])->get()->getRow();

            if (empty($articlelang)) {
                // No setting - add the row
                $db->table('ec_categories_langs')->insert($rowLang);
            }
        }



        // gestionde l'application
        $rowsBlogTabs = [
            'id_parent'       => 20,
            'depth'           => 2,
            'left'            => 11,
            'right'           => 19,
            'position'        => 1,
            'section'         => 0,
            'namespace'       => 'Adnduweb\Ci4_ecommerce',
            'class_name'      => '',
            'active'          => 1,
            'icon'            => '',
            'slug'            => 'catalogue',
        ];

        $rowsBlogTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'catalogue',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'catalogue',
            ],
        ];


        $rowsArticlesTabs = [
            'depth'           => 3,
            'left'            => 12,
            'right'           => 13,
            'position'        => 1,
            'section'         => 0,
            'namespace'       => 'Adnduweb\Ci4_ecommerce',
            'class_name'      => 'product',
            'active'          => 1,
            'icon'            => '',
            'slug'            => 'catalogue/product',
        ];

        $rowsArticlesTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'produits',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'produits',
            ],
        ];

        $rowsCatTabs = [
            'depth'           => 3,
            'left'            => 14,
            'right'           => 15,
            'position'        => 1,
            'section'         => 0,
            'namespace'       => 'Adnduweb\Ci4_ecommerce',
            'class_name'      => 'category',
            'active'          => 1,
            'icon'            => '',
            'slug'            => 'catalogue/category',
        ];

        $rowsCatTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'catégories',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'catégories',
            ],
        ];

        $rowsManTabs = [
            'depth'      => 3,
            'left'       => 15,
            'right'      => 16,
            'position'   => 4,
            'section'    => 0,
            'namespace'  => 'Adnduweb\Ci4_ecommerce',
            'class_name' => 'brand',
            'active'     => 1,
            'icon'       => '',
            'slug'       => 'catalogue/brand',
        ];

        $rowsManTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'marques',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'brands',
            ],
        ];

        $rowsSuppliersTabs = [
            'depth'      => 3,
            'left'       => 15,
            'right'      => 16,
            'position'   => 4,
            'section'    => 0,
            'namespace'  => 'Adnduweb\Ci4_ecommerce',
            'class_name' => 'supplier',
            'active'     => 1,
            'icon'       => '',
            'slug'       => 'catalogue/supplier',
        ];

        $rowsSuppliersTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'fournisseurs',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'supplier',
            ],
        ];


        $tabBlog = $db->table('tabs')->where('class_name', $rowsBlogTabs['class_name'])->where('namespace', $rowsBlogTabs['namespace'])->get()->getRow();
        //print_r($tab); exit;
        if (empty($tabBlog)) {
            // No setting - add the row
            $db->table('tabs')->insert($rowsBlogTabs);
            $newInsert = $db->insertID();
            $i = 0;
            foreach ($rowsBlogTabsLangs as $rowLang) {
                $rowLang['tab_id']   = $newInsert;
                // No setting - add the row
                $db->table('tabs_langs')->insert($rowLang);
                $i++;
            }

            // on insere les articles
            $tabArticles = $db->table('tabs')->where('class_name', $rowsArticlesTabs['class_name'])->where('namespace', $rowsArticlesTabs['namespace'])->get()->getRow();
            //print_r($tab); exit;
            if (empty($tabArticles)) {
                // No setting - add the row
                $rowsArticlesTabs['id_parent']  = $newInsert;
                $db->table('tabs')->insert($rowsArticlesTabs);
                $newInsertArt = $db->insertID();
                $i = 0;
                foreach ($rowsArticlesTabsLangs as $rowLang) {
                    $rowLang['tab_id']   = $newInsertArt;
                    // No setting - add the row
                    $db->table('tabs_langs')->insert($rowLang);
                    $i++;
                }
            }

            // On Insére les categories
            $tabCategorie = $db->table('tabs')->where('class_name', $rowsCatTabs['class_name'])->where('namespace', $rowsCatTabs['namespace'])->get()->getRow();
            //print_r($tab); exit;
            if (empty($tabCategorie)) {
                // No setting - add the row
                $rowsCatTabs['id_parent']  = $newInsert;
                $db->table('tabs')->insert($rowsCatTabs);
                $newInsertCat = $db->insertID();
                $i = 0;
                foreach ($rowsCatTabsLangs as $rowLang) {
                    $rowLang['tab_id']   = $newInsertCat;
                    // No setting - add the row
                    $db->table('tabs_langs')->insert($rowLang);
                    $i++;
                }
            }

            // On Insére les brands
            $tabCategorie = $db->table('tabs')->where('class_name', $rowsManTabs['class_name'])->where('namespace', $rowsManTabs['namespace'])->get()->getRow();
            //print_r($tab); exit;
            if (empty($tabCategorie)) {
                // No setting - add the row
                $rowsManTabs['id_parent']  = $newInsert;
                $db->table('tabs')->insert($rowsManTabs);
                $newInsertCat = $db->insertID();
                $i = 0;
                foreach ($rowsManTabsLangs as $rowLang) {
                    $rowLang['tab_id']   = $newInsertCat;
                    // No setting - add the row
                    $db->table('tabs_langs')->insert($rowLang);
                    $i++;
                }
            }

             // On Insére les suppliers
             $tabCategorie = $db->table('tabs')->where('class_name', $rowsSuppliersTabs['class_name'])->where('namespace', $rowsSuppliersTabs['namespace'])->get()->getRow();
             //print_r($tab); exit;
             if (empty($tabCategorie)) {
                 // No setting - add the row
                 $rowsSuppliersTabs['id_parent']  = $newInsert;
                 $db->table('tabs')->insert($rowsSuppliersTabs);
                 $newInsertCat = $db->insertID();
                 $i = 0;
                 foreach ($rowsSuppliersTabsLangs as $rowLang) {
                     $rowLang['tab_id']   = $newInsertCat;
                     // No setting - add the row
                     $db->table('tabs_langs')->insert($rowLang);
                     $i++;
                 }
             }
        }


        /**
         *
         * Gestion des permissions
         */
        $rowsPermissionsEcommerce = [
            [
                'name'              => 'EC_Product::view',
                'description'       => 'Voir les Produits',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'EC_Product::create',
                'description'       => 'Créer des Produits',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'EC_Product::edit',
                'description'       => 'Modifier les Produits',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'EC_Product::delete',
                'description'       => 'Supprimer des articles',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'EC_categories::view',
                'description'       => 'Voir les categories',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'EC_categories::create',
                'description'       => 'Créer des categories',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'EC_categories::edit',
                'description'       => 'Modifier les categories',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'EC_categories::delete',
                'description'       => 'Supprimer des categories',
                'is_natif'          => '0',
            ],

        ];

        // On insére le role par default au user
        foreach ($rowsPermissionsEcommerce as $row) {
            $tabRow =  $db->table('auth_permissions')->where(['name' => $row['name']])->get()->getRow();
            if (empty($tabRow)) {
                // No langue - add the row
                $db->table('auth_permissions')->insert($row);
            }
        }

        //Gestion des module
        $rowsModulePages = [
            'name'       => 'ecommerce',
            'namespace'  => 'Adnduweb\Ci4_ecommerce',
            'active'     => 1,
            'version'    => '1.0.2',
            'created_at' =>  date('Y-m-d H:i:s')
        ];

        $tabRow =  $db->table('modules')->where(['name' => $rowsModulePages['name']])->get()->getRow();
        if (empty($tabRow)) {
            // No langue - add the row
            $db->table('modules')->insert($rowsModulePages);
        }
    }
}
