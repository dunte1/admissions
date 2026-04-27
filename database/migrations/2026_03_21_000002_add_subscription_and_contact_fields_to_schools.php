<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->enum('subscription_status', ['trial', 'active', 'expired', 'grace_period', 'suspended', 'cancelled'])
                ->default('active')
                ->after('status');
            
            $table->dateTime('trial_ends_at')->nullable()->after('subscription_status');
            $table->dateTime('grace_ends_at')->nullable()->after('trial_ends_at');
            
            $table->string('county')->nullable()->after('address');
            $table->string('town')->nullable()->after('county');
            $table->string('postal_code')->nullable()->after('town');
            $table->string('website')->nullable()->after('postal_code');
            $table->string('facebook')->nullable()->after('website');
            $table->string('twitter')->nullable()->after('facebook');
            $table->string('instagram')->nullable()->after('twitter');
            $table->string('linkedin')->nullable()->after('instagram');
            
            $table->string('admin_name')->nullable()->after('linkedin');
            $table->string('admin_email')->nullable()->after('admin_name');
            $table->string('admin_phone')->nullable()->after('admin_email');
            
            $table->string('admissions_contact_name')->nullable()->after('admin_phone');
            $table->string('admissions_contact_email')->nullable()->after('admissions_contact_name');
            $table->string('admissions_contact_phone')->nullable()->after('admissions_contact_email');
            
            $table->string('finance_contact_name')->nullable()->after('admissions_contact_phone');
            $table->string('finance_contact_email')->nullable()->after('finance_contact_name');
            $table->string('finance_contact_phone')->nullable()->after('finance_contact_email');
            
            $table->text('notes')->nullable()->after('finance_contact_phone');
            
            $table->index('subscription_status');
            $table->index('trial_ends_at');
            $table->index('grace_ends_at');
            $table->index('county');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->integer('grace_period_days')->default(3)->after('duration_days');
            $table->boolean('is_trialable')->default(true)->after('is_active');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dateTime('trial_ends_at')->nullable()->after('cancelled_at');
            $table->dateTime('grace_ends_at')->nullable()->after('trial_ends_at');
            $table->boolean('is_trial')->default(false)->after('grace_ends_at');
            $table->text('admin_notes')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex(['subscription_status']);
            $table->dropIndex(['trial_ends_at']);
            $table->dropIndex(['grace_ends_at']);
            $table->dropIndex(['county']);
            
            $columns = [
                'subscription_status', 'trial_ends_at', 'grace_ends_at',
                'county', 'town', 'postal_code', 'website',
                'facebook', 'twitter', 'instagram', 'linkedin',
                'admin_name', 'admin_email', 'admin_phone',
                'admissions_contact_name', 'admissions_contact_email', 'admissions_contact_phone',
                'finance_contact_name', 'finance_contact_email', 'finance_contact_phone',
                'notes'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('schools', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'grace_period_days')) {
                $table->dropColumn('grace_period_days');
            }
            if (Schema::hasColumn('plans', 'is_trialable')) {
                $table->dropColumn('is_trialable');
            }
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $columns = ['trial_ends_at', 'grace_ends_at', 'is_trial', 'admin_notes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('subscriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
