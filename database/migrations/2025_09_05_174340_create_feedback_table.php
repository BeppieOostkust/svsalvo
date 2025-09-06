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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['idea', 'feedback', 'suggestion', 'bug_report', 'feature_request']);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'implemented', 'closed'])->default('pending');
            $table->text('moderator_notes')->nullable();
            $table->text('admin_response')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('moderator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('upvotes')->default(0);
            $table->integer('downvotes')->default(0);
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
