<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'password_reset_sms_token')) {
                $table->string('password_reset_sms_token', 255)->nullable()->after('password');
            }

            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('remember_token');
            }

            if (!Schema::hasColumn('users', 'password_reset_token')) {
                $table->string('password_reset_token', 255)->nullable()->after('password_reset_sms_token');
            }

            if (!Schema::hasColumn('users', 'password_reset_expires_at')) {
                $table->timestamp('password_reset_expires_at')->nullable()->after('password_reset_token');
            }

            if (!Schema::hasColumn('users', 'password_reset_method')) {
                $table->string('password_reset_method', 50)->nullable()->after('password_reset_expires_at');
            }

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (Schema::hasColumn('users', 'password_reset_method')) {
                $table->dropColumn('password_reset_method');
            }

            if (Schema::hasColumn('users', 'password_reset_expires_at')) {
                $table->dropColumn('password_reset_expires_at');
            }

            if (Schema::hasColumn('users', 'password_reset_token')) {
                $table->dropColumn('password_reset_token');
            }

            if (Schema::hasColumn('users', 'phone_verified_at')) {
                $table->dropColumn('phone_verified_at');
            }

            if (Schema::hasColumn('users', 'password_reset_sms_token')) {
                $table->dropColumn('password_reset_sms_token');
            }

        });
    }
};
