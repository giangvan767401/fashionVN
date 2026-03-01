<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id', 255)->nullable();
            $table->string('event_type', 50)->comment('page_view, product_view, add_to_cart, purchase...');
            $table->string('entity_type', 50)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id', 'idx_user');
            $table->index('event_type', 'idx_event_type');
            $table->index('created_at', 'idx_created');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 50)->comment('order_update, promotion, stock_alert...');
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->string('icon_url', 500)->nullable();
            $table->string('action_url', 500)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'is_read'], 'idx_user_read');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('newsletter_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 191)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('unsubscribed_at')->nullable();

            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('subject', 255);
            $table->longText('body_html');
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'cancelled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('recipient_count')->default(0);
            $table->unsignedInteger('open_count')->default(0);
            $table->unsignedInteger('click_count')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email', 191);
            $table->string('type', 50)->comment('order_confirm, reset_password, marketing...');
            $table->string('subject', 255);
            $table->unsignedInteger('campaign_id')->nullable();
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('status', ['queued', 'sent', 'failed', 'bounced'])->default('queued');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('error_message', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id', 'idx_user');
            $table->index('campaign_id', 'idx_campaign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('campaign_id')->references('id')->on('email_campaigns')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_campaigns');
        Schema::dropIfExists('newsletter_subscriptions');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('analytics_events');
    }
};
