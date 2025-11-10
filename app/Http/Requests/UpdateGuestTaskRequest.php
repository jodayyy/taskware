<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateGuestTaskRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return session('is_guest') === true;
	}

	/**
	 * Prepare the data for validation.
	 */
	protected function prepareForValidation(): void
	{
		if ($this->has('project_id')) {
			if ($this->project_id === '' || $this->project_id === null) {
				$this->merge(['project_id' => null]);
			} else {
				// Convert string to integer for validation
				$this->merge(['project_id' => (int) $this->project_id]);
			}
		}
		
		// Convert deadline from dd/mm/yyyy to Y-m-d format for Laravel validation
		if ($this->has('deadline') && $this->deadline) {
			$deadline = $this->input('deadline');
			// Check if it's in dd/mm/yyyy format
			if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $deadline, $matches)) {
				$day = $matches[1];
				$month = $matches[2];
				$year = $matches[3];
				// Convert to Y-m-d format
				$this->merge(['deadline' => "{$year}-{$month}-{$day}"]);
			}
		}
	}

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		$guestId = session('guest_id');
		
		$guestConnection = config('database.guest_connection', 'guest_sqlite');

		return [
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'deadline' => 'required|date',
			'priority' => 'required|in:low,normal,urgent',
			'status' => 'required|in:to_do,in_progress,done',
			'notes' => 'nullable|string',
			'project_id' => [
				'nullable',
				function ($attribute, $value, $fail) use ($guestId, $guestConnection) {
					if ($value !== null && $value !== '') {
						$projectId = is_numeric($value) ? (int) $value : $value;
						$exists = DB::connection($guestConnection)
							->table('guest_projects')
							->where('id', $projectId)
							->where('guest_id', $guestId)
							->exists();
						
						if (!$exists) {
							$fail('The selected project is invalid.');
						}
					}
				},
			],
		];
	}
}

