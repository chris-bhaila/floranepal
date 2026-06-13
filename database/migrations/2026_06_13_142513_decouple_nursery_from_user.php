<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nurseries', function (Blueprint $table) {
            // Drop the cascading foreign key so deleting a user no longer deletes their nursery
            $table->dropForeign(['user_id']);

            // Allow user_id to be null so the nursery persists after a user is deleted
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Re-add the foreign key with SET NULL so the column is cleared on user deletion
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('nurseries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
