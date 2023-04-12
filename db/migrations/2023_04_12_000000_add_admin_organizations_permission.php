<?php

declare(strict_types=1);

namespace Engelsystem\Migrations;

use Engelsystem\Database\Migration\Migration;

class AddAdminOrganizationPermission extends Migration
{
    /**
     * Run the migration
     */
    public function up(): void
    {
        $db = $this->schema->getConnection();
        $db->table('privileges')
            ->insert(['name' => 'admin_organizations', 'description' => 'Organisationen administrieren']);

        $adminOrganizations = $db->table('privileges')
            ->where('name', 'admin_organizations')
            ->get(['id'])
            ->first();

        $buerocrat = 80;
        $db->table('group_privileges')
            ->insertOrIgnore([
                ['group_id' => $buerocrat, 'privilege_id' => $adminOrganizations->id],
            ]);
    }

    /**
     * Reverse the migration
     */
    public function down(): void
    {
        $db = $this->schema->getConnection();
        $db->table('privileges')
            ->where(['name' => 'admin_organizations'])
            ->delete();
    }
}
