<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyNoteNullableInCorrectionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('correction_requests', function (Blueprint $table) {
            $table->string('note')->nullable()->change(); // noteカラムをNULL許容に変更
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('correction_requests', function (Blueprint $table) {
            $table->string('note')->nullable(false)->change(); // rollback時にNULLを不可に戻す
        });
    }
}
