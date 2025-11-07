<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	/**
	 * Get the connection name for this migration.
	 */
	private function getConnection(): ?string
	{
		return $this->connection ?? null;
	}

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		$connection = $this->getConnection();
		$isGuestConnection = in_array($connection, ['guest_sqlite', 'guest_pgsql']);
		$tableName = $isGuestConnection ? 'guest_tasks' : 'tasks';
		
		// Skip if table doesn't exist
		if (!Schema::connection($connection)->hasTable($tableName)) {
			return;
		}
		
		// Check if migration already ran (column has new enum values)
		try {
			$columnInfo = DB::connection($connection)
				->select("SELECT column_type FROM information_schema.columns WHERE table_schema = 'public' AND table_name = ? AND column_name = 'priority'", [$tableName]);
			
			if (!empty($columnInfo) && str_contains($columnInfo[0]->column_type ?? '', 'urgent')) {
				return; // Already migrated
			}
		} catch (\Exception $e) {
			// If check fails, proceed with migration
		}
		
		// Add a temporary column if it doesn't exist
		if (!Schema::connection($connection)->hasColumn($tableName, 'priority_temp')) {
			Schema::connection($connection)->table($tableName, function (Blueprint $table) {
				$table->string('priority_temp')->nullable();
			});
		}
		
		// Update existing priority values to new mapping
		DB::connection($connection)->table($tableName)->where('priority', 'medium')->update(['priority_temp' => 'normal']);
		DB::connection($connection)->table($tableName)->where('priority', 'high')->update(['priority_temp' => 'urgent']);
		DB::connection($connection)->table($tableName)->where('priority', 'low')->update(['priority_temp' => 'low']);
		
		// Drop the old priority column and recreate with new enum
		Schema::connection($connection)->table($tableName, function (Blueprint $table) {
			$table->dropColumn('priority');
		});
		
		Schema::connection($connection)->table($tableName, function (Blueprint $table) {
			$table->enum('priority', ['low', 'normal', 'urgent'])->default('normal')->after('deadline');
		});
		
		// Copy data back using individual updates (works reliably with PostgreSQL enums)
		DB::connection($connection)->table($tableName)->whereNotNull('priority_temp')->chunkById(100, function ($rows) use ($connection, $tableName) {
			foreach ($rows as $row) {
				DB::connection($connection)->table($tableName)
					->where('id', $row->id)
					->update(['priority' => $row->priority_temp]);
			}
		});
		
		Schema::connection($connection)->table($tableName, function (Blueprint $table) {
			$table->dropColumn('priority_temp');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		$connection = $this->getConnection();
		$isGuestConnection = in_array($connection, ['guest_sqlite', 'guest_pgsql']);
		$tableName = $isGuestConnection ? 'guest_tasks' : 'tasks';
		
		if (!Schema::connection($connection)->hasTable($tableName)) {
			return;
		}
		
		// Reverse the changes: normal -> medium, urgent -> high
		DB::connection($connection)->table($tableName)->where('priority', 'normal')->update(['priority' => 'medium']);
		DB::connection($connection)->table($tableName)->where('priority', 'urgent')->update(['priority' => 'high']);
		
		Schema::connection($connection)->table($tableName, function (Blueprint $table) {
			$table->dropColumn('priority');
		});
		
		Schema::connection($connection)->table($tableName, function (Blueprint $table) {
			$table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('deadline');
		});
	}
};