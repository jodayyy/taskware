<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'user_id',
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
	 * Get the user that owns the task.
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
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