<?php

declare(strict_types=1);

namespace Engelsystem\Migrations;

use Engelsystem\Database\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeMapUrlLength extends Migration
{
    /**
     * Run the migration
     */
    public function up(): void
    {
        $this->schema->table('rooms', function (Blueprint $table): void {
            $table->string('map_url', 2000)->change();
        });
    }

    /**
     * Reverse the migration
     */
    public function down(): void
    {
        $this->schema->table('rooms', function (Blueprint $table): void {
            $table->string('map_url', 300)->change();
        });
    }
}
