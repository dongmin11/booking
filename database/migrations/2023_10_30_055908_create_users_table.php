<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments("ID");
            $table->string("name",10);
            $table->string("email",30)->unique();
            $table->date("Birth");
            $table->string("Note")->nullable()->default(null);
            $table->string("password")->nullable()->default(null);
            $table->integer("DeleteFlg")->default(0);
            $table->integer("login_time")->nullable()->default(null);
            $table->integer("login_try")->nullable()->default(null);
            $table->integer("CreateUserID")->nullable()->default(null);
            $table->date("created_at")->nullable()->default(null);
            $table->timestamp("UpdateUserID")->nullable()->default(null);
            $table->date("updated_at")->nullable()->default(null);
            $table->integer("LockVer")->default(0);
            $table->integer("passLock")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
