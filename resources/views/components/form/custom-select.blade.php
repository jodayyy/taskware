@props([
	'name',
	'id' => $name,
	'value' => '',
	'options' => [],
	'placeholder' => 'Select an option',
	'required' => false,
])

<div class="relative" 
	x-data="{
		isOpen: false,
		selectedValue: '{{ $value }}',
		selectedText: '',
		options: @js($options),
		
		init() {
			if (this.selectedValue && this.options[this.selectedValue]) {
				this.selectedText = this.options[this.selectedValue];
			}
		},
		
		toggleDropdown() {
			this.isOpen = !this.isOpen;
		},
		
		closeDropdown() {
			this.isOpen = false;
		},
		
		selectOption(value, text) {
			this.selectedValue = value;
			this.selectedText = text;
			this.closeDropdown();
		}
	}">
	<!-- Hidden input for form submission -->
	<input 
		type="hidden" 
		id="{{ $id }}"
		name="{{ $name }}" 
		x-model="selectedValue"
		{{ $required ? 'required' : '' }}
	>
	
	<!-- Custom Select Button -->
	<button 
		type="button"
		@click="toggleDropdown()"
		class="w-full border-2 border-primary px-3 py-2 bg-primary text-primary focus:outline-none focus:border-gray-500 text-left flex justify-between items-center rounded-none"
		:class="{ 'text-gray-500': !selectedValue }"
	>
		<span x-text="selectedText || '{{ $placeholder }}'"></span>
		<svg 
			class="w-4 h-4 transition-transform duration-200" 
			:class="{ 'rotate-180': isOpen }"
			fill="none" 
			stroke="currentColor" 
			viewBox="0 0 24 24"
		>
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
		</svg>
	</button>
	
	<!-- Dropdown Options -->
	<div 
		x-show="isOpen" 
		x-transition:enter="transition ease-out duration-100"
		x-transition:enter-start="transform opacity-0 scale-95"
		x-transition:enter-end="transform opacity-100 scale-100"
		x-transition:leave="transition ease-in duration-75"
		x-transition:leave-start="transform opacity-100 scale-100"
		x-transition:leave-end="transform opacity-0 scale-95"
		@click.outside="closeDropdown()"
		class="absolute z-50 w-full mt-1 bg-primary border-2 border-primary shadow-lg max-h-60 overflow-auto"
		style="display: none;"
	>
		@foreach($options as $optionValue => $optionText)
			<div 
				@click="selectOption('{{ $optionValue }}', '{{ $optionText }}')"
				class="px-3 py-2 cursor-pointer transition-colors duration-150 custom-select-option"
				:class="{ 'selected': selectedValue === '{{ $optionValue }}' }"
			>
				{{ $optionText }}
			</div>
		@endforeach
	</div>
</div>