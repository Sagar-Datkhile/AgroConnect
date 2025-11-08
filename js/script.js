// AgroConnect - Main JavaScript File

// Helper function to get base directory
function getBasePath() {
    const path = window.location.pathname;
    const directory = path.substring(0, path.lastIndexOf('/'));
    return directory || '';
}

// Helper function to get PHP endpoint URL
function getPhpUrl(endpoint) {
    const basePath = getBasePath();
    return `${basePath}/php/${endpoint}`;
}

// Check session status
async function checkSession() {
    try {
        const response = await fetch(getPhpUrl('check_session.php'));
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
        const response = await fetch(getPhpUrl('check_admin_session.php'));
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

// Format time ago (e.g., "2d ago", "3mo ago", "1y ago")
function timeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const seconds = Math.floor((now - date) / 1000);
    
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60
    };
    
    if (seconds < 60) {
        return 'just now';
    }
    
    if (seconds < intervals.hour) {
        const minutes = Math.floor(seconds / intervals.minute);
        return `${minutes}m ago`;
    }
    
    if (seconds < intervals.day) {
        const hours = Math.floor(seconds / intervals.hour);
        return `${hours}h ago`;
    }
    
    if (seconds < intervals.week) {
        const days = Math.floor(seconds / intervals.day);
        return `${days}d ago`;
    }
    
    if (seconds < intervals.month) {
        const weeks = Math.floor(seconds / intervals.week);
        return `${weeks}w ago`;
    }
    
    if (seconds < intervals.year) {
        const months = Math.floor(seconds / intervals.month);
        return `${months}mo ago`;
    }
    
    const years = Math.floor(seconds / intervals.year);
    return `${years}y ago`;
}

// Note: Using window.confirm() directly for dialogs

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
        
        const response = await fetch(`${getPhpUrl('search_crops.php')}?${params.toString()}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Search error:', error);
        return { success: false, crops: [], error: error.message };
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
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                <h4 style="margin: 0;">${crop.crop_name}</h4>
                <span class="time-badge">${timeAgo(crop.created_at)}</span>
            </div>
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
        const response = await fetch(getPhpUrl('fetch_crops.php'));
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Fetch crops error:', error);
        return { success: false, crops: [] };
    }
}

// Delete crop
async function deleteCrop(cropId) {
    console.log('Delete crop called with ID:', cropId);
    
    if (!confirm('Are you sure you want to delete this crop?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('crop_id', cropId);
        
        const response = await fetch(getPhpUrl('delete_crop.php'), {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });
        
        console.log('Delete response status:', response.status);
        
        const data = await response.json();
        console.log('Delete response data:', data);
        
        if (data.success) {
            // Check if showAlert is available
            if (typeof showAlert === 'function') {
                showAlert(data.message, 'success');
            } else {
                alert(data.message);
            }
            
            // Reload crops
            if (typeof loadMyCrops === 'function') {
                loadMyCrops();
            } else {
                // Fallback: reload the page
                window.location.reload();
            }
        } else {
            if (typeof showAlert === 'function') {
                showAlert(data.message, 'error');
            } else {
                alert(data.message);
            }
        }
    } catch (error) {
        console.error('Delete error:', error);
        if (typeof showAlert === 'function') {
            showAlert('An error occurred while deleting the crop.', 'error');
        } else {
            alert('An error occurred while deleting the crop.');
        }
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
async function logout(event) {
    if (event) {
        event.preventDefault();
    }
    
    if (window.confirm('Are you sure you want to logout?')) {
        try {
            const response = await fetch(getPhpUrl('logout.php'), {
                method: 'GET',
                credentials: 'same-origin'
            });
            const data = await response.json();
            
            if (data.success) {
                // Clear any local storage/session storage if used
                sessionStorage.clear();
                // Redirect to home page after logout
                window.location.href = 'index.html';
            } else {
                console.error('Logout failed:', data);
                window.location.href = 'index.html';
            }
        } catch (error) {
            console.error('Logout error:', error);
            // Redirect anyway
            window.location.href = 'index.html';
        }
    }
    return false;
}

