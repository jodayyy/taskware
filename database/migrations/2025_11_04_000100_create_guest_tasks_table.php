<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Get the database connection to use for the migration.
	 */
	private function getGuestConnection(): string
	{
		return config('database.guest_connection', 'guest_sqlite');
	}

	public function up(): void
	{
		$connection = $this->getGuestConnection();
		
		if (!Schema::connection($connection)->hasTable('guest_tasks')) {
			Schema::connection($connection)->create('guest_tasks', function (Blueprint $table) {
				$table->id();
				$table->string('guest_id');
				$table->string('title');
				$table->text('description');
				$table->date('deadline');
				$table->enum('priority', ['low', 'normal', 'urgent'])->default('normal');
				$table->enum('status', ['to_do', 'in_progress', 'done'])->default('to_do');
				$table->text('notes')->nullable();
				$table->timestamps();

				$table->index('guest_id');
				$table->index('status');
				$table->index('deadline');
				$table->index('priority');
				$table->index(['guest_id', 'status']); // Composite index for common filter combo
			});
		}
	}

	public function down(): void
	{
		$connection = $this->getGuestConnection();
		Schema::connection($connection)->dropIfExists('guest_tasks');
	}
};


