<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_key')->unique();
            $table->string('session_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('landing_path')->nullable()->index();
            $table->string('landing_route_name')->nullable()->index();
            $table->string('last_path')->nullable()->index();
            $table->string('last_route_name')->nullable()->index();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->text('referrer')->nullable();
            $table->unsignedInteger('page_views')->default(0);
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_visitors');
    }
};
