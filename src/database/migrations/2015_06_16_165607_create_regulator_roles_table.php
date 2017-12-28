<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegulatorRolesTable extends Migration
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
        Schema::create($this->table_prefix . 'regulator_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->integer('level')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop($this->table_prefix . 'regulator_roles');
    }
}
