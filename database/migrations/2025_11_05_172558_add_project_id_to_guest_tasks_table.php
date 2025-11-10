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
		
		if (!Schema::connection($connection)->hasColumn('guest_tasks', 'project_id')) {
			Schema::connection($connection)->table('guest_tasks', function (Blueprint $table) {
				$table->unsignedBigInteger('project_id')->nullable()->after('guest_id');
				$table->foreign('project_id')->references('id')->on('guest_projects')->onDelete('set null');
			});
		}
	}

	public function down(): void
	{
		$connection = $this->getGuestConnection();
		Schema::connection($connection)->table('guest_tasks', function (Blueprint $table) {
			$table->dropForeign(['project_id']);
			$table->dropColumn('project_id');
		});
	}
};
