<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * The database connection to use for the migration.
	 */
	protected $connection = 'guest_sqlite';

	public function up(): void
	{
		if (!Schema::connection('guest_sqlite')->hasColumn('guest_tasks', 'project_id')) {
			Schema::connection('guest_sqlite')->table('guest_tasks', function (Blueprint $table) {
				$table->unsignedBigInteger('project_id')->nullable()->after('guest_id');
				$table->foreign('project_id')->references('id')->on('guest_projects')->onDelete('set null');
			});
		}
	}

	public function down(): void
	{
		Schema::connection('guest_sqlite')->table('guest_tasks', function (Blueprint $table) {
			$table->dropForeign(['project_id']);
			$table->dropColumn('project_id');
		});
	}
};
