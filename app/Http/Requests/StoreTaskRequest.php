<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	/**
	 * Prepare the data for validation.
	 */
	protected function prepareForValidation(): void
	{
		// Normalize project_id: empty string, null, or '0' becomes null
		$projectId = $this->input('project_id');
		
		if (empty($projectId) || $projectId === '0' || $projectId === '') {
			$this->merge(['project_id' => null]);
		} else {
			// Ensure it's a valid integer
			$projectId = filter_var($projectId, FILTER_VALIDATE_INT);
			if ($projectId !== false && $projectId > 0) {
				$this->merge(['project_id' => $projectId]);
			} else {
				$this->merge(['project_id' => null]);
			}
		}
	}

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		$userId = $this->user()->id;
		
		return [
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'deadline' => 'required|date',
			'priority' => 'required|in:low,normal,urgent',
			'notes' => 'nullable|string',
			'project_id' => [
				'nullable',
				'integer',
				'min:1',
				Rule::exists('projects', 'id')->where('user_id', $userId),
			],
		];
	}
}

