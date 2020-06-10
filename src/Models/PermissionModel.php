<?php

namespace Adnduweb\Ci4_ecommerce\Authorization;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'authf_permissions';

    protected $allowedFields = [
        'name', 'description'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'name' => 'required|max_length[255]|is_unique[authf_permissions.name,name,{name}]',
        'description' => 'max_length[255]',
    ];

    /**
     * Checks to see if a user, or one of their groups,
     * has a specific permission.
     *
     * @param $customerId
     * @param $permissionId
     *
     * @return bool
     */
    public function doesUserHavePermission(int $customerId, int $permissionId): bool
    {
        // Check user permissions and take advantage of caching
        $userPerms = $this->getPermissionsForCustomer($customerId);

        if (count($userPerms) && array_key_exists($permissionId, $userPerms)) {
            return true;
        }

        // Check group permissions
        $count = $this->db->table('authf_groups_permissions')
            ->join('authf_groups_users', 'authf_groups_users.group_id = authf_groups_permissions.group_id', 'inner')
            ->where('authf_groups_permissions.permission_id', $permissionId)
            ->where('authf_groups_users.customer_id', $customerId)
            ->countAllResults();

        return $count > 0;
    }

    /**
     * Adds a single permission to a single user.
     *
     * @param int $permissionId
     * @param int $customerId
     *
     * @return \CodeIgniter\Database\BaseResult|\CodeIgniter\Database\Query|false
     */
    public function addPermissionToCustomer(int $permissionId, int $customerId)
    {
        cache()->delete("{$customerId}_authfpermissions");

        return $this->db->table('authf_users_permissions')->insert([
            'customer_id' => $customerId,
            'permission_id' => $permissionId
        ]);
    }

    /**
     * Removes a permission from a user.
     *
     * @param int $permissionId
     * @param int $customerId
     *
     * @return mixed
     */
    public function removePermissionFromUser(int $permissionId, int $customerId)
    {
        $this->db->table('authf_users_permissions')->where([
            'customer_id' => $customerId,
            'permission_id' => $permissionId
        ])->delete();

        cache()->delete("{$customerId}_authfpermissions");
    }

    /**
     * Gets all permissions for a user in a way that can be
     * easily used to check against:
     *
     * [
     *  id => name,
     *  id => name
     * ]
     *
     * @param int $customerId
     *
     * @return array
     */
    public function getPermissionsForUser(int $customerId): array
    {
        if (!$found = cache("{$customerId}_authfpermissions")) {
            $fromUser = $this->db->table('authf_users_permissions')
                ->select('id, authf_permissions.name')
                ->join('authf_permissions', 'authf_permissions.id = permission_id', 'inner')
                ->where('customer_id', $customerId)
                ->get()
                ->getResultObject();
            $fromGroup = $this->db->table('authf_groups_users')
                ->select('authf_permissions.id, authf_permissions.name')
                ->join('authf_groups_permissions', 'authf_groups_permissions.group_id = authf_groups_users.group_id', 'inner')
                ->join('authf_permissions', 'authf_permissions.id = authf_groups_permissions.permission_id', 'inner')
                ->where('customer_id', $customerId)
                ->get()
                ->getResultObject();

            $combined = array_merge($fromUser, $fromGroup);

            $found = [];
            foreach ($combined as $row) {
                $found[$row->id] = strtolower($row->name);
            }

            cache()->save("{$customerId}_authfpermissions", $found, 300);
        }

        return $found;
    }
}
