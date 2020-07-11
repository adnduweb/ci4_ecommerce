<?php

namespace Adnduweb\Ci4_ecommerce\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_ecommerce_tables extends Migration
{
    public function up()
    {
        
        /*
         * Product
         */
        $this->forge->addField([
            'id'                  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_category_default' => ['type' => 'int',  'constraint' => 11, 'default' => 1],
            'shop_id'             => ['type' => 'int',  'constraint' => 11, 'default' => 1],
            'supplier_id'         => ['type' => 'int',  'constraint' => 11, 'default' => 1],
            'brand_id'            => ['type' => 'int',  'constraint' => 11, 'default' => 1],
            'taxe_rules_group_id' => ['type' => 'int',  'constraint' => 11, 'default' => 1],
            'isbn'                => ['type' => 'VARCHAR',  'constraint' => 255],
            'ean13'               => ['type' => 'VARCHAR',  'constraint' => 13],
            'upc'                 => ['type' => 'VARCHAR',  'constraint' => 48],
            'on_sale'             => ['type' => 'TINYINT',  'constraint' => 1],
            'ecotax'              => ['type' => 'DECIMAL',  'constraint' => "17,6", 'default' => '0.000000'],
            'quantity'            => ['type' => 'INT',  'constraint' => 10],
            'quantity_minimal'    => ['type' => 'INT',  'constraint' => 10],
            'low_stock_alert'     => ['type' => 'TINYINT',  'constraint' => 1],
            'price'               => ['type' => 'DECIMAL',  'constraint' => "20,6", 'default' => '0.000000'],
            'wholesale_price'     => ['type' => 'DECIMAL',  'constraint' => "20,6", 'default' => '0.000000'],
            'width'               => ['type' => 'DECIMAL',  'constraint' => "20,6", 'default' => '0.000000'],
            'height'              => ['type' => 'DECIMAL',  'constraint' => "20,6", 'default' => '0.000000'],
            'depth'               => ['type' => 'DECIMAL',  'constraint' => "20,6", 'default' => '0.000000'],
            'weight'              => ['type' => 'DECIMAL',  'constraint' => "20,6", 'default' => '0.000000'],
            'active'              => ['type' => 'TINYINT',  'constraint' => 1],
            'created_at'          => ['type' => 'datetime', 'null' => true],
            'updated_at'          => ['type' => 'datetime', 'null' => true],
            'deleted_at'          => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_category_default');
        $this->forge->addKey('supplier_id');
        $this->forge->addKey('brand_id');
        $this->forge->addKey('shop_id');
        $this->forge->addKey('taxe_rules_group_id');
        $this->forge->addKey('created_at');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('ec_products', true);


        /*
         * Product lang
         */
        $fields = [
            'id_product_lang' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'product_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_lang'           => ['type' => 'INT', 'constraint' => 11],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'sous_name'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'description_short' => ['type' => 'TEXT'],
            'description'       => ['type' => 'TEXT'],
            'meta_title'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'meta_description'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'tags'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id_product_lang', true);
        $this->forge->addKey('id_lang');
        $this->forge->addForeignKey('product_id', 'ec_products', 'id', false, 'CASCADE');
        $this->forge->createTable('ec_products_langs', true);

        /*
         * Product
         */
        /* CATEGORYS */
        $fields = [
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_parent'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'order'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'active'     => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addKey('created_at');
        $this->forge->addKey('updated_at');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('ec_categories');


        $fields = [
            'id_categorie_lang' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'category_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'id_lang'           => ['type' => 'INT', 'constraint' => 11],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'description_short' => ['type' => 'TEXT'],
            'meta_title'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'meta_description'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id_categorie_lang', true);
        $this->forge->addKey('id_lang');
        $this->forge->addForeignKey('category_id', 'ec_categories', 'id', false, 'CASCADE');
        $this->forge->createTable('ec_categories_langs', true);

        $fields = [
            'product_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true]
        ];

        $this->forge->addField($fields);
        $this->forge->addForeignKey('product_id', 'ec_products', 'id', false, 'CASCADE');
        $this->forge->addForeignKey('category_id', 'ec_categories', 'id', false, false);
        $this->forge->createTable('ec_products_categories', true);



        /*
         * Product
         */
        /* MARQUES */
        $fields = [
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'active'     => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addKey('created_at');
        $this->forge->addKey('updated_at');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('ec_brands');


        $fields = [
            'id_brand_lang' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'brand_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'id_lang'              => ['type' => 'INT', 'constraint' => 11],
            'description'          => ['type' => 'TEXT'],
            'description_short'    => ['type' => 'TEXT'],
            'meta_title'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'meta_description'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'                 => ['type' => 'VARCHAR', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id_brand_lang', true);
        $this->forge->addKey('id_lang');
        $this->forge->addForeignKey('brand_id', 'ec_brands', 'id', false, 'CASCADE');
        $this->forge->createTable('ec_brands_langs', true);


         /*
         * Product
         */
        /* Fournisseurs */
        $fields = [
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'active'     => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addKey('created_at');
        $this->forge->addKey('updated_at');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('ec_suppliers');


        $fields = [
            'id_supplier_lang'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'supplier_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'id_lang'           => ['type' => 'INT', 'constraint' => 11],
            'description'       => ['type' => 'TEXT'],
            'description_short' => ['type' => 'TEXT'],
            'meta_title'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'meta_description'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id_supplier_lang', true);
        $this->forge->addKey('id_lang');
        $this->forge->addForeignKey('supplier_id', 'ec_suppliers', 'id', false, 'CASCADE');
        $this->forge->createTable('ec_suppliers_langs', true);

    }



    //--------------------------------------------------------------------

    public function down()
    {
        $this->forge->dropTable('ec_products', true);
        $this->forge->dropTable('ec_products_langs', true);
        $this->forge->dropTable('ec_categories', true);
        $this->forge->dropTable('ec_categories_langs', true);
        $this->forge->dropTable('ec_products_categories', true);
        $this->forge->dropTable('ec_brands', true);
        $this->forge->dropTable('ec_brands_langs', true); 
        $this->forge->dropTable('ec_suppliers', true);
        $this->forge->dropTable('ec_suppliers_langs', true);


    }
}
