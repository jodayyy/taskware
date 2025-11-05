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
		if (!Schema::connection('guest_sqlite')->hasTable('guest_projects')) {
			Schema::connection('guest_sqlite')->create('guest_projects', function (Blueprint $table) {
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
		Schema::connection('guest_sqlite')->dropIfExists('guest_projects');
	}
};
