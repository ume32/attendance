<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrectionRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('correction_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            $table->time('new_start_time')->nullable();
            $table->time('new_end_time')->nullable();
            $table->json('new_breaks')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['承認待ち', '承認済み'])->default('承認待ち');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('correction_requests');
    }
}
