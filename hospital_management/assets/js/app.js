/**
 * Hospital Management System - Client JS
 * Includes form validation and AJAX helpers
 */

document.addEventListener('DOMContentLoaded', function () {
    // Mobile nav toggle
    const toggle = document.getElementById('navToggle');
    const links = document.getElementById('navLinks');
    if (toggle && links) {
        toggle.addEventListener('click', () => links.classList.toggle('open'));
    }

    // Auto-hide flash after 5s
    const flash = document.getElementById('flashMsg');
    if (flash) {
        setTimeout(() => { flash.style.display = 'none'; }, 5000);
    }

    // Generic form validation
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', function (e) {
            let valid = true;
            form.querySelectorAll('[required]').forEach(el => {
                el.classList.remove('is-invalid');
                if (!el.value.trim()) {
                    el.classList.add('is-invalid');
                    valid = false;
                }
            });
            // Email fields
            form.querySelectorAll('input[type="email"]').forEach(el => {
                if (el.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value)) {
                    el.classList.add('is-invalid');
                    valid = false;
                }
            });
            // Password match
            const pass = form.querySelector('[name="password"]');
            const conf = form.querySelector('[name="confirm_password"]');
            if (pass && conf && pass.value !== conf.value) {
                conf.classList.add('is-invalid');
                valid = false;
                alert('Passwords do not match.');
            }
            if (!valid) {
                e.preventDefault();
                alert('Please fill all required fields correctly.');
            }
        });
    });

    // Live username check (AJAX)
    const usernameInput = document.querySelector('[name="username"][data-check]');
    if (usernameInput) {
        let timer;
        usernameInput.addEventListener('input', function () {
            clearTimeout(timer);
            const val = this.value.trim();
            if (val.length < 3) return;
            timer = setTimeout(() => {
                fetch(`index.php?page=ajax&action=check_username&username=${encodeURIComponent(val)}`)
                    .then(r => r.json())
                    .then(data => {
                        const msg = document.getElementById('username-feedback');
                        if (msg) {
                            msg.textContent = data.exists ? 'Username already taken' : 'Username available';
                            msg.style.color = data.exists ? '#dc3545' : '#198754';
                        }
                    });
            }, 400);
        });
    }

    // Confirm delete
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!confirm(this.getAttribute('data-confirm') || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });

    // Search filter on tables (client-side quick filter)
    const tableSearch = document.getElementById('tableSearch');
    if (tableSearch) {
        tableSearch.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.data-table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
});

// Helper for AJAX GET
function ajaxGet(action, params = {}) {
    const qs = new URLSearchParams({ page: 'ajax', action, ...params });
    return fetch('index.php?' + qs.toString()).then(r => r.json());
}
