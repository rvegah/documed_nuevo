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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('company_name', 255)->after('id');
            $table->string('email', 255)->nullable()->after('company_name');
            $table->string('phone', 50)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('postal_code', 20)->nullable()->after('city');
            $table->string('province', 100)->nullable()->after('postal_code');
            $table->boolean('has_center_staff')->default(false)->after('rn_owner');
            $table->enum('company_type', ['clinic', 'professional', 'both'])->default('clinic')->after('has_center_staff');
            $table->text('notes')->nullable()->after('company_type');
            $table->timestamp('completed_at')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'company_name', 'email', 'phone', 'address', 'city', 
                'postal_code', 'province', 'has_center_staff', 
                'company_type', 'notes', 'completed_at'
            ]);
        });
    }
};