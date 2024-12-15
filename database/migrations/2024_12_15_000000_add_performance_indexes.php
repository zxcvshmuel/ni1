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
        // Add indexes to message_logs
        Schema::table('message_logs', function (Blueprint $table) {
            $table->index(['type', 'status']); // For filtering and reporting
            $table->index(['invitation_id', 'status']); // For invitation status checks
            $table->index('sent_at'); // For date-based queries
        });

        // Add indexes to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'created_at']); // For payment processing and reporting
            $table->index(['user_id', 'status']); // For user purchase history
            $table->index('payment_id'); // For payment gateway callbacks
        });

        // Add indexes to invitations
        Schema::table('invitations', function (Blueprint $table) {
            $table->index(['user_id', 'event_date']); // For user's upcoming events
            $table->index(['event_date', 'is_active']); // For event scheduling
            $table->index(['slug', 'is_active']); // For public URL lookups
            $table->index('expires_at'); // For cleanup tasks
        });

        // Add indexes to rsvp_responses
        Schema::table('rsvp_responses', function (Blueprint $table) {
            $table->index(['invitation_id', 'status']); // For RSVP stats
            $table->index('email'); // For duplicate checks
            $table->index('phone'); // For duplicate checks
        });

        // Add indexes to settings
        Schema::table('settings', function (Blueprint $table) {
            $table->index(['group', 'name']); // For grouped settings lookup
        });

        // Add indexes to templates
        Schema::table('templates', function (Blueprint $table) {
            $table->index(['category_id', 'is_active']); // For template browsing
        });

        // Add indexes to songs and effects
        Schema::table('songs', function (Blueprint $table) {
            $table->index(['is_active', 'plays_count']); // For popular songs
        });

        Schema::table('effects', function (Blueprint $table) {
            $table->index(['type', 'is_active']); // For effect filtering
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove message_logs indexes
        Schema::table('message_logs', function (Blueprint $table) {
            $table->dropIndex(['type', 'status']);
            $table->dropIndex(['invitation_id', 'status']);
            $table->dropIndex(['sent_at']);
        });

        // Remove orders indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['payment_id']);
        });

        // Remove invitations indexes
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'event_date']);
            $table->dropIndex(['event_date', 'is_active']);
            $table->dropIndex(['slug', 'is_active']);
            $table->dropIndex(['expires_at']);
        });

        // Remove rsvp_responses indexes
        Schema::table('rsvp_responses', function (Blueprint $table) {
            $table->dropIndex(['invitation_id', 'status']);
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
        });

        // Remove settings indexes
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['group', 'name']);
        });

        // Remove template indexes
        Schema::table('templates', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'is_active']);
        });

        // Remove song and effect indexes
        Schema::table('songs', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'plays_count']);
        });

        Schema::table('effects', function (Blueprint $table) {
            $table->dropIndex(['type', 'is_active']);
        });
    }
};