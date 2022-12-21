<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abbreviation_legends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialization_id')->constrained()->cascadeOnDelete();
            $table->string('abbreviation', 50);
            $table->string('fullname', 255);
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
        Schema::dropIfExists('abbreviation_legends');
    }
};
