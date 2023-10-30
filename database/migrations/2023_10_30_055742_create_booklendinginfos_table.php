<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booklendinginfos', function (Blueprint $table) {
            $table->increments("ID");
            $table->integer('BookID');
            $table->integer("LendingUserID");
            $table->date("LendingDate");
            $table->date("ReturnExpectedDate");
            $table->date("ReturnDate")->nullable()->default(null);
            $table->string("Note")->nullable()->default(null);
            $table->integer("CreateUserID");
            $table->dateTime('created_at');
            $table->integer('UpdateUserID')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);
            $table->integer('LockVer')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booklendinginfos');
    }
};
