@props([
	'name' => 'date',
	'id' => null,
	'value' => null,
	'required' => false,
	'placeholder' => 'dd/mm/yyyy',
	'label' => null,
	'error' => null
])

@php
	$componentId = $id ?? $name;
	$pickerId = $componentId . '_picker';
@endphp

<div class="date-picker-container" data-date-picker="{{ $componentId }}">
	@if($label)
	<label for="{{ $componentId }}" class="block text-sm font-medium text-primary mb-2">
		{{ $label }} @if($required)<span class="text-red-500">*</span>@endif
	</label>
	@endif
	
	<div class="relative">
		<input 
			type="text" 
			id="{{ $componentId }}" 
			name="{{ $name }}" 
			@if($required) required @endif
			readonly
			placeholder="{{ $placeholder }}"
			value="{{ $value }}"
			{{ $attributes->merge(['class' => 'w-full border-2 border-primary px-3 py-2 pr-10 bg-primary text-primary focus:outline-none focus:border-gray-500 cursor-pointer']) }}
		>
		<button 
			type="button" 
			class="absolute right-3 top-1/2 transform -translate-y-1/2 text-primary hover:text-gray-600 transition-colors"
			onclick="DatePicker.toggle('{{ $componentId }}')"
		>
			<x-icons.calendar class="w-5 h-5" />
		</button>
	</div>
	
	@if($error)
	<div class="text-red-500 text-sm mt-1">{{ $error }}</div>
	@endif
	
	<div id="{{ $pickerId }}_error" class="text-red-500 text-sm mt-1 hidden"></div>
	
	<!-- Date Picker Popup -->
	<div id="{{ $pickerId }}" class="date-picker-popup fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-2">
		<div class="date-picker-content bg-primary border-2 border-primary w-full max-w-xs mx-auto shadow-xl">
			<!-- Header -->
			<div class="flex justify-between items-center p-3 border-b-2 border-primary">
				<h3 class="text-base font-bold text-primary">Select Date</h3>
				<button 
					type="button" 
					onclick="DatePicker.close('{{ $componentId }}')"
					class="text-primary hover:text-gray-600 transition-colors"
				>
					<x-icons.close class="w-5 h-5" />
				</button>
			</div>
			
			<!-- Calendar Container -->
			<div class="p-3">
				<!-- Calendar View -->
				<div id="{{ $pickerId }}_calendar" class="calendar-view">
					<!-- Month/Year Navigation -->
					<div class="flex justify-between items-center mb-3">
						<button 
							type="button" 
							onclick="DatePicker.changeMonth('{{ $componentId }}', -1)" 
							class="text-primary hover:bg-secondary hover:text-secondary p-1 rounded transition-colors"
							id="{{ $pickerId }}_prevBtn"
						>
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
							</svg>
						</button>
						<button 
							type="button" 
							onclick="DatePicker.showMonthYear('{{ $componentId }}')" 
							class="text-primary font-bold text-sm hover:bg-secondary hover:text-secondary px-2 py-1 rounded transition-colors" 
							id="{{ $pickerId }}_monthYear"
						></button>
						<button 
							type="button" 
							onclick="DatePicker.changeMonth('{{ $componentId }}', 1)" 
							class="text-primary hover:bg-secondary hover:text-secondary p-1 rounded transition-colors"
							id="{{ $pickerId }}_nextBtn"
						>
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
							</svg>
						</button>
					</div>
					
					<!-- Day Headers -->
					<div class="grid grid-cols-7 gap-1 mb-2">
						<div class="text-center text-xs font-bold text-primary p-1">Su</div>
						<div class="text-center text-xs font-bold text-primary p-1">Mo</div>
						<div class="text-center text-xs font-bold text-primary p-1">Tu</div>
						<div class="text-center text-xs font-bold text-primary p-1">We</div>
						<div class="text-center text-xs font-bold text-primary p-1">Th</div>
						<div class="text-center text-xs font-bold text-primary p-1">Fr</div>
						<div class="text-center text-xs font-bold text-primary p-1">Sa</div>
					</div>
					
					<!-- Calendar Days -->
					<div id="{{ $pickerId }}_days" class="grid grid-cols-7 gap-1 mb-3"></div>
				</div>
				
				<!-- Month Selection View -->
				<div id="{{ $pickerId }}_months" class="month-view hidden">
					<div class="flex justify-between items-center mb-3">
						<button 
							type="button" 
							onclick="DatePicker.changeYear('{{ $componentId }}', -1)" 
							class="text-primary hover:bg-secondary hover:text-secondary p-1 rounded transition-colors"
						>
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
							</svg>
						</button>
						<button 
							type="button" 
							onclick="DatePicker.showYears('{{ $componentId }}')" 
							class="text-primary font-bold text-sm hover:bg-secondary hover:text-secondary px-2 py-1 rounded transition-colors" 
							id="{{ $pickerId }}_year"
						></button>
						<button 
							type="button" 
							onclick="DatePicker.changeYear('{{ $componentId }}', 1)" 
							class="text-primary hover:bg-secondary hover:text-secondary p-1 rounded transition-colors"
						>
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
							</svg>
						</button>
					</div>
					<div id="{{ $pickerId }}_monthGrid" class="grid grid-cols-3 gap-1 mb-3"></div>
				</div>
				
				<!-- Year Selection View -->
				<div id="{{ $pickerId }}_years" class="year-view hidden">
					<div class="flex justify-between items-center mb-3">
						<button 
							type="button" 
							onclick="DatePicker.changeDecade('{{ $componentId }}', -1)" 
							class="text-primary hover:bg-secondary hover:text-secondary p-1 rounded transition-colors"
						>
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
							</svg>
						</button>
						<div class="text-primary font-bold text-sm" id="{{ $pickerId }}_decade"></div>
						<button 
							type="button" 
							onclick="DatePicker.changeDecade('{{ $componentId }}', 1)" 
							class="text-primary hover:bg-secondary hover:text-secondary p-1 rounded transition-colors"
						>
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
							</svg>
						</button>
					</div>
					<div id="{{ $pickerId }}_yearGrid" class="grid grid-cols-4 gap-1 mb-3"></div>
				</div>
				
				<!-- Action Buttons -->
				<div class="flex justify-between items-center pt-3 border-t-2 border-primary">
					<button 
						type="button" 
						onclick="DatePicker.selectToday('{{ $componentId }}')" 
						class="text-xs text-primary hover:bg-secondary hover:text-secondary px-3 py-1 border border-primary rounded transition-colors"
					>
						Today
					</button>
					<div class="space-x-1">
						<button 
							type="button" 
							onclick="DatePicker.clear('{{ $componentId }}')" 
							class="text-xs text-primary hover:bg-secondary hover:text-secondary px-3 py-1 border border-primary rounded transition-colors"
						>
							Clear
						</button>
						<button 
							type="button" 
							onclick="DatePicker.close('{{ $componentId }}')" 
							class="text-xs bg-secondary text-secondary hover:bg-primary hover:text-primary px-3 py-1 border border-primary rounded transition-colors"
						>
							Close
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>