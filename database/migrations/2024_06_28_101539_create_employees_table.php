<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->foreignId('country_id')->constrained();
            $table->foreignId('state_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->text('address');
            $table->string('zip_code');
            $table->foreignId('department_id')->constrained();
            $table->foreignId('employment_type_id')->constrained();
            $table->decimal('current_package', 10, 2);
            $table->string('github')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('photo')->nullable();
            $table->string('cv');
            $table->json('skills');
            $table->date('date_of_birth');
            $table->date('date_of_hired');
            $table->boolean('is_terminated')->default(false);
            $table->date('date_of_termination')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('employer');
            $table->date('from');
            $table->date('to');
            $table->string('employer_email');
            $table->string('employee_id_at_employer');
            $table->string('job_role');
            $table->string('job_location');
            $table->timestamps();
        });

        Schema::create('employee_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('college');
            $table->string('course');
            $table->string('branch');
            $table->year('from_year');
            $table->year('passing_year');
            $table->decimal('percentage', 5, 2);
            $table->string('certificate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_qualifications');
        Schema::dropIfExists('employee_experiences');
        Schema::dropIfExists('employees');
    }
};
