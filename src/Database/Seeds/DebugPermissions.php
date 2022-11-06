<?php

/*
 * TLP
 *
 * @link      https://github.com/jv-k/annaslittleprince
 * @copyright Copyright (c) 2022 John Valai
 * @license   UNLICENSED
 */

namespace UserFrosting\Sprinkle\UfDebug\Database\Seeds;

use UserFrosting\Sprinkle\Account\Database\Models\Permission;
use UserFrosting\Sprinkle\Account\Database\Models\Role;
use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;
use UserFrosting\Sprinkle\Core\Facades\Seeder;

/**
 * Seeder for the TLP Maintenance UF permissions
 */
class DebugPermissions extends BaseSeed  {

    /**
     * {@inheritdoc}
     */
    public function run() {

        // Acquire the default roles, so we can add new permissions to admin
        Seeder::execute('DefaultRoles');

        // Get and save permissions
        $permissions = $this->getPermissions();
        $this->savePermissions($permissions);

        // Add  mappings to permissions
        $this->syncPermissionsRole($permissions);

        $TlpPerm = Permission::where('slug', 'debug_access')->first();
    }

    /**
     * @return array Permission(s) to seed
     */
    protected function getPermissions() {

        return [
            'debug_access' => new Permission([
                'slug' => 'debug_access',
                'name' => 'Debug section access',
                'conditions' => 'always()',
                'descriptions' => 'Allow user to access the Debug section',
            ])
        ];
    }

    /**
     * Save custom permissions (taken from Account Sprinkle)
     *
     * @param array $permissions
     */
    protected function savePermissions(array &$permissions) {

        foreach ($permissions as $slug => $permission) {

            // Trying to find if the permission already exist
            $existingPermission = Permission::where(['slug' => $permission->slug, 'conditions' => $permission->conditions])->first();

            // Don't save if already exist, use existing permission reference
            // otherwise to re-sync permissions and roles
            if ($existingPermission == null) {
                $permission->save();
            } else {
                $permissions[$slug] = $existingPermission;
            }
        }
    }

   /**
     * Sync custom permission(s) to  relevant role(s)
     *
     * @param array $permissions
     */
    protected function syncPermissionsRole(array $permissions) {
        
        $roleSiteAdmin = Role::where('slug', 'site-admin')->first();
        if ($roleSiteAdmin) {
            $roleSiteAdmin->permissions()->syncWithoutDetaching([
                $permissions['debug_access']->id,
            ]);
        }
    }
}