// Date Picker Component JavaScript
window.DatePicker = {
	instances: {},
	
	init(componentId) {
		if (!this.instances[componentId]) {
			this.instances[componentId] = {
				currentDate: new Date(),
				selectedDate: null,
				isOpen: false,
				view: 'calendar' // calendar, months, years
			};
		}
		return this.instances[componentId];
	},
	
	toggle(componentId) {
		const instance = this.init(componentId);
		
		if (instance.isOpen) {
			this.close(componentId);
		} else {
			this.open(componentId);
		}
	},
	
	open(componentId) {
		// Close any other open date pickers
		Object.keys(this.instances).forEach(id => {
			if (id !== componentId && this.instances[id].isOpen) {
				this.close(id);
			}
		});
		
		const instance = this.init(componentId);
		const popup = document.getElementById(componentId + '_picker');
		const input = document.getElementById(componentId);
		
		if (popup && input) {
			// Parse existing value if any
			const currentValue = input.value;
			if (currentValue) {
				const parsedDate = this.parseDate(currentValue);
				if (parsedDate) {
					instance.selectedDate = parsedDate;
					instance.currentDate = new Date(parsedDate);
				} else {
					instance.currentDate = new Date();
				}
			} else {
				instance.currentDate = new Date();
			}
			
			instance.view = 'calendar';
			this.renderCalendar(componentId);
			popup.classList.remove('hidden');
			instance.isOpen = true;
			document.body.style.overflow = 'hidden';
		}
	},
	
	close(componentId) {
		const instance = this.instances[componentId];
		const popup = document.getElementById(componentId + '_picker');
		
		if (popup && instance) {
			popup.classList.add('hidden');
			instance.isOpen = false;
			document.body.style.overflow = 'auto';
		}
	},
	
	selectDate(componentId, date) {
		const instance = this.instances[componentId];
		const input = document.getElementById(componentId);
		
		if (instance && input) {
			instance.selectedDate = new Date(date);
			input.value = this.formatDate(date);
			
			// Trigger change event
			const event = new Event('change', { bubbles: true });
			input.dispatchEvent(event);
			
			this.close(componentId);
		}
	},
	
	selectToday(componentId) {
		this.selectDate(componentId, new Date());
	},
	
	clear(componentId) {
		const instance = this.instances[componentId];
		const input = document.getElementById(componentId);
		
		if (instance && input) {
			instance.selectedDate = null;
			input.value = '';
			
			// Trigger change event
			const event = new Event('change', { bubbles: true });
			input.dispatchEvent(event);
			
			this.close(componentId);
		}
	},
	
	changeMonth(componentId, direction) {
		const instance = this.instances[componentId];
		if (instance) {
			instance.currentDate.setMonth(instance.currentDate.getMonth() + direction);
			this.renderCalendar(componentId);
		}
	},
	
	changeYear(componentId, direction) {
		const instance = this.instances[componentId];
		if (instance) {
			instance.currentDate.setFullYear(instance.currentDate.getFullYear() + direction);
			this.renderMonths(componentId);
		}
	},
	
	changeDecade(componentId, direction) {
		const instance = this.instances[componentId];
		if (instance) {
			instance.currentDate.setFullYear(instance.currentDate.getFullYear() + (direction * 10));
			this.renderYears(componentId);
		}
	},
	
	showMonthYear(componentId) {
		const instance = this.instances[componentId];
		if (instance) {
			instance.view = 'months';
			this.showView(componentId, 'months');
			this.renderMonths(componentId);
		}
	},
	
	showYears(componentId) {
		const instance = this.instances[componentId];
		if (instance) {
			instance.view = 'years';
			this.showView(componentId, 'years');
			this.renderYears(componentId);
		}
	},
	
	selectMonth(componentId, month) {
		const instance = this.instances[componentId];
		if (instance) {
			instance.currentDate.setMonth(month);
			instance.view = 'calendar';
			this.showView(componentId, 'calendar');
			this.renderCalendar(componentId);
		}
	},
	
	selectYear(componentId, year) {
		const instance = this.instances[componentId];
		if (instance) {
			instance.currentDate.setFullYear(year);
			instance.view = 'months';
			this.showView(componentId, 'months');
			this.renderMonths(componentId);
		}
	},
	
	showView(componentId, view) {
		const pickerId = componentId + '_picker';
		const calendarView = document.getElementById(pickerId + '_calendar');
		const monthsView = document.getElementById(pickerId + '_months');
		const yearsView = document.getElementById(pickerId + '_years');
		
		// Hide all views
		calendarView.classList.add('hidden');
		monthsView.classList.add('hidden');
		yearsView.classList.add('hidden');
		
		// Show selected view
		if (view === 'calendar') {
			calendarView.classList.remove('hidden');
		} else if (view === 'months') {
			monthsView.classList.remove('hidden');
		} else if (view === 'years') {
			yearsView.classList.remove('hidden');
		}
	},
	
	renderCalendar(componentId) {
		const instance = this.instances[componentId];
		if (!instance) return;
		
		const monthNames = [
			'January', 'February', 'March', 'April', 'May', 'June',
			'July', 'August', 'September', 'October', 'November', 'December'
		];
		
		const monthYearElement = document.getElementById(componentId + '_picker_monthYear');
		const daysElement = document.getElementById(componentId + '_picker_days');
		
		if (!monthYearElement || !daysElement) return;
		
		// Update month/year display
		monthYearElement.textContent = `${monthNames[instance.currentDate.getMonth()]} ${instance.currentDate.getFullYear()}`;
		
		// Clear days
		daysElement.innerHTML = '';
		
		// Get calendar data
		const firstDay = new Date(instance.currentDate.getFullYear(), instance.currentDate.getMonth(), 1);
		const lastDay = new Date(instance.currentDate.getFullYear(), instance.currentDate.getMonth() + 1, 0);
		const daysInMonth = lastDay.getDate();
		const startingDayOfWeek = firstDay.getDay();
		
		// Add empty cells for days before month starts
		for (let i = 0; i < startingDayOfWeek; i++) {
			const emptyDay = document.createElement('div');
			emptyDay.className = 'p-1';
			daysElement.appendChild(emptyDay);
		}
		
		// Add days of the month
		for (let day = 1; day <= daysInMonth; day++) {
			const dayElement = document.createElement('button');
			dayElement.type = 'button';
			dayElement.textContent = day;
			dayElement.className = 'p-1 text-sm text-primary hover:bg-secondary hover:text-secondary rounded transition-colors min-h-[28px] flex items-center justify-center';
			
			const dayDate = new Date(instance.currentDate.getFullYear(), instance.currentDate.getMonth(), day);
			
			// Highlight selected date
			if (instance.selectedDate && this.isSameDate(dayDate, instance.selectedDate)) {
				dayElement.className = 'p-1 text-sm bg-secondary text-secondary rounded min-h-[28px] flex items-center justify-center font-bold';
			}
			
			// Highlight today
			const today = new Date();
			if (this.isSameDate(dayDate, today) && (!instance.selectedDate || !this.isSameDate(dayDate, instance.selectedDate))) {
				dayElement.className += ' border-2 border-primary';
			}
			
			dayElement.onclick = () => this.selectDate(componentId, dayDate);
			daysElement.appendChild(dayElement);
		}
		
		this.showView(componentId, 'calendar');
	},
	
	renderMonths(componentId) {
		const instance = this.instances[componentId];
		if (!instance) return;
		
		const monthNames = [
			'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
			'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
		];
		
		const yearElement = document.getElementById(componentId + '_picker_year');
		const monthGrid = document.getElementById(componentId + '_picker_monthGrid');
		
		if (!yearElement || !monthGrid) return;
		
		// Update year display
		yearElement.textContent = instance.currentDate.getFullYear();
		
		// Clear months
		monthGrid.innerHTML = '';
		
		// Add month buttons
		monthNames.forEach((month, index) => {
			const monthElement = document.createElement('button');
			monthElement.type = 'button';
			monthElement.textContent = month;
			monthElement.className = 'p-2 text-sm text-primary hover:bg-secondary hover:text-secondary rounded transition-colors';
			
			// Highlight current month
			if (index === instance.currentDate.getMonth()) {
				monthElement.className += ' bg-secondary text-secondary font-bold';
			}
			
			monthElement.onclick = () => this.selectMonth(componentId, index);
			monthGrid.appendChild(monthElement);
		});
	},
	
	renderYears(componentId) {
		const instance = this.instances[componentId];
		if (!instance) return;
		
		const currentYear = instance.currentDate.getFullYear();
		const decade = Math.floor(currentYear / 10) * 10;
		
		const decadeElement = document.getElementById(componentId + '_picker_decade');
		const yearGrid = document.getElementById(componentId + '_picker_yearGrid');
		
		if (!decadeElement || !yearGrid) return;
		
		// Update decade display
		decadeElement.textContent = `${decade}-${decade + 9}`;
		
		// Clear years
		yearGrid.innerHTML = '';
		
		// Add year buttons
		for (let year = decade; year < decade + 12; year++) {
			const yearElement = document.createElement('button');
			yearElement.type = 'button';
			yearElement.textContent = year;
			yearElement.className = 'p-2 text-sm text-primary hover:bg-secondary hover:text-secondary rounded transition-colors';
			
			// Highlight current year
			if (year === currentYear) {
				yearElement.className += ' bg-secondary text-secondary font-bold';
			}
			
			// Gray out years outside current decade
			if (year < decade || year >= decade + 10) {
				yearElement.className += ' opacity-50';
			}
			
			yearElement.onclick = () => this.selectYear(componentId, year);
			yearGrid.appendChild(yearElement);
		}
	},
	
	formatDate(date) {
		const day = date.getDate().toString().padStart(2, '0');
		const month = (date.getMonth() + 1).toString().padStart(2, '0');
		const year = date.getFullYear();
		return `${day}/${month}/${year}`;
	},
	
	parseDate(dateString) {
		if (!dateString) return null;
		const parts = dateString.split('/');
		if (parts.length !== 3) return null;
		
		const day = parseInt(parts[0]);
		const month = parseInt(parts[1]) - 1; // Month is 0-indexed
		const year = parseInt(parts[2]);
		
		if (isNaN(day) || isNaN(month) || isNaN(year)) return null;
		
		return new Date(year, month, day);
	},
	
	formatDateForServer(date) {
		if (!date) return '';
		const year = date.getFullYear();
		const month = (date.getMonth() + 1).toString().padStart(2, '0');
		const day = date.getDate().toString().padStart(2, '0');
		return `${year}-${month}-${day}`;
	},
	
	isSameDate(date1, date2) {
		return date1.getDate() === date2.getDate() &&
			   date1.getMonth() === date2.getMonth() &&
			   date1.getFullYear() === date2.getFullYear();
	}
};

// Global event handlers
document.addEventListener('DOMContentLoaded', function() {
	// Close date picker when clicking outside
	document.addEventListener('click', function(event) {
		const datePicker = event.target.closest('.date-picker-popup');
		const datePickerTrigger = event.target.closest('.date-picker-container');
		
		if (!datePicker && !datePickerTrigger) {
			Object.keys(DatePicker.instances).forEach(componentId => {
				if (DatePicker.instances[componentId].isOpen) {
					DatePicker.close(componentId);
				}
			});
		}
	});
	
	// Close date picker on escape key
	document.addEventListener('keydown', function(event) {
		if (event.key === 'Escape') {
			Object.keys(DatePicker.instances).forEach(componentId => {
				if (DatePicker.instances[componentId].isOpen) {
					DatePicker.close(componentId);
				}
			});
		}
	});
});