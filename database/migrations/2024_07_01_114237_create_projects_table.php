<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('client_name');
            $table->string('client_email');
            $table->foreignId('project_manager_id')->constrained('employees');
            $table->foreignId('project_lead_id')->constrained('employees');
            $table->json('technologies');
            $table->timestamps();
        });

        Schema::create('project_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('project_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->text('status');
            $table->timestamps();
        });

        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->decimal('price', 10, 2);
            $table->string('alternative')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_expenses');
        Schema::dropIfExists('project_statuses');
        Schema::dropIfExists('project_employee');
        Schema::dropIfExists('projects');
    }
};