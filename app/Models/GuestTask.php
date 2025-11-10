<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestTask extends Model
{
	/**
	 * Get the connection name for the model.
	 *
	 * @return string
	 */
    public function getConnectionName()
    {
        return config('database.guest_connection', 'guest_sqlite');
    }

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'guest_tasks';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'guest_id',
		'project_id',
		'title',
		'description',
		'deadline',
		'priority',
		'status',
		'notes',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'deadline' => 'date',
		'status' => TaskStatus::class,
		'priority' => TaskPriority::class,
	];

	/**
	 * Get the guest user that owns the task.
	 */
	public function guestUser(): BelongsTo
	{
		return $this->belongsTo(GuestUser::class, 'guest_id', 'guest_id');
	}

	/**
	 * Get the guest project that owns the task.
	 */
	public function guestProject(): BelongsTo
	{
		return $this->belongsTo(GuestProject::class, 'project_id');
	}

	/**
	 * Get the status display label
	 */
	public function getStatusLabelAttribute(): string
	{
		return $this->status instanceof TaskStatus
			? $this->status->label()
			: ucfirst((string) $this->status);
	}

	/**
	 * Get the priority display label
	 */
	public function getPriorityLabelAttribute(): string
	{
		return $this->priority instanceof TaskPriority
			? $this->priority->label()
			: ucfirst((string) $this->priority);
	}
}

