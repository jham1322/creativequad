<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('course_slug')->index();
            $table->string('course_name');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('PHP');
            $table->string('status', 30)->default('pending')->index();
            $table->string('payment_method', 40)->nullable();
            $table->string('xendit_reference')->nullable()->unique();
            $table->string('invoice_url')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('username')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('source', 20)->default('xendit');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
