<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('user_progress', function (Blueprint $table) {
        if (!Schema::hasColumn('user_progress', 'cintura')) {
            $table->decimal('cintura', 5, 2)->nullable()->after('altura');
        }
        if (!Schema::hasColumn('user_progress', 'notas')) {
            $table->text('notas')->nullable()->after('foto_path');
        }
    });
}

public function down(): void
{
    Schema::table('user_progress', function (Blueprint $table) {
        $table->dropColumn(['cintura', 'notas']);
    });
}
};