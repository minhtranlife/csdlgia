<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagdvvtkhacTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagdvvtkhac', function (Blueprint $table) {
            $table->increments('id');
            $table->string('maxa')->nullable();
            $table->string('mahuyen')->nullable();
            $table->string('tendn')->nullable();
            $table->string('masokk')->nullable();
            $table->string('madichvu')->nullable();
            $table->double('sanluong')->default(0);
            $table->double('cpnguyenlieutt')->default(0);
            $table->double('cpcongnhantt')->default(0);
            $table->double('cpkhauhaott')->default(0);
            $table->double('cpsanxuatdt')->default(0);
            $table->double('cpsanxuatc')->default(0);
            $table->double('cptaichinh')->default(0);
            $table->double('cpbanhang')->default(0);
            $table->double('cpquanly')->default(0);
            $table->text('giaitrinh')->nullable();
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
        Schema::drop('pagdvvtkhac');
    }
}
