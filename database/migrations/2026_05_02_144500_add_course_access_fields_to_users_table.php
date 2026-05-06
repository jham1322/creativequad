<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('username')->nullable()->unique()->after('last_name');
            $table->string('xendit_reference')->nullable()->unique()->after('remember_token');
            $table->string('course_slug')->nullable()->after('xendit_reference');
            $table->timestamp('purchased_at')->nullable()->after('course_slug');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropUnique(['xendit_reference']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'username',
                'xendit_reference',
                'course_slug',
                'purchased_at',
            ]);
        });
    }
};
