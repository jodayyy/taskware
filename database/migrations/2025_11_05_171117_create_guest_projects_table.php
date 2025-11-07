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
		return env('GUEST_DB_CONNECTION', 'guest_sqlite');
	}

	public function up(): void
	{
		$connection = $this->getGuestConnection();
		
		if (!Schema::connection($connection)->hasTable('guest_projects')) {
			Schema::connection($connection)->create('guest_projects', function (Blueprint $table) {
				$table->id();
				$table->string('guest_id');
				$table->string('title');
				$table->text('description');
				$table->timestamps();

				$table->index('guest_id');
			});
		}
	}

	public function down(): void
	{
		$connection = $this->getGuestConnection();
		Schema::connection($connection)->dropIfExists('guest_projects');
	}
};
