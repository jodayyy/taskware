<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOldGuestData extends Command
{
	protected $signature = 'guest:cleanup {--days=30 : Delete data older than N days}';

	protected $description = 'Delete old guest users and their tasks from guest_sqlite';

	public function handle(): int
	{
		$days = (int) $this->option('days');
		$threshold = now()->subDays($days);
		$connection = env('GUEST_DB_CONNECTION', 'guest_sqlite');

		// Find old guest_ids
		$oldGuestIds = DB::connection($connection)
			->table('guest_users')
			->where('updated_at', '<', $threshold)
			->pluck('guest_id')
			->all();

		if (!empty($oldGuestIds)) {
			// Delete guest projects first (foreign key constraints)
			DB::connection($connection)
				->table('guest_projects')
				->whereIn('guest_id', $oldGuestIds)
				->delete();

			// Delete guest tasks
			DB::connection($connection)
				->table('guest_tasks')
				->whereIn('guest_id', $oldGuestIds)
				->delete();

			// Finally delete guest users
			DB::connection($connection)
				->table('guest_users')
				->whereIn('guest_id', $oldGuestIds)
				->delete();
		}

		$this->info('Guest cleanup complete. Deleted users: ' . count($oldGuestIds));
		return Command::SUCCESS;
	}
}


