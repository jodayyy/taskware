<?php

namespace App\Models;

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
		return match($this->status) {
			'to_do' => 'To Do',
			'in_progress' => 'In Progress',
			'done' => 'Done',
			default => ucfirst($this->status)
		};
	}

	/**
	 * Get the priority display label
	 */
	public function getPriorityLabelAttribute(): string
	{
		return ucfirst($this->priority);
	}
}