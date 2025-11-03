<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		// Add a temporary column
		Schema::table('tasks', function (Blueprint $table) {
			$table->string('priority_temp')->nullable();
		});
		
		// Update existing priority values to new mapping
		DB::table('tasks')->where('priority', 'medium')->update(['priority_temp' => 'normal']);
		DB::table('tasks')->where('priority', 'high')->update(['priority_temp' => 'urgent']);
		DB::table('tasks')->where('priority', 'low')->update(['priority_temp' => 'low']);
		
		// For guest tasks (if they exist)
		if (Schema::hasTable('guest_tasks')) {
			Schema::table('guest_tasks', function (Blueprint $table) {
				$table->string('priority_temp')->nullable();
			});
			
			DB::table('guest_tasks')->where('priority', 'medium')->update(['priority_temp' => 'normal']);
			DB::table('guest_tasks')->where('priority', 'high')->update(['priority_temp' => 'urgent']);
			DB::table('guest_tasks')->where('priority', 'low')->update(['priority_temp' => 'low']);
		}
		
		// Drop the old priority column and recreate with new enum
		Schema::table('tasks', function (Blueprint $table) {
			$table->dropColumn('priority');
		});
		
		Schema::table('tasks', function (Blueprint $table) {
			$table->enum('priority', ['low', 'normal', 'urgent'])->default('normal')->after('deadline');
		});
		
		// Copy data back and drop temp column
		DB::statement('UPDATE tasks SET priority = priority_temp WHERE priority_temp IS NOT NULL');
		
		Schema::table('tasks', function (Blueprint $table) {
			$table->dropColumn('priority_temp');
		});
		
		// Handle guest_tasks if it exists
		if (Schema::hasTable('guest_tasks')) {
			Schema::table('guest_tasks', function (Blueprint $table) {
				$table->dropColumn('priority');
			});
			
			Schema::table('guest_tasks', function (Blueprint $table) {
				$table->enum('priority', ['low', 'normal', 'urgent'])->default('normal');
			});
			
			DB::statement('UPDATE guest_tasks SET priority = priority_temp WHERE priority_temp IS NOT NULL');
			
			Schema::table('guest_tasks', function (Blueprint $table) {
				$table->dropColumn('priority_temp');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		// Reverse the changes: normal -> medium, urgent -> high
		DB::table('tasks')->where('priority', 'normal')->update(['priority' => 'medium']);
		DB::table('tasks')->where('priority', 'urgent')->update(['priority' => 'high']);
		
		if (Schema::hasTable('guest_tasks')) {
			DB::table('guest_tasks')->where('priority', 'normal')->update(['priority' => 'medium']);
			DB::table('guest_tasks')->where('priority', 'urgent')->update(['priority' => 'high']);
		}
		
		Schema::table('tasks', function (Blueprint $table) {
			$table->dropColumn('priority');
		});
		
		Schema::table('tasks', function (Blueprint $table) {
			$table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('deadline');
		});
	}
};