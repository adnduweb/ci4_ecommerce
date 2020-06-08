<?php

namespace Adnduweb\Ci4_page\Database\Seeds;

use Adnduweb\Ci4_page\Models\PagesModel;
use joshtronic\LoremIpsum;

class PageSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $lipsum = new LoremIpsum();
        // Define default project setting templates
        $rows = [
            [
                'id_page'            => 1,
                'id_parent'          => 0,
                'template'           => 'default',
                'active'             => 1,
                'no_follow_no_index' => 0,
                'handle'             => null,
                'order'              => 1,
                'created_at'         => date('Y-m-d H:i:s'),
            ]

        ];
        $rowsLang = [
            [
                'id_page'      => 1,
                'id_lang'           => 1,
                'name'              => 'Welcome to CodeIgniter',
                'name_2'            => 'The small framework with powerful features',
                'description_short' => $lipsum->sentence(),
                'description'       => $lipsum->paragraphs(5),
                'meta_title'        => $lipsum->sentence(),
                'meta_description'  => $lipsum->sentence(),
                'tags'              => 'test',
                'slug'              => '/'
            ]

        ];

        // Check for and create project setting templates
        //$pages = new PagesModel();
        $db = \Config\Database::connect();
        foreach ($rows as $row) {
            $page = $db->table('pages')->where('id_page', $row['id_page'])->get()->getRow();
            //print_r($page); exit;
            if (empty($page)) {
                // No setting - add the row
                $db->table('pages')->insert($row);
            }
        }

        foreach ($rowsLang as $rowLang) {
            $pagelang = $db->table('pages_langs')->where('id_page', $rowLang['id_page'])->get()->getRow();

            if (empty($pagelang)) {
                // No setting - add the row
                $db->table('pages_langs')->insert($rowLang);
            }
        }

        $rowsTabs = [
            [
                'id_parent'         => 17,
                'depth'             => 2,
                'left'              => 33,
                'right'             => 34,
                'position'          => 1,
                'section'           => 0,
                'module'            => 'Adnduweb\Ci4_page',
                'class_name'        => 'AdminPages',
                'active'            =>  1,
                'icon'              => '',
                'slug'             => 'pages',
                'name_controller'       => ''
            ],
        ];

        $rowsTabsLangs = [
            [
                'id_lang'         => 1,
                'name'             => 'pages',
            ],
            [
                'id_lang'         => 2,
                'name'             => 'pages',
            ],
        ];

        foreach ($rowsTabs as $row) {
            $tab = $db->table('tabs')->where('class_name', $row['class_name'])->get()->getRow();
            //print_r($tab); exit;
            if (empty($tab)) {
                // No setting - add the row
                $db->table('tabs')->insert($row);
                $newInsert = $db->insertID();
                $i = 0;
                foreach ($rowsTabsLangs as $rowLang) {
                    $rowLang['tab_id']   = $newInsert;
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
        $rowsPermissionsPages = [
            [
                'name'              => 'Pages::views',
                'description'       => 'Voir les pages',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Pages::create',
                'description'       => 'Créer des pages',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Pages::edit',
                'description'       => 'Modifier les pages',
                'is_natif'          => '0',
            ],
            [
                'name'              => 'Pages::delete',
                'description'       => 'Supprimer des pages',
                'is_natif'          => '0',
            ]
        ];

        // On insére le role par default au user
        foreach ($rowsPermissionsPages as $row) {
            $tabRow =  $db->table('auth_permissions')->where(['name' => $row['name']])->get()->getRow();
            if (empty($tabRow)) {
                // No langue - add the row
                $db->table('auth_permissions')->insert($row);
            }
        }

        //Gestion des module
        $rowsModulePages = [
            'name'       => 'pages',
            'namespace'  => 'Adnduweb\Ci4_page',
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
