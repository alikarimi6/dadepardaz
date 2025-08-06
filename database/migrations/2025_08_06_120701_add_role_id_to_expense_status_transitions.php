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
        Schema::table('expense_status_transitions', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->foreignId('role_id')->default(0)->constrained('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_status_transitions', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->string('role');
        });
    }
};
