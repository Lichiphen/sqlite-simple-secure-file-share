<?php
/**
 * How to Use Template
 * Updated: Auto-generated Table of Contents with collapsible sections
 */

// Ensure direct access is prevented
if (!defined('BASE_PATH')) exit('No direct access.');

$pageTitle = __('how_to_use');
$currentPage = 'howto';

// Include header
include __DIR__ . '/partials/header.php';
?>
    <style>
        .howto-section { margin-bottom: 2rem; scroll-margin-top: 80px; }
        .howto-section h2 { margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: center; gap: 0.5rem; }
        .howto-section h2[id] { cursor: pointer; }
        .howto-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .feature-card { padding: 1rem; background: var(--color-bg-tertiary); border-radius: var(--radius-md); border: 1px solid var(--color-border); }
        .step-box { padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-border); }
        .step-box h3 { margin: 0 0 0.75rem; font-weight: 700; }
        .step-box ol, .step-box ul { margin: 0; padding-left: 1.5rem; }
        .step-box li { margin-bottom: 0.5rem; color: var(--color-text-secondary); }
        
        /* Table of Contents */
        .toc-container {
            background: var(--color-bg-tertiary);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .toc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            background: var(--color-bg-secondary);
            cursor: pointer;
            user-select: none;
            transition: background 0.2s;
        }
        .toc-header:hover {
            background: var(--color-border);
        }
        .toc-header h3 {
            margin: 0;
            font-size: 1rem;
            color: var(--color-text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .toc-toggle {
            width: 24px;
            height: 24px;
            transition: transform 0.3s;
            color: var(--color-text-muted);
        }
        .toc-container.collapsed .toc-toggle {
            transform: rotate(-90deg);
        }
        .toc-content {
            padding: 1rem 1.25rem;
            max-height: 500px;
            overflow-y: auto;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }
        .toc-container.collapsed .toc-content {
            max-height: 0;
            padding-top: 0;
            padding-bottom: 0;
            overflow: hidden;
        }
        .toc-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .toc-list li {
            margin-bottom: 0.5rem;
        }
        .toc-list a {
            color: var(--color-text-secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-md);
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        .toc-list a:hover {
            background: var(--color-accent-light);
            color: var(--color-accent);
        }
        .toc-list .toc-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }
        .toc-list .toc-number {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--color-accent);
            color: white;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: 600;
            flex-shrink: 0;
        }
    </style>

    <main class="container" style="padding: 2rem 1rem; max-width: 56rem;">
        <div class="card">
            <div class="card-body">
                <h1 style="margin: 0 0 0.5rem; font-size: 1.5rem; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-accent);">
                        <path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/>
                    </svg>
                    <?php _e('how_to_use'); ?> - <?php _e('app_name'); ?>
                </h1>
                
                <div style="padding: 1rem; background: var(--color-accent-light); border-radius: var(--radius-md); margin-bottom: 2rem;">
                    <p style="margin: 0; color: var(--color-accent);">
                        <strong><?php _e('version'); ?>:</strong> <?php echo APP_VERSION; ?> | 
                        <strong><?php _e('author'); ?>:</strong> AI Generator, Direction: Lichiphen
                    </p>
                </div>
                
                <!-- Overview Description -->
                <p style="color: var(--color-text-secondary); margin-bottom: 1.5rem; line-height: 1.7;">
                    <?php _e('overview_desc'); ?><br>
                    <?php _e('overview_desc2'); ?>
                </p>
                
                <!-- Auto-generated Table of Contents -->
                <div class="toc-container" id="toc">
                    <div class="toc-header" onclick="toggleToc()">
                        <h3>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                            <?php _e('table_of_contents'); ?>
                        </h3>
                        <svg class="toc-toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="toc-content">
                        <ul class="toc-list" id="toc-list">
                            <!-- Will be populated by JavaScript -->
                        </ul>
                    </div>
                </div>
                
                <!-- Section 1: Key Features -->
                <section class="howto-section" id="section-features">
                    <h2 style="color: var(--color-text-primary);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #eab308;">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <?php _e('key_features'); ?>
                    </h2>
                    <div class="howto-grid">
                        <div class="feature-card">
                            <h3 style="margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.5rem; color: var(--color-success);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                                </svg>
                                <?php _e('password_protection'); ?>
                            </h3>
                            <p class="text-sm" style="margin: 0; color: var(--color-text-secondary);"><?php _e('password_protection_feature'); ?></p>
                        </div>
                        <div class="feature-card">
                            <h3 style="margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.5rem; color: var(--color-accent);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                                    <line x1="6" y1="20" x2="6" y2="14"/>
                                </svg>
                                <?php _e('download_counter'); ?>
                            </h3>
                            <p class="text-sm" style="margin: 0; color: var(--color-text-secondary);"><?php _e('download_counter_desc'); ?></p>
                        </div>
                        <div class="feature-card">
                            <h3 style="margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.5rem; color: #a855f7;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                <?php _e('zip_download'); ?>
                            </h3>
                            <p class="text-sm" style="margin: 0; color: var(--color-text-secondary);"><?php _e('zip_download_desc'); ?></p>
                        </div>
                        <div class="feature-card">
                            <h3 style="margin: 0 0 0.5rem; display: flex; align-items: center; gap: 0.5rem; color: var(--color-error);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                                <?php _e('direct_link_prevention'); ?>
                            </h3>
                            <p class="text-sm" style="margin: 0; color: var(--color-text-secondary);"><?php _e('direct_link_desc'); ?></p>
                        </div>
                    </div>
                </section>
                
                <!-- Section 2: Usage Instructions -->
                <section class="howto-section" id="section-usage">
                    <h2 style="color: var(--color-text-primary);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #6366f1;">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <?php _e('usage_instructions'); ?>
                    </h2>
                    
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div class="step-box" style="background: var(--color-success-bg); border-color: var(--color-success);">
                            <h3 style="color: var(--color-success);"><?php _e('step1'); ?></h3>
                            <ol>
                                <li><?php _e('step1_1'); ?></li>
                                <li><?php _e('step1_2'); ?></li>
                                <li><?php _e('step1_3'); ?></li>
                                <li><?php _e('step1_4'); ?></li>
                                <li><?php _e('step1_5'); ?></li>
                            </ol>
                        </div>
                        
                        <div class="step-box" style="background: var(--color-accent-light); border-color: var(--color-accent);">
                            <h3 style="color: var(--color-accent);"><?php _e('step2'); ?></h3>
                            <ul>
                                <li><?php _e('step2_1'); ?></li>
                                <li><?php _e('step2_2'); ?></li>
                                <li><?php _e('step2_3'); ?></li>
                            </ul>
                        </div>
                        
                        <div class="step-box" style="background: var(--color-bg-tertiary); border-color: #a855f7;">
                            <h3 style="color: #a855f7;"><?php _e('step3'); ?></h3>
                            <ul>
                                <li><?php _e('step3_1'); ?></li>
                                <li><?php _e('step3_2'); ?></li>
                                <li><?php _e('step3_3'); ?></li>
                            </ul>
                        </div>
                    </div>
                </section>
                
                <!-- Section 3: Security Features -->
                <section class="howto-section" id="section-security">
                    <h2 style="color: var(--color-text-primary);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-success);">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <?php _e('security_features'); ?>
                    </h2>
                    <ul style="color: var(--color-text-secondary);">
                        <li><strong><?php _e('direct_access_prevention'); ?></strong> <?php _e('direct_access_desc'); ?></li>
                        <li><strong><?php _e('password_encryption'); ?></strong> <?php _e('password_encryption_desc'); ?></li>
                        <li><strong><?php _e('cookie_auth'); ?></strong> <?php _e('cookie_auth_desc'); ?></li>
                        <li><strong><?php _e('referer_check'); ?></strong> <?php _e('referer_check_desc'); ?></li>
                    </ul>
                </section>
                
                <!-- Section 4: Password Tips -->
                <section class="howto-section" id="section-password">
                    <h2 style="color: var(--color-text-primary);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-warning);">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                        <?php _e('password_tips_title'); ?>
                    </h2>
                    
                    <div class="step-box" style="background: var(--color-warning-bg); border: 1px solid var(--color-warning); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;">
                        <h3 style="margin: 0 0 0.75rem; color: var(--color-warning); font-size: 1rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.5rem;">
                                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                            <?php _e('password_forgot_title'); ?>
                        </h3>
                        <p style="color: var(--color-text-secondary); margin: 0 0 1rem;"><?php _e('password_forgot_desc'); ?></p>
                        <ol style="color: var(--color-text-secondary); margin: 0; padding-left: 1.25rem;">
                            <li><?php _e('password_forgot_step1'); ?></li>
                            <li><?php _e('password_forgot_step2'); ?></li>
                            <li><?php _e('password_forgot_step3'); ?></li>
                        </ol>
                        <p style="color: var(--color-text-secondary); margin: 1rem 0 0; font-size: 0.875rem;">
                            <strong>💡 <?php _e('password_tip'); ?></strong>
                        </p>
                    </div>
                </section>
                
                <!-- Section 5: Clipboard Tips -->
                <section class="howto-section" id="section-clipboard">
                    <h2 style="color: var(--color-text-primary);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-accent);">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                        </svg>
                        <?php _e('clipboard_tips_title'); ?>
                    </h2>
                    
                    <div class="step-box" style="background: var(--color-bg-tertiary); border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem;">
                        <h3 style="margin: 0 0 0.75rem; color: var(--color-text-primary); font-size: 1rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.5rem; color: var(--color-accent);">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                <line x1="8" y1="21" x2="16" y2="21"/>
                                <line x1="12" y1="17" x2="12" y2="21"/>
                            </svg>
                            <?php _e('clipboard_windows_title'); ?>
                        </h3>
                        <ol style="color: var(--color-text-secondary); margin: 0; padding-left: 1.25rem; font-size: 0.875rem;">
                            <li><?php _e('clipboard_windows_step1'); ?></li>
                            <li><?php _e('clipboard_windows_step2'); ?></li>
                            <li><?php _e('clipboard_windows_step3'); ?></li>
                        </ol>
                    </div>
                    
                    <div class="step-box" style="background: var(--color-bg-tertiary); border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem;">
                        <h3 style="margin: 0 0 0.75rem; color: var(--color-text-primary); font-size: 1rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.5rem; color: var(--color-text-secondary);">
                                <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"/>
                            </svg>
                            <?php _e('clipboard_mac_title'); ?>
                        </h3>
                        <p style="color: var(--color-text-secondary); margin: 0; font-size: 0.875rem;">
                            <?php _e('clipboard_mac_desc'); ?>
                        </p>
                    </div>
                    
                    <div class="step-box" style="background: var(--color-accent-light); border: 1px solid var(--color-accent); border-radius: 8px; padding: 1.5rem;">
                        <h3 style="margin: 0 0 0.75rem; color: var(--color-accent); font-size: 1rem;">
                            🎁 <?php _e('clipboard_recommend_title'); ?>
                        </h3>
                        <p style="color: var(--color-text-secondary); margin: 0 0 0.75rem; font-size: 0.875rem;">
                            <?php _e('clipboard_recommend_desc'); ?>
                        </p>
                        <ul style="color: var(--color-text-secondary); margin: 0; padding-left: 1.25rem; font-size: 0.875rem;">
                            <li><a href="https://chigusa-web.com/clibor/" target="_blank" rel="noopener" style="color: var(--color-accent);">Clibor (Windows)</a></li>
                            <li><a href="https://chigusa-web.com/clibor-for-mac/" target="_blank" rel="noopener" style="color: var(--color-accent);">Clibor for Mac</a></li>
                        </ul>
                    </div>
                </section>
                
                <!-- Section 6: Update Guide -->
                <section class="howto-section" id="section-update">
                    <h2 style="color: var(--color-text-primary);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #10b981;">
                            <polyline points="23 4 23 10 17 10"></polyline>
                            <polyline points="1 20 1 14 7 14"></polyline>
                            <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"></path>
                        </svg>
                        <?php _e('update_guide_title'); ?>
                    </h2>
                    
                    <div class="step-box" style="background: var(--color-error-bg); border: 1px solid var(--color-error); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;">
                        <h3 style="margin: 0 0 0.75rem; color: var(--color-error); font-size: 1rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 0.5rem;">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php _e('update_warning_title'); ?>
                        </h3>
                        <p style="color: var(--color-text-secondary); margin: 0 0 1rem; font-weight: 600;">
                            <?php _e('update_warning_desc'); ?>
                        </p>
                        <ul style="color: var(--color-text-secondary); margin: 0; padding-left: 1.25rem;">
                            <li><code style="background: var(--color-bg-tertiary); padding: 0.125rem 0.375rem; border-radius: 4px;">storage/</code> - <?php _e('update_storage_desc'); ?></li>
                            <li><code style="background: var(--color-bg-tertiary); padding: 0.125rem 0.375rem; border-radius: 4px;">database/shares.db</code> - <?php _e('update_database_desc'); ?></li>
                        </ul>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div class="step-box" style="background: var(--color-bg-tertiary); border-color: var(--color-border);">
                            <h3 style="color: var(--color-text-primary);"><?php _e('update_step1'); ?></h3>
                            <ol style="color: var(--color-text-secondary);">
                                <li><?php _e('update_step1_1'); ?></li>
                                <li><?php _e('update_step1_2'); ?></li>
                            </ol>
                        </div>
                        
                        <div class="step-box" style="background: var(--color-bg-tertiary); border-color: var(--color-border);">
                            <h3 style="color: var(--color-text-primary);"><?php _e('update_step2'); ?></h3>
                            <ol style="color: var(--color-text-secondary);">
                                <li><?php _e('update_step2_1'); ?></li>
                                <li><?php _e('update_step2_2'); ?></li>
                                <li><?php _e('update_step2_3'); ?></li>
                            </ol>
                        </div>
                        
                        <div class="step-box" style="background: var(--color-success-bg); border-color: var(--color-success);">
                            <h3 style="color: var(--color-success);"><?php _e('update_step3'); ?></h3>
                            <ol style="color: var(--color-text-secondary);">
                                <li><?php _e('update_step3_1'); ?></li>
                                <li><?php _e('update_step3_2'); ?></li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="step-box" style="background: var(--color-accent-light); border: 1px solid var(--color-accent); border-radius: 8px; padding: 1.5rem; margin-top: 1.5rem;">
                        <h3 style="margin: 0 0 0.75rem; color: var(--color-accent); font-size: 1rem;">
                            💡 <?php _e('update_tip_title'); ?>
                        </h3>
                        <p style="color: var(--color-text-secondary); margin: 0; font-size: 0.875rem;">
                            <?php _e('update_tip_desc'); ?>
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </main>
    
    <script>
    // Toggle Table of Contents
    function toggleToc() {
        const toc = document.getElementById('toc');
        toc.classList.toggle('collapsed');
        localStorage.setItem('howto_toc_collapsed', toc.classList.contains('collapsed'));
    }
    
    // Auto-generate Table of Contents from section h2 elements
    document.addEventListener('DOMContentLoaded', function() {
        const tocList = document.getElementById('toc-list');
        const sections = document.querySelectorAll('.howto-section[id]');
        
        let counter = 1;
        sections.forEach(section => {
            const h2 = section.querySelector('h2');
            if (h2) {
                const svg = h2.querySelector('svg');
                const text = h2.textContent.trim();
                const id = section.id;
                
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = '#' + id;
                
                // Add number
                const num = document.createElement('span');
                num.className = 'toc-number';
                num.textContent = counter++;
                a.appendChild(num);
                
                // Add text
                const span = document.createElement('span');
                span.textContent = text;
                a.appendChild(span);
                
                // Smooth scroll
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
                });
                
                li.appendChild(a);
                tocList.appendChild(li);
            }
        });
        
        // Restore TOC state
        if (localStorage.getItem('howto_toc_collapsed') === 'true') {
            document.getElementById('toc').classList.add('collapsed');
        }
    });
    </script>

<?php
// Include simple footer
include __DIR__ . '/partials/footer-simple.php';
?>
