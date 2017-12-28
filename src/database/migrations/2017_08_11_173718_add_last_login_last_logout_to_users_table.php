<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastLoginLastLogoutToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login')->after('remember_token')->nullable();
            $table->timestamp('last_logout')->after('last_login')->nullable();
            $table->timestamp('verified_at')->after('last_logout')->nullable();
            $table->boolean('disabled')->after('verified_at')->default(0);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
