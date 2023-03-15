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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone_number')->unique();
            $table->date('date_of_birth');
            $table->enum('enrollment_status', ['New', 'Old'])->default('New');
            $table->foreignId('class_level_id');
            $table->string('parent_first_name')->nullable();
            $table->string('parent_last_name')->nullable();
            $table->string('parent_phone_number_1')->nullable();
            $table->string('parent_phone_number_2')->nullable();
            $table->string('parent_home_address')->nullable();
            $table->string('parent_emergency_contact')->nullable();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
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
        Schema::dropIfExists('students');
    }
};
