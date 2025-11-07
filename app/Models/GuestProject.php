<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestProject extends Model
{
	/**
	 * Get the connection name for the model.
	 *
	 * @return string
	 */
	public function getConnectionName()
	{
		return env('GUEST_DB_CONNECTION', 'guest_sqlite');
	}

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'guest_projects';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'guest_id',
		'title',
		'description',
	];

	/**
	 * Get the guest user that owns the project.
	 */
	public function guestUser(): BelongsTo
	{
		return $this->belongsTo(GuestUser::class, 'guest_id', 'guest_id');
	}

	/**
	 * Get the tasks for the guest project.
	 */
	public function tasks(): HasMany
	{
		return $this->hasMany(GuestTask::class, 'project_id');
	}
}

