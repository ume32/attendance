<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyNoteNullableInCorrectionRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('correction_requests', function (Blueprint $table) {
            $table->string('note')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('correction_requests', function (Blueprint $table) {
            $table->string('note')->nullable(false)->change();
        });
    }
}
