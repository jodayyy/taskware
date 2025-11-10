<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestUser extends Model
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
	protected $table = 'guest_users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'guest_id',
		'username',
	];

	/**
	 * Get the tasks for the guest user.
	 */
	public function tasks(): HasMany
	{
		return $this->hasMany(GuestTask::class, 'guest_id', 'guest_id');
	}

	/**
	 * Get the projects for the guest user.
	 */
	public function projects(): HasMany
	{
		return $this->hasMany(GuestProject::class, 'guest_id', 'guest_id');
	}
}

