<?php

namespace Adnduweb\Ci4_ecommerce\Models;

use CodeIgniter\Model;

class GroupModel extends Model
{
    protected $table = 'authf_groups';
    protected $primaryKey = 'id';

    protected $returnType = 'object';
    protected $allowedFields = [
        'name', 'description'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'name' => 'required|max_length[255]|is_unique[authf_groups.name,name,{name}]',
        'description' => 'max_length[255]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    //--------------------------------------------------------------------
    // Users
    //--------------------------------------------------------------------

    /**
     * Adds a single user to a single group.
     *
     * @param $customerId
     * @param $groupId
     *
     * @return object
     */
    public function addCustomerToGroup(int $customerId, int $groupId)
    {
        cache()->delete("{$customerId}_authfgroups");

        $data = [
            'customer_id'   => (int) $customerId,
            'group_id'  => (int) $groupId
        ];

        return $this->db->table('authf_groups_customer')->insert($data);
    }

    /**
     * Removes a single user from a single group.
     *
     * @param $customerId
     * @param $groupId
     *
     * @return bool
     */
    public function removeCustomerFromGroup(int $customerId, $groupId)
    {
        cache()->delete("{$customerId}_authfgroups");

        return $this->db->table('authf_groups_customer')
            ->where([
                'customer_id' => (int) $customerId,
                'group_id' => (int) $groupId
            ])->delete();
    }

    /**
     * Removes a single user from all groups.
     *
     * @param $customerId
     *
     * @return mixed
     */
    public function removeCustomerFromAllGroups(int $customerId)
    {
        cache()->delete("{$customerId}_authfgroups");

        return $this->db->table('authf_groups_customer')
            ->where('customer_id', (int) $customerId)
            ->delete();
    }

    /**
     * Returns an array of all groups that a user is a member of.
     *
     * @param $customerId
     *
     * @return object
     */
    public function getGroupsForCustomer(int $customerId)
    {
        if (!$found = cache("{$customerId}_authfgroups")) {
            $found = $this->builder()
                ->select('authf_groups_customer.*, authf_groups.name, authf_groups.description')
                ->join('authf_groups_customer', 'authf_groups_customer.group_id = authf_groups.id', 'left')
                ->where('customer_id', $customerId)
                ->get()->getResultArray();

            cache()->save("{$customerId}_authfgroups", $found, 300);
        }

        return $found;
    }


    //--------------------------------------------------------------------
    // Permissions
    //--------------------------------------------------------------------

    /**
     * Gets all permissions for a group in a way that can be
     * easily used to check against:
     *
     * [
     *  id => name,
     *  id => name
     * ]
     *
     * @param int $groupId
     *
     * @return array
     */
    public function getPermissionsForGroup(int $groupId): array
    {
        $permissionModel = model(PermissionModel::class);
        $fromGroup = $permissionModel
            ->select('authf_permissions.*')
            ->join('authf_groups_permissions', 'authf_groups_permissions.permission_id = authf_permissions.id', 'inner')
            ->where('group_id', $groupId)
            ->findAll();

        $found = [];
        foreach ($fromGroup as $permission) {
            $found[$permission['id']] = $permission;
        }

        return $found;
    }

    /**
     * Add a single permission to a single group, by IDs.
     *
     * @param $permissionId
     * @param $groupId
     *
     * @return mixed
     */
    public function addPermissionToGroup(int $permissionId, int $groupId)
    {
        $data = [
            'permission_id' => (int) $permissionId,
            'group_id'      => (int) $groupId
        ];

        return $this->db->table('authf_groups_permissions')->insert($data);
    }

    //--------------------------------------------------------------------


    /**
     * Removes a single permission from a single group.
     *
     * @param $permissionId
     * @param $groupId
     *
     * @return mixed
     */
    public function removePermissionFromGroup(int $permissionId, int $groupId)
    {
        return $this->db->table('authf_groups_permissions')
            ->where([
                'permission_id' => $permissionId,
                'group_id'      => $groupId
            ])->delete();
    }

    //--------------------------------------------------------------------

    /**
     * Removes a single permission from all groups.
     *
     * @param $permissionId
     *
     * @return mixed
     */
    public function removePermissionFromAllGroups(int $permissionId)
    {
        return $this->db->table('authf_groups_permissions')
            ->where('permission_id', $permissionId)
            ->delete();
    }
}
