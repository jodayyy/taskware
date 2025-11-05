<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreGuestTaskRequest extends FormRequest
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
	}

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		$guestId = session('guest_id');
		
		return [
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'deadline' => 'required|date',
			'priority' => 'required|in:low,normal,urgent',
			'notes' => 'nullable|string',
			'project_id' => [
				'nullable',
				function ($attribute, $value, $fail) use ($guestId) {
					if ($value !== null && $value !== '') {
						$projectId = is_numeric($value) ? (int) $value : $value;
						$exists = DB::connection('guest_sqlite')
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

