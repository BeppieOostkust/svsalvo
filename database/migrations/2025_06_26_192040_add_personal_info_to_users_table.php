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
        Schema::table('users', function (Blueprint $table) {
            // Personal Information
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->date('date_of_birth')->nullable()->after('last_name');
            
            // Only add phone if it doesn't already exist
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('date_of_birth');
            }
            
            $table->text('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('postal_code')->nullable()->after('city');
            $table->string('country')->default('Nederland')->after('postal_code');
            
            // Club/Organization Information (optional - for board members etc.)
            $table->string('position')->nullable()->after('country'); // Chairman, Secretary, etc.
            $table->text('bio')->nullable()->after('position');
            $table->string('profile_image')->nullable()->after('bio');
            $table->boolean('show_in_organization')->default(false)->after('profile_image');
            $table->integer('organization_sort_order')->default(0)->after('show_in_organization');
            $table->date('member_since')->nullable()->after('organization_sort_order');
            
            // Shooting Information
            $table->string('preferred_discipline')->nullable()->after('member_since'); // pistol, rifle, etc.
            $table->string('license_number')->nullable()->after('preferred_discipline');
            $table->date('license_expiry')->nullable()->after('license_number');
            
            // Privacy Settings
            $table->boolean('show_contact_info')->default(false)->after('license_expiry');
            $table->boolean('show_scores_public')->default(true)->after('show_contact_info');
            
            // Status
            $table->boolean('is_active_member')->default(true)->after('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name', 
                'date_of_birth',
                'phone',
                'address',
                'city',
                'postal_code',
                'country',
                'position',
                'bio',
                'profile_image',
                'show_in_organization',
                'organization_sort_order',
                'member_since',
                'preferred_discipline',
                'license_number',
                'license_expiry',
                'show_contact_info',
                'show_scores_public',
                'is_active_member',
            ]);
        });
    }
};
