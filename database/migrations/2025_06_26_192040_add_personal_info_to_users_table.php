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
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('last_name');
            }
            
            // Only add phone if it doesn't already exist
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('date_of_birth');
            }
            
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->default('Nederland')->after('postal_code');
            }
            
            // Club/Organization Information (optional - for board members etc.)
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('country'); // Chairman, Secretary, etc.
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('position');
            }
            if (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'show_in_organization')) {
                $table->boolean('show_in_organization')->default(false)->after('profile_image');
            }
            if (!Schema::hasColumn('users', 'organization_sort_order')) {
                $table->integer('organization_sort_order')->default(0)->after('show_in_organization');
            }
            if (!Schema::hasColumn('users', 'member_since')) {
                $table->date('member_since')->nullable()->after('organization_sort_order');
            }
            
            // Shooting Information
            if (!Schema::hasColumn('users', 'preferred_discipline')) {
                $table->string('preferred_discipline')->nullable()->after('member_since'); // pistol, rifle, etc.
            }
            if (!Schema::hasColumn('users', 'license_number')) {
                $table->string('license_number')->nullable()->after('preferred_discipline');
            }
            if (!Schema::hasColumn('users', 'license_expiry')) {
                $table->date('license_expiry')->nullable()->after('license_number');
            }
            
            // Privacy Settings
            if (!Schema::hasColumn('users', 'show_contact_info')) {
                $table->boolean('show_contact_info')->default(false)->after('license_expiry');
            }
            if (!Schema::hasColumn('users', 'show_scores_public')) {
                $table->boolean('show_scores_public')->default(false)->after('show_contact_info');
            }
            
            // Status
            if (!Schema::hasColumn('users', 'is_active_member')) {
                $table->boolean('is_active_member')->default(true)->after('is_admin');
            }
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
