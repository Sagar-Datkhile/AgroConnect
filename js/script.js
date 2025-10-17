// AgroConnect - Main JavaScript File

// Check session status
async function checkSession() {
    try {
        const response = await fetch('php/check_session.php');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Session check error:', error);
        return { logged_in: false };
    }
}

// Check admin session
async function checkAdminSession() {
    try {
        const response = await fetch('php/check_admin_session.php');
        const data = await response.json();
        
        if (!data.logged_in) {
            window.location.href = 'admin_login.html';
        }
        
        return data;
    } catch (error) {
        console.error('Admin session check error:', error);
        window.location.href = 'admin_login.html';
    }
}

// Protect farmer dashboard pages
async function protectFarmerPage() {
    const session = await checkSession();
    
    if (!session.logged_in) {
        window.location.href = 'farmer_login.html';
        return false;
    }
    
    return session;
}

// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal when clicking outside
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR'
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Confirm dialog
function confirm(message) {
    return window.confirm(message);
}

// Show loading state
function showLoading(element) {
    if (element) {
        element.innerHTML = '<div class="loading">Loading...</div>';
    }
}

// Hide loading state
function hideLoading(element) {
    const loading = element?.querySelector('.loading');
    if (loading) {
        loading.remove();
    }
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Search crops
async function searchCrops(filters) {
    try {
        const params = new URLSearchParams();
        
        if (filters.crop_name) params.append('crop_name', filters.crop_name);
        if (filters.region) params.append('region', filters.region);
        if (filters.min_area) params.append('min_area', filters.min_area);
        
        const response = await fetch(`php/search_crops.php?${params.toString()}`);
        const data = await response.json();
        
        return data;
    } catch (error) {
        console.error('Search error:', error);
        return { success: false, crops: [] };
    }
}

// Display search results
function displaySearchResults(crops, containerId) {
    const container = document.getElementById(containerId);
    
    if (!container) return;
    
    if (crops.length === 0) {
        container.innerHTML = '<div class="text-center"><p>No crops found matching your criteria.</p></div>';
        return;
    }
    
    container.innerHTML = crops.map(crop => `
        <div class="crop-card">
            <h4>${crop.crop_name}</h4>
            <div class="info-row">
                <strong>Farmer:</strong>
                <span>${crop.farmer_name}</span>
            </div>
            <div class="info-row">
                <strong>Region:</strong>
                <span>${crop.region}</span>
            </div>
            <div class="info-row">
                <strong>Soil Type:</strong>
                <span>${crop.soil_type}</span>
            </div>
            <div class="info-row">
                <strong>Farm Area:</strong>
                <span>${crop.area} acres</span>
            </div>
            <div class="info-row">
                <strong>Investment:</strong>
                <span>${formatCurrency(crop.investment)}</span>
            </div>
            <div class="info-row">
                <strong>Turnover:</strong>
                <span>${formatCurrency(crop.turnover)}</span>
            </div>
            ${crop.description ? `<p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">${crop.description}</p>` : ''}
        </div>
    `).join('');
}

// Fetch farmer's crops
async function fetchFarmerCrops() {
    try {
        const response = await fetch('php/fetch_crops.php');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Fetch crops error:', error);
        return { success: false, crops: [] };
    }
}

// Delete crop
async function deleteCrop(cropId) {
    if (!window.confirm('Are you sure you want to delete this crop?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('crop_id', cropId);
        
        const response = await fetch('php/delete_crop.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert(data.message, 'success');
            // Reload crops
            if (typeof loadMyCrops === 'function') {
                loadMyCrops();
            }
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        showAlert('An error occurred while deleting the crop.', 'error');
        console.error('Delete error:', error);
    }
}

// Set active navigation link
function setActiveNav() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-links a, .sidebar a');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage) {
            link.classList.add('active');
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    setActiveNav();
});

// Logout function
function logout() {
    if (window.confirm('Are you sure you want to logout?')) {
        window.location.href = 'php/logout.php';
    }
}

