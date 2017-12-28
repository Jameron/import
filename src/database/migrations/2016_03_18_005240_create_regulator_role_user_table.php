<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegulatorRoleUserTable extends Migration
{
    public $table_prefix;

    public function __construct()
    {
        $this->table_prefix = config('regulator.db_table_prefix');
        if (!empty($this->table_prefix)) {
            $this->table_prefix = $this->table_prefix . (substr($this->table_prefix, -1)=='_') ? '' : '_';
        }
    }

    public function up()
    {
        Schema::create($this->table_prefix . 'regulator_role_user', function (Blueprint $table) {
            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on($this->table_prefix . 'regulator_roles')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['role_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::drop($this->table_prefix . 'regulator_role_user');
    }
}
