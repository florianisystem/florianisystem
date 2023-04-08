<?php

declare(strict_types=1);

namespace Engelsystem\Migrations;

use Engelsystem\Database\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddOrganizationFeature extends Migration
{
    use Reference;

    /**
     * Run the migration
     */
    public function up(): void
    {
        $this->schema->create('organizations', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name', 64)->unique();
            $table->text('description')->nullable()->default(null);
            $table->string('email', 254)->nullable()->default(null);
            $table->string('phone', 40)->nullable()->default(null);
            $table->string('contact_person', 64)->nullable()->default(null);
            $table->timestamps();
        });
        
        $this->schema->table('users_settings', function (Blueprint $table): void {
            $table->unsignedInteger('organization_id')->nullable()->default(null)->after('theme');

            $table->index('organization_id');
            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnUpdate()->nullOnDelete();
        });

    }

    public function down(): void
    {
        $this->schema->table('users_settings', function (Blueprint $table): void {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });

        $this->schema->drop('organizations');
    }

}
