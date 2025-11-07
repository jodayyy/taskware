<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	/**
	 * Resolve the database connection name for this migration.
	 */
	protected function connectionName(): string
	{
		return $this->connection ?? config('database.default');
	}

	/**
	 * Resolve the table name for the current connection.
	 */
	protected function tableName(string $connection): string
	{
		return in_array($connection, ['guest_sqlite', 'guest_pgsql'], true) ? 'guest_tasks' : 'tasks';
	}

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		$connectionName = $this->connectionName();
		$connection = DB::connection($connectionName);
		$schema = Schema::connection($connectionName);
		$tableName = $this->tableName($connectionName);
		
		if (!$schema->hasTable($tableName)) {
			return;
		}
		
		// Update existing values to new naming convention
		$connection->table($tableName)->where('priority', 'medium')->update(['priority' => 'normal']);
		$connection->table($tableName)->where('priority', 'high')->update(['priority' => 'urgent']);

		$driver = $connection->getDriverName();
		$grammar = $connection->getQueryGrammar();
		$wrappedTable = $grammar->wrapTable($tableName);

		if ($driver === 'pgsql') {
			$constraintName = $tableName.'_priority_check';
			$connection->statement("ALTER TABLE {$wrappedTable} DROP CONSTRAINT IF EXISTS {$constraintName}");
			$connection->statement("ALTER TABLE {$wrappedTable} ALTER COLUMN priority TYPE TEXT");
			$connection->statement("ALTER TABLE {$wrappedTable} ALTER COLUMN priority SET DEFAULT 'normal'");
			$connection->statement("ALTER TABLE {$wrappedTable} ADD CONSTRAINT {$constraintName} CHECK (priority IN ('low','normal','urgent'))");
		} elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
			$connection->statement("ALTER TABLE {$wrappedTable} MODIFY COLUMN priority ENUM('low','normal','urgent') NOT NULL DEFAULT 'normal'");
		} else {
			try {
				$schema->table($tableName, function (Blueprint $table) {
					$table->string('priority')->default('normal')->change();
				});
			} catch (\Throwable $throwable) {
				// SQLite cannot easily modify column defaults; ignore.
			}
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		$connectionName = $this->connectionName();
		$connection = DB::connection($connectionName);
		$schema = Schema::connection($connectionName);
		$tableName = $this->tableName($connectionName);

		if (!$schema->hasTable($tableName)) {
			return;
		}

		// Revert data changes
		$connection->table($tableName)->where('priority', 'normal')->update(['priority' => 'medium']);
		$connection->table($tableName)->where('priority', 'urgent')->update(['priority' => 'high']);

		$driver = $connection->getDriverName();
		$grammar = $connection->getQueryGrammar();
		$wrappedTable = $grammar->wrapTable($tableName);

		if ($driver === 'pgsql') {
			$constraintName = $tableName.'_priority_check';
			$connection->statement("ALTER TABLE {$wrappedTable} DROP CONSTRAINT IF EXISTS {$constraintName}");
			$connection->statement("ALTER TABLE {$wrappedTable} ALTER COLUMN priority TYPE TEXT");
			$connection->statement("ALTER TABLE {$wrappedTable} ALTER COLUMN priority SET DEFAULT 'medium'");
			$connection->statement("ALTER TABLE {$wrappedTable} ADD CONSTRAINT {$constraintName} CHECK (priority IN ('low','medium','high'))");
		} elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
			$connection->statement("ALTER TABLE {$wrappedTable} MODIFY COLUMN priority ENUM('low','medium','high') NOT NULL DEFAULT 'medium'");
		} else {
			try {
				$schema->table($tableName, function (Blueprint $table) {
					$table->string('priority')->default('medium')->change();
				});
			} catch (\Throwable $throwable) {
				// Ignore for SQLite
			}
		}
	}
};