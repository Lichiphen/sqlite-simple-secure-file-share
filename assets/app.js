/**
 * Simple Secure File Share - JavaScript
 */

(function() {
    'use strict';

    // ============================================
    // Theme Management (Uses SFS_THEME from header)
    // ============================================

    const ThemeManager = {
        init() {
            // Setup toggle buttons for both old (data-theme-toggle) and new (data-theme-mode) attributes
            document.querySelectorAll('[data-theme-mode]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const mode = btn.dataset.themeMode; // 'light' | 'dark' | 'system'
                    if (window.SFS_THEME && mode) {
                        window.SFS_THEME.setMode(mode);
                        this.updateToggleButtons();
                    }
                });
            });
            
            // Legacy support for data-theme-toggle
            document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const mode = btn.dataset.themeToggle; // 'light' | 'dark' | 'system'
                    if (window.SFS_THEME && mode) {
                        window.SFS_THEME.setMode(mode);
                        this.updateToggleButtons();
                    }
                });
            });
            
            this.updateToggleButtons();
        },
        
        updateToggleButtons() {
            const currentMode = (window.SFS_THEME && window.SFS_THEME.getMode()) || 'system';
            
            // Update data-theme-mode buttons
            document.querySelectorAll('[data-theme-mode]').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.themeMode === currentMode);
            });
            
            // Update legacy data-theme-toggle buttons
            document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.themeToggle === currentMode);
            });
        }
    };

    // ============================================
    // Toast Notifications
    // ============================================

    // ============================================
    // Popup Notification
    // ============================================

    const Toast = {
        show(message, type = 'success', title = null) {
            // Remove existing popup
            const existing = document.querySelector('.popup-overlay');
            if (existing) existing.remove();
            
            // Set default title based on type
            if (!title) {
                title = type === 'success' ? (window.TRANSLATIONS?.success || 'Success') : (window.TRANSLATIONS?.error || 'Error');
            }
            
            const overlay = document.createElement('div');
            overlay.className = 'popup-overlay';
            
            const iconSvg = type === 'success' 
                ? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
                : '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
            
            overlay.innerHTML = `
                <div class="popup-content">
                    <div class="popup-icon ${type}">
                        ${iconSvg}
                    </div>
                    <div class="popup-title">${this.escapeHtml(title)}</div>
                    <div class="popup-message">${this.escapeHtml(message)}</div>
                    <button type="button" class="btn btn-primary w-full" data-popup-close>OK</button>
                </div>
            `;
            
            document.body.appendChild(overlay);
            
            // Activate
            requestAnimationFrame(() => {
                overlay.classList.add('active');
            });
            
            // Close handler
            const closeBtn = overlay.querySelector('[data-popup-close]');
            closeBtn.addEventListener('click', () => {
                overlay.classList.remove('active');
                setTimeout(() => overlay.remove(), 300);
            });
        },
        
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // ============================================
    // Clipboard
    // ============================================

    const Clipboard = {
        copy(text, successMessage = 'Copied!') {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text)
                    .then(() => Toast.show(successMessage))
                    .catch(() => this.fallbackCopy(text, successMessage));
            } else {
                this.fallbackCopy(text, successMessage);
            }
        },
        
        fallbackCopy(text, successMessage) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            
            try {
                document.execCommand('copy');
                Toast.show(successMessage);
            } catch (err) {
                Toast.show('Copy failed');
            }
            
            document.body.removeChild(textarea);
        }
    };

    // ============================================
    // File Upload Manager
    // ============================================

    const FileUploader = {
        files: [],
        dropZone: null,
        fileInput: null,
        fileList: null,
        form: null,
        progressWrapper: null,
        progressBar: null,
        progressText: null,
        submitBtn: null,
        modal: null,
        
        init() {
            this.dropZone = document.getElementById('drop-zone');
            this.fileInput = document.getElementById('file-input');
            this.fileList = document.getElementById('file-list');
            this.form = document.getElementById('upload-form');
            this.progressWrapper = document.getElementById('progress-wrapper');
            this.progressBar = document.getElementById('progress-bar');
            this.progressText = document.getElementById('progress-text');
            this.submitBtn = document.getElementById('submit-btn');
            this.modal = document.getElementById('success-modal');
            
            if (!this.dropZone || !this.form) return;
            
            this.setupDropZone();
            this.setupFileInput();
            this.setupForm();
            this.setupClearAll();
        },
        
        setupDropZone() {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
                this.dropZone.addEventListener(event, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });
            
            ['dragenter', 'dragover'].forEach(event => {
                this.dropZone.addEventListener(event, () => {
                    this.dropZone.classList.add('drag-over');
                }, false);
            });
            
            ['dragleave', 'drop'].forEach(event => {
                this.dropZone.addEventListener(event, () => {
                    this.dropZone.classList.remove('drag-over');
                }, false);
            });
            
            this.dropZone.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                this.addFiles(files);
            });
        },
        
        setupFileInput() {
            this.fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    this.addFiles(e.target.files);
                    e.target.value = '';
                }
            });
        },
        
        setupClearAll() {
            const clearBtn = document.getElementById('clear-all');
            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    this.files = [];
                    this.renderFileList();
                });
            }
        },
        
        addFiles(fileList) {
            Array.from(fileList).forEach(file => {
                const exists = this.files.some(f => f.name === file.name && f.size === file.size);
                if (!exists) {
                    this.files.push(file);
                }
            });
            this.renderFileList();
        },
        
        removeFile(index) {
            this.files.splice(index, 1);
            this.renderFileList();
        },
        
        renderFileList() {
            const container = document.getElementById('file-list-container');
            
            if (this.files.length === 0) {
                container.classList.add('hidden');
                return;
            }
            
            container.classList.remove('hidden');
            this.fileList.innerHTML = '';
            
            this.files.forEach((file, index) => {
                const size = this.formatSize(file.size);
                const li = document.createElement('li');
                li.className = 'file-item';
                li.innerHTML = `
                    <div class="file-info">
                        <svg class="file-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        <div>
                            <div class="file-name truncate" title="${this.escapeHtml(file.name)}">${this.escapeHtml(file.name)}</div>
                            <div class="file-size">${size}</div>
                        </div>
                    </div>
                    <button type="button" class="file-remove" data-index="${index}" title="Remove">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    </button>
                `;
                
                li.querySelector('.file-remove').addEventListener('click', () => {
                    this.removeFile(index);
                });
                
                this.fileList.appendChild(li);
            });
        },
        
        setupForm() {
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSubmit();
            });
        },
        
        async handleSubmit() {
            // Disable button immediately to prevent double submission
            if(this.submitBtn.disabled) return;
            this.submitBtn.disabled = true;
            this.submitBtn.textContent = window.TRANSLATIONS?.uploading || 'Uploading...';

            const title = document.getElementById('share-title').value.trim();
            const passwordEnabled = document.querySelector('input[name="enable_password"]:checked')?.value === '1';
            const password = passwordEnabled ? document.getElementById('share-password').value : '';
            
            if (!title) {
                alert(window.TRANSLATIONS?.title_required || 'Title is required.');
                return;
            }
            
            if (this.files.length === 0) {
                alert(window.TRANSLATIONS?.files_required || 'Please select at least one file.');
                return;
            }
            
            if (passwordEnabled && !password) {
                alert(window.TRANSLATIONS?.password_required || 'Please enter a password.');
                return;
            }
            
            // Prepare FormData
            const formData = new FormData();
            formData.append('action', 'upload');
            formData.append('csrf_token', document.getElementById('csrf-token').value);
            formData.append('share_title', title);
            
            if (passwordEnabled) {
                formData.append('share_password', password);
            }
            
            this.files.forEach(file => {
                formData.append('files[]', file);
            });
            
            // Show progress
            this.submitBtn.disabled = true;
            this.submitBtn.textContent = window.TRANSLATIONS?.uploading || 'Uploading...';
            this.progressWrapper.classList.remove('hidden');
            this.progressBar.style.width = '0%';
            this.progressText.textContent = '0%';
            
            try {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', window.location.href, true);
                
                xhr.upload.onprogress = (e) => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        this.progressBar.style.width = percent + '%';
                        this.progressText.textContent = percent + '%';
                    }
                };
                
                xhr.onload = () => {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                this.progressBar.style.width = '100%';
                                this.progressText.textContent = '100%';
                                
                                // Set share URL in modal
                                const baseUrl = (window.SFS_CONFIG && window.SFS_CONFIG.baseUrl) 
                                    ? window.SFS_CONFIG.baseUrl 
                                    : window.location.origin;
                                    
                                const shareUrl = baseUrl + '/s/' + response.token;
                                const urlInput = document.getElementById('modal-share-url');
                                const copyBtn = document.getElementById('modal-copy-btn');
                                const openBtn = document.getElementById('modal-open-btn');
                                
                                if (urlInput) urlInput.value = shareUrl;
                                if (openBtn) openBtn.href = shareUrl;
                                
                                if (copyBtn) {
                                    // Remove old listeners to prevent duplicates
                                    const newBtn = copyBtn.cloneNode(true);
                                    copyBtn.parentNode.replaceChild(newBtn, copyBtn);
                                    
                                    newBtn.addEventListener('click', () => {
                                        window.SFS.Clipboard.copy(shareUrl, window.TRANSLATIONS?.url_copied || 'URL copied!');
                                        // Reload after a short delay
                                        setTimeout(() => {
                                            window.SFS.Toast.show(window.TRANSLATIONS?.reloading || 'Reloading...');
                                            setTimeout(() => window.location.reload(), 1000);
                                        }, 500);
                                    });
                                }
                                
                                // Show success modal - create dynamically to avoid CSS issues
                                setTimeout(() => {
                                    // Try existing modal first
                                    let modal = this.modal || document.getElementById('success-modal');
                                    
                                    // Create modal dynamically if needed or if CSS doesn't work
                                    const showDynamicModal = () => {
                                        // Remove any existing dynamic modal
                                        const existing = document.getElementById('dynamic-success-modal');
                                        if (existing) existing.remove();
                                        
                                        // Check if dark mode
                                        const isDark = document.documentElement.dataset.theme === 'dark';
                                        
                                        const dynamicModal = document.createElement('div');
                                        dynamicModal.id = 'dynamic-success-modal';
                                        dynamicModal.style.cssText = `
                                            position: fixed;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                            background: rgba(0, 0, 0, 0.7);
                                            backdrop-filter: blur(4px);
                                            z-index: 999999;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                        `;
                                        
                                        const content = document.createElement('div');
                                        content.style.cssText = `
                                            background: ${isDark ? '#1e293b' : '#ffffff'};
                                            padding: 2rem;
                                            border-radius: 12px;
                                            max-width: 400px;
                                            width: 90%;
                                            text-align: center;
                                            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
                                            border: 1px solid ${isDark ? '#334155' : '#e5e7eb'};
                                        `;
                                        
                                        content.innerHTML = `
                                            <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
                                            <h2 style="margin: 0 0 0.5rem 0; color: #10b981;">アップロード完了！</h2>
                                            <p style="color: ${isDark ? '#94a3b8' : '#666'}; margin-bottom: 1.5rem;">共有リンクが作成されました</p>
                                            <input type="text" value="${shareUrl}" readonly 
                                                style="width: 100%; padding: 0.75rem; border: 1px solid ${isDark ? '#475569' : '#ddd'}; border-radius: 6px; margin-bottom: 1rem; font-size: 0.875rem; box-sizing: border-box; background: ${isDark ? '#0f172a' : '#f9fafb'}; color: ${isDark ? '#e2e8f0' : '#1f2937'};"
                                                id="dynamic-modal-url">
                                            <div style="display: flex; gap: 0.5rem;">
                                                <button id="dynamic-copy-btn" style="flex: 1; padding: 0.75rem; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                                                    コピー
                                                </button>
                                                <button id="dynamic-close-btn" style="flex: 1; padding: 0.75rem; background: ${isDark ? '#475569' : '#e5e7eb'}; color: ${isDark ? '#e2e8f0' : '#374151'}; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                                                    閉じる
                                                </button>
                                            </div>
                                        `;
                                        
                                        dynamicModal.appendChild(content);
                                        document.body.appendChild(dynamicModal);
                                        
                                        // Event listeners
                                        document.getElementById('dynamic-copy-btn').addEventListener('click', () => {
                                            navigator.clipboard.writeText(shareUrl).then(() => {
                                                window.SFS.Toast.show(window.TRANSLATIONS?.url_copied || 'URL copied!');
                                                setTimeout(() => {
                                                    window.SFS.Toast.show(window.TRANSLATIONS?.reloading || 'Reloading...');
                                                    setTimeout(() => window.location.reload(), 1000);
                                                }, 500);
                                            });
                                        });
                                        
                                        document.getElementById('dynamic-close-btn').addEventListener('click', () => {
                                            dynamicModal.remove();
                                            window.location.reload();
                                        });
                                        
                                        console.log('Dynamic modal created and displayed');
                                    };
                                    
                                    if (modal) {
                                        modal.classList.add('active');
                                        
                                        // Check if modal is actually visible after 100ms
                                        setTimeout(() => {
                                            const rect = modal.getBoundingClientRect();
                                            const style = window.getComputedStyle(modal);
                                            
                                            console.log('Modal check:', {
                                                width: rect.width,
                                                height: rect.height,
                                                visibility: style.visibility,
                                                opacity: style.opacity
                                            });
                                            
                                            // If modal is not visible, use dynamic modal
                                            if (rect.width === 0 || rect.height === 0 || 
                                                style.visibility === 'hidden' || 
                                                !style.visibility ||
                                                style.opacity === '0' ||
                                                !style.opacity) {
                                                console.warn('Static modal not visible, using dynamic modal');
                                                modal.classList.remove('active');
                                                showDynamicModal();
                                            } else {
                                                console.log('Static modal displayed successfully');
                                            }
                                        }, 100);
                                    } else {
                                        console.log('No static modal found, using dynamic modal');
                                        showDynamicModal();
                                    }
                                }, 300);
                            } else {
                                alert(response.message || 'Upload failed');
                                this.resetForm();
                            }
                        } catch (err) {
                            alert('Server response error');
                            this.resetForm();
                        }
                    } else {
                        alert('Upload failed. Status: ' + xhr.status);
                        this.resetForm();
                    }
                };
                
                xhr.onerror = () => {
                    alert('Network error');
                    this.resetForm();
                };
                
                xhr.send(formData);
            } catch (err) {
                alert('Upload error: ' + err.message);
                this.resetForm();
            }
        },
        
        resetForm() {
            this.submitBtn.disabled = false;
            this.submitBtn.textContent = window.TRANSLATIONS?.upload_and_create || 'Upload and Create Share Link';
            this.progressWrapper.classList.add('hidden');
            this.progressBar.style.width = '0%';
        },
        
        formatSize(bytes) {
            const units = ['B', 'KB', 'MB', 'GB'];
            let i = 0;
            while (bytes >= 1024 && i < units.length - 1) {
                bytes /= 1024;
                i++;
            }
            return bytes.toFixed(1) + ' ' + units[i];
        },
        
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // ============================================
    // Password Field Manager
    // ============================================

    const PasswordManager = {
        init() {
            this.setupToggle();
            this.setupGenerate();
            this.setupPasswordRadios();
        },
        
        setupPasswordRadios() {
            const radios = document.querySelectorAll('input[name="enable_password"]');
            const container = document.getElementById('password-container');
            const input = document.getElementById('share-password');
            
            if (!radios.length || !container) return;
            
            radios.forEach(radio => {
                radio.addEventListener('change', () => {
                    if (radio.value === '1') {
                        container.classList.remove('hidden');
                        if (input) input.required = true;
                    } else {
                        container.classList.add('hidden');
                        if (input) {
                            input.required = false;
                            input.value = '';
                        }
                    }
                });
            });
        },
        
        setupToggle() {
            document.querySelectorAll('[data-toggle-password]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.dataset.togglePassword;
                    const input = document.getElementById(targetId);
                    if (!input) return;
                    
                    const icon = btn.querySelector('svg');
                    
                    if (input.classList.contains('password-masked')) {
                        input.classList.remove('password-masked');
                        if (icon) icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
                    } else {
                        input.classList.add('password-masked');
                        if (icon) icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
                    }
                });
            });
        },
        
        setupGenerate() {
            document.querySelectorAll('[data-generate-password]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.dataset.generatePassword;
                    const input = document.getElementById(targetId);
                    if (!input) return;
                    
                    const password = this.generate(12);
                    input.value = password;
                    input.classList.remove('password-masked');
                    
                    Clipboard.copy(password, window.TRANSLATIONS?.password_copied || 'Password copied!');
                });
            });
        },
        
        generate(length = 12) {
            const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';
            const types = [
                'abcdefghijklmnopqrstuvwxyz',
                'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                '0123456789',
                '!@#$%^&*()_+'
            ];
            
            let password = '';
            
            // Ensure at least one character from each type
            types.forEach(t => {
                password += t.charAt(Math.floor(Math.random() * t.length));
            });
            
            // Fill remaining length
            for (let i = password.length; i < length; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            
            // Shuffle
            return password.split('').sort(() => 0.5 - Math.random()).join('');
        }
    };

    // ============================================
    // Copy URL Handler
    // ============================================

    const CopyHandler = {
        init() {
            document.querySelectorAll('[data-copy]').forEach(el => {
                el.addEventListener('click', () => {
                    const text = el.dataset.copy;
                    Clipboard.copy(text, window.TRANSLATIONS?.url_copied || 'URL copied!');
                });
            });
        }
    };

    // ============================================
    // Modal Handler
    // ============================================

    const ModalHandler = {
        init() {
            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const modal = btn.closest('.modal-overlay');
                    if (modal) {
                        modal.classList.remove('active');
                    }
                });
            });
            
            document.querySelectorAll('[data-modal-refresh]').forEach(btn => {
                btn.addEventListener('click', () => {
                    window.location.reload();
                });
            });
        }
    };

    // Note: Initialization at bottom of file

    // ============================================
    // Share List Controller
    // ============================================

    const ShareListController = {
        data: [],
        state: {
            sort: 'created_at',
            order: 'desc',
            filter: '',
            page: 1,
            perPage: 10
        },

        init() {
            if (!document.getElementById('share-list-body')) return;
            if (window.SFS_DATA) {
                this.data = window.SFS_DATA;
                // Setup initial icon state
                this.updateSortIcons();
                this.render();
                this.setupControls();
            }
        },

        setupControls() {
            // Search
            const searchInput = document.getElementById('share-search');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    this.state.filter = e.target.value.toLowerCase();
                    this.state.page = 1;
                    this.render();
                });
            }
            
            // Per Page
            const perPageSelect = document.getElementById('per-page-select');
            if (perPageSelect) {
                perPageSelect.addEventListener('change', (e) => {
                    this.state.perPage = parseInt(e.target.value);
                    this.state.page = 1;
                    this.render();
                });
            }
            
            // Sort Headers
            document.querySelectorAll('th[data-sort]').forEach(th => {
                th.addEventListener('click', () => {
                    const col = th.dataset.sort;
                    if (this.state.sort === col) {
                        this.state.order = this.state.order === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.state.sort = col;
                        this.state.order = 'asc';
                        // Custom defaults
                        if (['created_at', 'id', 'file_count', 'download_count'].includes(col)) {
                            this.state.order = 'desc';
                        }
                    }
                    this.updateSortIcons();
                    this.render();
                });
            });
            
            // Password update
            document.getElementById('update-password-btn')?.addEventListener('click', () => {
                const id = UI.currentEditId;
                const pass = document.getElementById('edit-password').value;
                UI.updatePassword(id, pass);
            });
            
            // Edit modal - Generate random password
            document.getElementById('edit-generate-password')?.addEventListener('click', () => {
                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
                let password = '';
                for (let i = 0; i < 10; i++) {
                    password += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                const input = document.getElementById('edit-password');
                if (input) {
                    input.value = password;
                    input.classList.remove('password-masked');
                    input.classList.add('password-visible');
                }
                window.SFS.Clipboard.copy(password, window.TRANSLATIONS?.password_copied || 'Password copied!');
            });
            
            // Edit modal - Toggle password visibility
            document.querySelector('.edit-password-toggle')?.addEventListener('click', () => {
                const input = document.getElementById('edit-password');
                if (input) {
                    if (input.classList.contains('password-masked')) {
                        input.classList.remove('password-masked');
                        input.classList.add('password-visible');
                    } else {
                        input.classList.remove('password-visible');
                        input.classList.add('password-masked');
                    }
                }
            });
            
            // Delete share from edit modal
            document.getElementById('delete-share-btn')?.addEventListener('click', () => {
                const id = UI.currentEditId;
                if (!id) return;
                
                if (!confirm(window.SFS_LANG.delete_confirm)) return;
                
                // Submit delete via form
                const form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_share">
                    <input type="hidden" name="share_id" value="${id}">
                    <input type="hidden" name="csrf_token" value="${window.SFS_CSRF}">
                `;
                document.body.appendChild(form);
                form.submit();
            });
            
            // Mobile Pager
            document.getElementById('mobile-prev-page')?.addEventListener('click', () => {
                 if(this.state.page > 1) {
                     this.state.page--;
                     this.render();
                     window.scrollTo(0,0);
                 }
            });
            document.getElementById('mobile-next-page')?.addEventListener('click', () => {
                 // Check max page? will do in render logic
                 this.state.page++;
                 this.render();
                 window.scrollTo(0,0);
            });
        },
        
        updateSortIcons() {
            document.querySelectorAll('th[data-sort] .sort-icon').forEach(span => {
                const th = span.closest('th');
                const col = th.dataset.sort;
                
                if (col === this.state.sort) {
                    span.textContent = this.state.order === 'asc' ? ' ▲' : ' ▼';
                    th.style.color = 'var(--color-primary)';
                } else {
                    span.textContent = ' ⇅';
                    span.style.opacity = '0.3';
                    th.style.color = '';
                }
            });
        },
        
        getFilteredAndSortedData() {
            // Filter
            let result = this.data.filter(item => {
                if (!this.state.filter) return true;
                const q = this.state.filter;
                return item.title.toLowerCase().includes(q) || 
                       String(item.id).includes(q) ||
                       (item.token && item.token.includes(q));
            });
            
            // Sort
            result.sort((a, b) => {
                let va = a[this.state.sort];
                let vb = b[this.state.sort];
                
                if (this.state.sort === 'is_locked') {
                     va = !!a.password_hash;
                     vb = !!b.password_hash;
                }
                
                // Numeric sort
                if (['id', 'file_count', 'download_count'].includes(this.state.sort)) {
                    va = Number(va);
                    vb = Number(vb);
                }
                
                if (va < vb) return this.state.order === 'asc' ? -1 : 1;
                if (va > vb) return this.state.order === 'asc' ? 1 : -1;
                return 0;
            });
            
            return result;
        },

        render() {
            const tbody = document.getElementById('share-list-body');
            if(!tbody) return;
            
            const processed = this.getFilteredAndSortedData();
            
            // Pagination
            const total = processed.length;
            const maxPage = Math.ceil(total / this.state.perPage) || 1;
            if (this.state.page > maxPage) this.state.page = maxPage;
            if (this.state.page < 1) this.state.page = 1;
            
            const start = (this.state.page - 1) * this.state.perPage;
            const end = start + this.state.perPage;
            const pageItems = processed.slice(start, end);
            
            // Show/Hide controls
            const controls = document.getElementById('list-controls');
            if (controls) {
                 controls.style.display = this.data.length > 5 ? 'flex' : 'none';
            }
            // Mobile Action Bar
            const mobileBar = document.getElementById('mobile-action-bar');
            if(mobileBar && window.innerWidth <= 768 && this.data.length > 5) {
                mobileBar.style.transform = 'translateY(0)';
                document.body.style.paddingBottom = '60px'; // Prevent overlap
            } else if (mobileBar) {
                mobileBar.style.transform = 'translateY(100%)';
                document.body.style.paddingBottom = '0';
            }
            
            // Render Rows
            tbody.innerHTML = '';
            pageItems.forEach(item => {
                tbody.appendChild(this.createRow(item));
            });
            
            this.renderPagination(maxPage);
        },
        
        renderPagination(maxPage) {
            const container = document.getElementById('pagination-pages');
            if(!container) return;
            container.innerHTML = '';
            
            if (maxPage <= 1) return;
            
            for (let i = 1; i <= maxPage; i++) {
                const btn = document.createElement('button');
                btn.className = 'btn btn-sm ' + (i === this.state.page ? 'btn-primary' : 'btn-secondary');
                btn.textContent = i;
                btn.onclick = () => {
                    this.state.page = i;
                    this.render();
                };
                container.appendChild(btn);
            }
            
            // Mobile pager state
            const prev = document.getElementById('mobile-prev-page');
            const next = document.getElementById('mobile-next-page');
            if(prev) prev.disabled = this.state.page <= 1;
            if(next) next.disabled = this.state.page >= maxPage;
        },
        
        createRow(item) {
            const tr = document.createElement('tr');
            const L = window.SFS_LANG;
            
            // ID (no S- prefix) - uses .col-id class
            tr.innerHTML += `<td class="col-id" style="padding: 0.75rem 0.5rem;">${item.id}</td>`;
            
            // Title (wrap on mobile)
            tr.innerHTML += `<td style="padding: 0.75rem 0.5rem;"><div class="font-medium" style="color: var(--color-text-primary); word-break: break-word;">${this.escape(item.title)}</div></td>`;
            
            // Status (icon only, no text) - uses .col-status
            const isLocked = !!item.password_hash;
            const badgeClass = isLocked ? 'badge-success' : 'badge-accent';
            const icon = isLocked 
                ? `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>`
                : `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>`;
            
            tr.innerHTML += `<td class="col-status" style="padding: 0.75rem 0.5rem;"><span class="badge ${badgeClass}" style="padding: 0.35rem 0.5rem;">${icon}</span></td>`;
            
            // Files count
            tr.innerHTML += `<td class="text-center" style="padding: 0.75rem 0.5rem;">${item.file_count}</td>`;
            
            // Download count
            tr.innerHTML += `<td class="text-center" style="padding: 0.75rem 0.5rem;"><span class="badge badge-muted">${item.download_count}</span></td>`;
            
            // Created At (full datetime - no truncation)
            const dateStr = item.created_at.substring(0,16).replace('T', ' ');
            tr.innerHTML += `<td style="font-size: 0.75rem; color: var(--color-text-muted); padding: 0.75rem 0.5rem; white-space: nowrap;">${dateStr}</td>`;
            
            // Actions
            const actionsTd = document.createElement('td');
            actionsTd.className = 'text-right';
            actionsTd.style.cssText = 'white-space: nowrap; padding: 0.75rem 0.5rem;';
            
            const btnGroup = document.createElement('div');
            btnGroup.className = 'flex items-center justify-end';
            btnGroup.style.gap = '0.5rem'; // Wider gap for mobile touch
            
            // Copy URL
            const url = window.SFS_CONFIG.baseUrl + '/s/' + item.token;
            const copyBtn = document.createElement('button');
            copyBtn.className = 'btn btn-secondary btn-sm';
            copyBtn.style.cssText = 'padding: 0.5rem; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;';
            copyBtn.title = L.copy;
            copyBtn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>`;
            copyBtn.onclick = () => window.SFS.Clipboard.copy(url);
            btnGroup.appendChild(copyBtn);
            
            // Edit
            const editBtn = document.createElement('button');
            editBtn.className = 'btn btn-secondary btn-sm';
            editBtn.style.cssText = 'padding: 0.5rem; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;';
            editBtn.title = L.edit;
            editBtn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>`;
            editBtn.onclick = () => window.SFS.UI.openEditModal(item.id);
            btnGroup.appendChild(editBtn);

            // Delete (Form)
            const delForm = document.createElement('form');
            delForm.method = 'post';
            delForm.style.display = 'inline';
            delForm.onsubmit = (e) => {
                if(!confirm(L.delete_confirm)) e.preventDefault();
            };
            delForm.innerHTML = `
                <input type="hidden" name="action" value="delete_share">
                <input type="hidden" name="share_id" value="${item.id}">
                <input type="hidden" name="csrf_token" value="${window.SFS_CSRF}">
                <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.5rem; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center;" title="${L.delete}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
            `;
            btnGroup.appendChild(delForm);
            
            actionsTd.appendChild(btnGroup);
            tr.appendChild(actionsTd);
            
            return tr;
        },
        
        escape(text) {
             const div = document.createElement('div');
             div.textContent = text;
             return div.innerHTML;
        }
    };

    // UI Helper
    const UI = {
        toggleSearch() {
            const controls = document.getElementById('list-controls');
            if (controls) {
                controls.style.display = 'flex';
                document.getElementById('share-search')?.focus();
            }
        },
        toggleSort() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        
        currentEditId: null,
        
        openEditModal(id) {
            console.log('openEditModal called with id:', id);
            
            const modal = document.getElementById('edit-modal');
            const shareIdInput = document.getElementById('edit-share-id');
            const titleEl = document.getElementById('edit-modal-title');
            const passwordInput = document.getElementById('edit-password');
            
            if (!modal) {
                console.error('edit-modal not found!');
                alert('Edit modal not found. Please refresh the page.');
                return;
            }
            
            this.currentEditId = id;
            if (shareIdInput) shareIdInput.value = id;
            modal.classList.add('active');
            
            const share = window.SFS_DATA.find(s => s.id === id);
            if (share) {
                if (titleEl) titleEl.textContent = `${window.SFS_LANG.edit}: ${share.title}`;
                this.loadFileList(share);
            }
            if (passwordInput) passwordInput.value = '';
        },
        
        loadFileList(share) {
            const container = document.getElementById('edit-file-list');
            container.innerHTML = '';
            
            if (!share.files || share.files.length === 0) {
                container.innerHTML = '<div style="padding:1rem; text-align:center; color:var(--color-text-muted);">No files</div>';
                return;
            }
            
            share.files.forEach(file => {
                const row = document.createElement('div');
                row.style.display = 'flex';
                row.style.justifyContent = 'space-between';
                row.style.alignItems = 'center';
                row.style.padding = '0.5rem';
                row.style.borderBottom = '1px solid var(--color-border)';
                
                const nameSpan = document.createElement('span');
                nameSpan.textContent = file.original_name;
                nameSpan.title = file.original_name;
                nameSpan.style.overflow = 'hidden';
                nameSpan.style.textOverflow = 'ellipsis';
                nameSpan.style.whiteSpace = 'nowrap';
                nameSpan.style.marginRight = '1rem';
                nameSpan.style.fontSize = '0.9rem';
                
                const delBtn = document.createElement('button');
                delBtn.className = 'btn btn-danger btn-sm';
                delBtn.innerHTML = '&times;'; 
                delBtn.style.minWidth = '24px';
                delBtn.onclick = () => this.deleteFile(share.id, file.system_name, row, file.original_name);
                
                row.appendChild(nameSpan);
                row.appendChild(delBtn);
                container.appendChild(row);
            });
        },
        
        deleteFile(shareId, systemName, rowElement, fileName) {
            if(!confirm(window.SFS_LANG.delete_confirm + "\n" + fileName)) return;
            
            fetch(window.SFS_CONFIG.baseUrl + '/admin', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete_file&share_id=${shareId}&system_name=${encodeURIComponent(systemName)}&csrf_token=${window.SFS_CSRF}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    rowElement.remove();
                    const share = window.SFS_DATA.find(s => s.id === shareId);
                    if(share) {
                        share.files = share.files.filter(f => f.system_name !== systemName);
                        share.file_count = share.files.length;
                        window.SFS.List.render(); 
                    }
                    window.SFS.Toast.show(window.SFS_LANG.delete + ' OK');
                } else {
                    window.SFS.Toast.show(data.message || 'Error', 'error');
                }
            })
            .catch(err => window.SFS.Toast.show('Network Error', 'error'));
        },
        
        updatePassword(id, password) {
             fetch(window.SFS_CONFIG.baseUrl + '/admin', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update_share&share_id=${id}&password=${encodeURIComponent(password)}&csrf_token=${window.SFS_CSRF}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('edit-modal').classList.remove('active');
                    window.SFS.Toast.show('Password Updated');
                    // Reload after update
                    setTimeout(() => {
                        window.SFS.Toast.show(window.TRANSLATIONS?.reloading || 'Reloading...');
                        setTimeout(() => window.location.reload(), 1000);
                    }, 500);
                } else {
                     window.SFS.Toast.show(data.message || 'Error', 'error');
                }
            })
            .catch(err => window.SFS.Toast.show('Network Error', 'error'));
        }
    };
    
    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        ThemeManager.init();
        FileUploader.init();
        PasswordManager.init();
        CopyHandler.init();
        ModalHandler.init();
        ShareListController.init(); // New controller
    });

    // Export for global access
    window.SFS = {
        Toast,
        Clipboard,
        ThemeManager,
        UI,
        List: ShareListController
    };

})();
