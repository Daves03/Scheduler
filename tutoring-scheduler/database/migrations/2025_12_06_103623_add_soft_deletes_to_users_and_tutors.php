<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->softDeletes(); // Adds 'deleted_at' column
    });
    Schema::table('tutors', function (Blueprint $table) {
        $table->softDeletes();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });
    Schema::table('tutors', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });
}
};
