 <?php

    namespace Adnduweb\Ci4_ecommerce\Entities;

    use Michalsn\Uuid\UuidEntity;

    class Customer extends UuidEntity
    {
        use \Tatter\Relations\Traits\EntityTrait;
        protected $table      = 'customer';
        protected $primaryKey = 'id_customer';
        protected $uuids      = ['uuid_customer'];

        protected $datamap = [];

        /**
         * Define properties that are automatically converted to Time instances.
         */
        protected $dates = ['reset_at', 'reset_expires', 'created_at', 'updated_at', 'deleted_at'];

        /**
         * Array of field names and the type of value to cast them as
         * when they are accessed.
         */
        protected $casts = [
            'active'           => 'boolean',
            'force_pass_reset' => 'boolean',
        ];
    }
