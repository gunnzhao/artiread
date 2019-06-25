<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('website_id')->index();
            $table->string('link_md5')->unique();
            $table->string('link', 1024)->comment('原文链接');
            $table->string('title', 512);
            $table->integer('mark')->default(0)->comment('收藏量');
            $table->bigInteger('click')->default(0)->comment('点击量');
            $table->string('description', 1024)->comment('简介');
            $table->longText('content')->comment('内容');
            $table->timestamp('publish_time')->nullable()->comment('最后更新时间');
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
        Schema::dropIfExists('articles');
    }
}
