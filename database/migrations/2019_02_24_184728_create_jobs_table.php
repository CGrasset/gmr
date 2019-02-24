<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('submitter_id')->nullable(false);
            $table->unsignedInteger('processor_id')->nullable();
            $table->integer('processing_time')->default(0)->nullable(false);
            $table->enum('status', ['available', 'processing', 'processed', 'failed'])->nullable(false);
            $table->timestamps();

            if (Schema::hasColumn('users', 'id'))
            {
                $table->foreign('submitter_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('jobs');
        Schema::enableForeignKeyConstraints();
    }
}
