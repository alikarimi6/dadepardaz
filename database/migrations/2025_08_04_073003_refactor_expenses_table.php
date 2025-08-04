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
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('state_id')->after('category_id')->default(1)->constrained('states');
            $table->dropColumn('state');
            $table->dropColumn('rejection_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('state')->default('requested');
            $table->text('rejection_comment')->nullable();
            $table->dropForeign(['state_id']);
            $table->dropColumn('state_id');
        });
    }
};
