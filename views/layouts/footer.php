    <script>
        // Sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            let isExpanded = false;
            let hoverTimeout;

            if (sidebar) {
                sidebar.addEventListener('mouseenter', function() {
                    clearTimeout(hoverTimeout);
                    isExpanded = true;
                    this.classList.add('expanded');
                });

                sidebar.addEventListener('mouseleave', function() {
                    isExpanded = false;
                    this.classList.remove('expanded');
                    
                    hoverTimeout = setTimeout(() => {
                        if (!isExpanded) {
                            // Sidebar collapsed
                        }
                    }, 100);
                });
            }

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Escape to close modals
                if (e.key === 'Escape') {
                    const visibleModals = document.querySelectorAll('.modal[style*="block"]');
                    visibleModals.forEach(modal => {
                        modal.style.display = 'none';
                    });
                    
                    const profilePanel = document.getElementById('profilePanel');
                    if (profilePanel && profilePanel.style.display !== 'none') {
                        closeProfilePanel();
                    }
                }
                
                // Ctrl + N for new patient
                if (e.ctrlKey && e.key === 'n') {
                    e.preventDefault();
                    const addBtn = document.querySelector('.btn-primary');
                    if (addBtn && addBtn.textContent.includes('Agregar')) {
                        addBtn.click();
                    }
                }
                
                // Ctrl + F for search
                if (e.ctrlKey && e.key === 'f') {
                    e.preventDefault();
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }
            });
        });

        // Global utility functions
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function formatDateTime(dateTimeString) {
            const date = new Date(dateTimeString);
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function calculateAge(birthDate) {
            const today = new Date();
            const birth = new Date(birthDate);
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            
            return age;
        }

        // Loading state management
        function showLoading(element) {
            if (element) {
                element.classList.add('loading');
                const spinner = document.createElement('div');
                spinner.className = 'spinner';
                element.prepend(spinner);
            }
        }

        function hideLoading(element) {
            if (element) {
                element.classList.remove('loading');
                const spinner = element.querySelector('.spinner');
                if (spinner) {
                    spinner.remove();
                }
            }
        }

        // Form validation
        function validateForm(formElement) {
            const requiredFields = formElement.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    field.style.borderColor = '#dee2e6';
                }
            });
            
            return isValid;
        }

        // AJAX helper function
        function makeRequest(url, options = {}) {
            const defaultOptions = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };
            
            const finalOptions = { ...defaultOptions, ...options };
            
            return fetch(url, finalOptions)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('Request failed:', error);
                    showNotification('Error en la conexión', 'error');
                    throw error;
                });
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

        // Auto-save functionality
        function setupAutoSave(formElement, saveUrl, interval = 30000) {
            if (!formElement || !saveUrl) return;
            
            const autoSave = () => {
                const formData = new FormData(formElement);
                formData.append('auto_save', '1');
                
                fetch(saveUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Auto-saved successfully');
                    }
                })
                .catch(error => {
                    console.error('Auto-save failed:', error);
                });
            };
            
            setInterval(autoSave, interval);
        }

        // Theme management
        function toggleTheme() {
            const currentTheme = localStorage.getItem('theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        // Initialize theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });

        // Export functionality
        function exportToCSV(data, filename) {
            const csv = data.map(row => 
                Object.values(row).map(value => 
                    typeof value === 'string' ? `"${value.replace(/"/g, '""')}"` : value
                ).join(',')
            ).join('\n');
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Print functionality
        function printElement(elementId) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Imprimir</title>
                        <style>
                            body { font-family: Arial, sans-serif; }
                            .no-print { display: none !important; }
                        </style>
                    </head>
                    <body>
                        ${element.innerHTML}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        // Clipboard functionality
        function copyToClipboard(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    showNotification('Copiado al portapapeles', 'success');
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showNotification('Copiado al portapapeles', 'success');
            }
        }

        // Local storage helpers
        function saveToLocalStorage(key, data) {
            try {
                localStorage.setItem(key, JSON.stringify(data));
            } catch (error) {
                console.error('Error saving to localStorage:', error);
            }
        }

        function getFromLocalStorage(key, defaultValue = null) {
            try {
                const item = localStorage.getItem(key);
                return item ? JSON.parse(item) : defaultValue;
            } catch (error) {
                console.error('Error reading from localStorage:', error);
                return defaultValue;
            }
        }

        // Session management
        function checkSession() {
            fetch('/api/check-session')
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        showNotification('Sesión expirada. Redirigiendo...', 'error');
                        setTimeout(() => {
                            window.location.href = '/login';
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Session check failed:', error);
                });
        }

        // Check session every 5 minutes
        setInterval(checkSession, 300000);

        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>