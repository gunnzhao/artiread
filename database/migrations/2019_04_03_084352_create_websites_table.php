<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url')->unique();
            $table->string('host')->comment('主机地址');
            $table->string('scheme')->comment('协议');
            $table->string('name');
            $table->string('logo')->default('');
            $table->string('description', 1024)->comment('简介');
            $table->integer('followers')->default(0)->comment('订阅量');
            $table->integer('last_update_time')->default(0)->index()->comment('最后更新时间');
            $table->tinyInteger('status')->default(0)->comment('状态(0正常 1删除)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('websites');
    }
}
