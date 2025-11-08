function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

function validatePassword(password) {
    return password.length >= 6;
}

function validateNumber(value) {
    return !isNaN(value) && parseFloat(value) > 0;
}

function validateRequired(value) {
    return value.trim() !== '';
}

function showError(inputElement, message) {
    const formGroup = inputElement.parentElement;
    let errorElement = formGroup.querySelector('.form-error');
    
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'form-error';
        formGroup.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
    inputElement.style.borderColor = '#DC2626';
}

function clearError(inputElement) {
    const formGroup = inputElement.parentElement;
    const errorElement = formGroup.querySelector('.form-error');
    
    if (errorElement) {
        errorElement.remove();
    }
    
    inputElement.style.borderColor = '#E5E7EB';
}

function validateFarmerRegistration(formData) {
    const errors = [];
    
    if (!validateRequired(formData.name)) {
        errors.push({ field: 'name', message: 'Name is required' });
    }
    
    if (!validateEmail(formData.email)) {
        errors.push({ field: 'email', message: 'Invalid email address' });
    }
    
    if (!validatePassword(formData.password)) {
        errors.push({ field: 'password', message: 'Password must be at least 6 characters' });
    }
    
    if (!validateRequired(formData.region)) {
        errors.push({ field: 'region', message: 'Region is required' });
    }
    
    if (!validateRequired(formData.soil_type)) {
        errors.push({ field: 'soil_type', message: 'Soil type is required' });
    }
    
    if (!validateNumber(formData.area)) {
        errors.push({ field: 'area', message: 'Area must be a positive number' });
    }
    
    return errors;
}

function validateLogin(formData) {
    const errors = [];
    
    if (!validateEmail(formData.email)) {
        errors.push({ field: 'email', message: 'Invalid email address' });
    }
    
    if (!validateRequired(formData.password)) {
        errors.push({ field: 'password', message: 'Password is required' });
    }
    
    return errors;
}

function validateCrop(formData) {
    const errors = [];
    
    if (!validateRequired(formData.crop_name)) {
        errors.push({ field: 'crop_name', message: 'Crop name is required' });
    }
    
    if (!validateNumber(formData.investment)) {
        errors.push({ field: 'investment', message: 'Investment must be a positive number' });
    }
    
    if (!validateNumber(formData.turnover)) {
        errors.push({ field: 'turnover', message: 'Turnover must be a positive number' });
    }
    
    return errors;
}

function getToastContainer() {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    return container;
}

function showAlert(message, type = 'info') {
    const container = getToastContainer();
    
    const icons = {
        success: '✓',
        error: '✕',
        info: 'ℹ'
    };
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <span class="toast-icon">${icons[type] || icons.info}</span>
        <span class="toast-message">${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('removing');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 4000);
}

function removeAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => alert.remove());
}

async function handleFormSubmit(formElement, validationFunction, submitUrl) {
    formElement.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const inputs = formElement.querySelectorAll('input, select, textarea');
        inputs.forEach(input => clearError(input));
        removeAlerts();
        
        const formData = new FormData(formElement);
        const data = Object.fromEntries(formData.entries());
        
        const errors = validationFunction(data);
        
        if (errors.length > 0) {
            errors.forEach(error => {
                const input = formElement.querySelector(`[name="${error.field}"]`);
                if (input) {
                    showError(input, error.message);
                }
            });
            return;
        }
        
        try {
            const response = await fetch(submitUrl, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert(result.message, 'success');
                formElement.reset();
                
                if (formElement.dataset.redirectUrl) {
                    setTimeout(() => {
                        window.location.href = formElement.dataset.redirectUrl;
                    }, 1500);
                }
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('An error occurred. Please try again.', 'error');
            console.error('Form submission error:', error);
        }
    });
});

function addRealTimeValidation(formElement) {
    const inputs = formElement.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', () => {
            const value = input.value;
            const name = input.name;
            
            clearError(input);
            
            if (name === 'email' && value && !validateEmail(value)) {
                showError(input, 'Invalid email address');
            } else if (name === 'password' && value && !validatePassword(value)) {
                showError(input, 'Password must be at least 6 characters');
            } else if ((name === 'investment' || name === 'turnover' || name === 'area') && value && !validateNumber(value)) {
                showError(input, 'Must be a positive number');
            } else if (input.required && !validateRequired(value)) {
                showError(input, 'This field is required');
            }
        });
    });
}

