<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->string('source', 50)->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['email', 'is_active']);
        });

        Schema::create('crm_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->string('color', 20)->nullable();
            $table->string('icon', 50)->nullable();
            $table->boolean('is_dynamic')->default(false);
            $table->json('criteria')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('crm_contact_segment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('crm_contacts')->cascadeOnDelete();
            $table->foreignId('segment_id')->constrained('crm_segments')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['contact_id', 'segment_id']);
        });

        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('crm_contacts')->cascadeOnDelete();
            $table->string('status', 50)->default('new');
            $table->string('source', 50)->nullable();
            $table->integer('score')->default(0);
            $table->decimal('expected_value', 15, 2)->default(0);
            $table->ulid('assigned_to')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->string('lost_reason')->nullable();
            $table->timestamp('next_follow_up')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['contact_id', 'status']);
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->morphs('subject');
            $table->string('type', 50);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->ulid('performed_by')->nullable();
            $table->timestamp('performed_at');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['subject_type', 'subject_id', 'type']);
            $table->foreign('performed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('crm_contact_segment');
        Schema::dropIfExists('crm_segments');
        Schema::dropIfExists('crm_contacts');
    }
};
