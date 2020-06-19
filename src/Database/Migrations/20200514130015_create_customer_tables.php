<?php

namespace Adnduweb\Ci4_ecommerce\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_customer_tables extends Migration
{
    public function up()
    {
        /*
         * customer
         */
        $this->forge->addField([
            'id'               => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'uuid'             => ['type' => 'BINARY', 'constraint' => 16, 'unique' => true],
            'lastname'         => ['type' => 'VARCHAR',  'constraint' => 255],
            'firstname'        => ['type' => 'VARCHAR',  'constraint' => 255,  'null' => true],
            'email'            => ['type' => 'varchar', 'constraint' => 255],
            'username'         => ['type' => 'varchar', 'constraint' => 30, 'null' => true],
            'password_hash'    => ['type' => 'varchar', 'constraint' => 255],
            'reset_hash'       => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'reset_at'         => ['type' => 'datetime', 'null' => true],
            'reset_expires'    => ['type' => 'datetime', 'null' => true],
            'activate_hash'    => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status'           => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'status_message'   => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'active'           => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
            'force_pass_reset' => ['type' => 'tinyint', 'constraint' => 1, 'null' => 0, 'default' => 0],
            'created_at'       => ['type' => 'datetime', 'null' => true],
            'updated_at'       => ['type' => 'datetime', 'null' => true],
            'deleted_at'       => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('username');

        $this->forge->createTable('authf_customer', true);

        /*
         * Auth Login Attempts
         */
        $this->forge->addField([
            'id'          => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ip_address'  => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'email'       => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'customer_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],             // Only for successful logins
            'date'        => ['type' => 'datetime'],
            'success'     => ['type' => 'tinyint', 'constraint' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('email');
        $this->forge->addKey('customer_id');
        // NOTE: Do NOT delete the customer_id or email when the user is deleted for security audits
        $this->forge->createTable('authf_logins', true);

        /*
         * Auth Tokens
         * @see https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
         */
        $this->forge->addField([
            'id'              => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'selector'        => ['type' => 'varchar', 'constraint' => 255],
            'hashedValidator' => ['type' => 'varchar', 'constraint' => 255],
            'customer_id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'expires'         => ['type' => 'datetime'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('selector');
        $this->forge->addForeignKey('customer_id', 'authf_customer', 'id', false, 'CASCADE');
        $this->forge->createTable('authf_tokens', true);

        /*
         * Password Reset Table
         */
        $this->forge->addField([
            'id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email'      => ['type' => 'varchar', 'constraint' => 255],
            'ip_address' => ['type' => 'varchar', 'constraint' => 255],
            'user_agent' => ['type' => 'varchar', 'constraint' => 255],
            'token'      => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('authf_reset_attempts');

        /*
         * Activation Attempts Table
         */
        $this->forge->addField([
            'id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ip_address' => ['type' => 'varchar', 'constraint' => 255],
            'user_agent' => ['type' => 'varchar', 'constraint' => 255],
            'token'      => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('authf_activation_attempts');

        /*
         * Groups Table
         */
        $fields = [
            'id'          => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'varchar', 'constraint' => 255],
            'description' => ['type' => 'varchar', 'constraint' => 255],
            'login_destination' => ['type' => 'VARCHAR', 'constraint' => 255, 'after' => 'description'],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('authf_groups', true);

        /*
         * Permissions Table
         */
        $fields = [
            'id'          => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'varchar', 'constraint' => 255],
            'description' => ['type' => 'varchar', 'constraint' => 255],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('authf_permissions', true);

        /*
         * Groups/Permissions Table
         */
        $fields = [
            'group_id'      => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'permission_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey(['group_id', 'permission_id']);
        $this->forge->addForeignKey('group_id', 'authf_groups', 'id', false, 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'authf_permissions', 'id', false, 'CASCADE');
        $this->forge->createTable('authf_groups_permissions', true);

        /*
         * customer/Groups Table
         */
        $fields = [
            'group_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'customer_id'  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey(['group_id', 'customer_id']);
        $this->forge->addForeignKey('group_id', 'authf_groups', 'id', false, 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'authf_customer', 'id', false, 'CASCADE');
        $this->forge->createTable('authf_groups_customer', true);

        /*
         * customer/Permissions Table
         */
        $fields = [
            'customer_id'       => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'permission_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey(['customer_id', 'permission_id']);
        $this->forge->addForeignKey('customer_id', 'authf_customer', 'id', false, 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'authf_permissions', 'id', false, 'CASCADE');
        $this->forge->createTable('authf_customer_permissions');

        //--------------------------------------------------------------------
        //----ECOMMERCE
        //--------------------------------------------------------------------

        /*
         * Product
         */
        $this->forge->addField([
            'id'                  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_category_default' => ['type' => 'int',  'constraint' => 11, 'default' => 1],
            'id_location'         => ['type' => 'int',  'constraint' => 11, 'default' => 1],
            'id_supplier'         => ['type' => 'int',  'constraint' => 11, 'default' => 1],
            'id_manufacturer'     => ['type' => 'int',  'constraint' => 11, 'default' => 1],
            'id_taxe'             => ['type' => 'int',  'constraint' => 11, 'default' => 1],
            'sku'                 => ['type' => 'VARCHAR',  'constraint' => 255],
            'ean13'               => ['type' => 'VARCHAR',  'constraint' => 13],
            'on_sale'             => ['type' => 'TINYINT',  'constraint' => 1],
            'quantity'            => ['type' => 'INT',  'constraint' => 10],
            'quantity_minimal'    => ['type' => 'INT',  'constraint' => 10],
            'price'               => ['type' => 'DECIMAL',  'constraint' => 20.6],
            'wholesale_price'     => ['type' => 'DECIMAL',  'constraint' => 20.6],
            'width'               => ['type' => 'DECIMAL',  'constraint' => 20.6],
            'height'              => ['type' => 'DECIMAL',  'constraint' => 20.6],
            'depth'               => ['type' => 'DECIMAL',  'constraint' => 20.6],
            'weight'              => ['type' => 'DECIMAL',  'constraint' => 20.6],
            'active'              => ['type' => 'TINYINT',  'constraint' => 1],
            'created_at'          => ['type' => 'datetime', 'null' => true],
            'updated_at'          => ['type' => 'datetime', 'null' => true],
            'deleted_at'          => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_category_default');
        $this->forge->addKey('id_supplier');
        $this->forge->addKey('id_manufacturer');
        $this->forge->addKey('id_location');
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
    }





    //--------------------------------------------------------------------

    public function down()
    {
        // drop constraints first to prevent errors
        if ($this->db->DBDriver != 'SQLite3') {
            $this->forge->dropForeignKey('authf_tokens', 'authf_tokens_customer_id_foreign');
            $this->forge->dropForeignKey('authf_groups_permissions', 'authf_groups_permissions_group_id_foreign');
            $this->forge->dropForeignKey('authf_groups_permissions', 'authf_groups_permissions_permission_id_foreign');
            $this->forge->dropForeignKey('authf_groups_customer', 'authf_groups_customer_group_id_foreign');
            $this->forge->dropForeignKey('authf_groups_customer', 'authf_groups_customer_customer_id_foreign');
            $this->forge->dropForeignKey('authf_customer_permissions', 'authf_customer_permissions_customer_id_foreign');
            $this->forge->dropForeignKey('authf_customer_permissions', 'authf_customer_permissions_permission_id_foreign');
        }

        $this->forge->dropTable('authf_customer', true);
        $this->forge->dropTable('authf_logins', true);
        $this->forge->dropTable('authf_tokens', true);
        $this->forge->dropTable('authf_reset_attempts', true);
        $this->forge->dropTable('authf_activation_attempts', true);
        $this->forge->dropTable('authf_groups', true);
        $this->forge->dropTable('authf_permissions', true);
        $this->forge->dropTable('authf_groups_permissions', true);
        $this->forge->dropTable('authf_groups_customer', true);
        $this->forge->dropTable('authf_customer_permissions', true);

        $this->forge->dropTable('ec_products', true);
        $this->forge->dropTable('ec_products_langs', true);
        $this->forge->dropTable('ec_categories', true);
        $this->forge->dropTable('ec_categories_langs', true);
        $this->forge->dropTable('ec_products_category', true);
    }
}
