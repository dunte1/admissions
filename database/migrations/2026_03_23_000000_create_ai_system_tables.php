<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ai_name')->default('Eliana D');
            $table->string('ai_icon')->nullable();
            $table->string('primary_color')->default('#7C3AED');
            $table->string('secondary_color')->default('#10B981');
            $table->text('welcome_message')->nullable();
            $table->string('tone')->default('professional'); // professional, friendly, formal
            $table->string('mode')->default('hybrid'); // sales, support, hybrid
            $table->boolean('enabled')->default(true);
            $table->boolean('human_handoff_enabled')->default(true);
            $table->json('escalation_phrases')->nullable();
            $table->string('api_key')->nullable()->unique();
            $table->boolean('embed_enabled')->default(false);
            $table->text('embed_script')->nullable();
            $table->timestamps();

            $table->index('school_id');
        });

        Schema::create('ai_knowledge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('type'); // faq, document, crawled, policy
            $table->string('source_url')->nullable();
            $table->string('source_type')->nullable(); // manual, webhook, crawl
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('tokens')->default(0);
            $table->timestamps();

            $table->index(['school_id', 'type']);
            $table->index(['school_id', 'is_active']);
        });

        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id')->index();
            $table->string('mode'); // sales, system, embed
            $table->text('initial_message')->nullable();
            $table->string('status')->default('active'); // active, escalated, resolved, closed
            $table->foreignId('escalated_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('resolution_notes')->nullable();
            $table->json('context')->nullable();
            $table->unsignedInteger('message_count')->default(0);
            $table->timestamps();

            $table->index(['school_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_conversations')->onDelete('cascade');
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('role'); // user, assistant, system
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->boolean('is_escalation')->default(false);
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });

        Schema::create('ai_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('event_type'); // chat_started, message_sent, escalation, conversion, etc.
            $table->string('session_id')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'event_type', 'created_at']);
            $table->index(['created_at']);
        });

        Schema::create('ai_crawl_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->unsignedInteger('pages_crawled')->default(0);
            $table->unsignedInteger('items_indexed')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'status']);
        });

        Schema::create('ai_admission_flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id')->index();
            $table->string('status')->default('in_progress'); // in_progress, completed, abandoned
            $table->unsignedInteger('current_step')->default(1);
            $table->json('collected_data')->nullable();
            $table->foreignId('application_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['school_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_admission_flows');
        Schema::dropIfExists('ai_crawl_jobs');
        Schema::dropIfExists('ai_analytics');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('ai_knowledge');
        Schema::dropIfExists('ai_settings');
    }
};
