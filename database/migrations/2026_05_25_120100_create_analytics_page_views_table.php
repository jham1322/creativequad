<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_page_views', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_key')->index();
            $table->string('session_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('path')->index();
            $table->string('route_name')->nullable()->index();
            $table->text('referrer')->nullable();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->timestamp('viewed_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_page_views');
    }
};
