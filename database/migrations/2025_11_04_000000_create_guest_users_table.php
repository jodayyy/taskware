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
		if (!Schema::connection('guest_sqlite')->hasTable('guest_users')) {
			Schema::connection('guest_sqlite')->create('guest_users', function (Blueprint $table) {
				$table->id();
				$table->string('guest_id')->unique();
				$table->string('username')->default('Guest');
				$table->timestamps();
			});
		}
	}

	public function down(): void
	{
		Schema::connection('guest_sqlite')->dropIfExists('guest_users');
	}
};


