// PWA Installation and Service Worker Registration
(function() {
    'use strict';

    // Check if service workers are supported
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            registerServiceWorker();
        });
    }

    // Register service worker
    async function registerServiceWorker() {
        try {
            const registration = await navigator.serviceWorker.register('/service-worker.js', {
                scope: '/'
            });

            console.log('[PWA] Service Worker registered successfully:', registration.scope);

            // Check for updates
            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;
                console.log('[PWA] New Service Worker found, installing...');

                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        // New service worker available, show update notification
                        showUpdateNotification();
                    }
                });
            });
        } catch (error) {
            console.error('[PWA] Service Worker registration failed:', error);
        }
    }

    // Show update notification
    function showUpdateNotification() {
        const updateBanner = document.createElement('div');
        updateBanner.className = 'pwa-update-banner';
        updateBanner.innerHTML = `
            <div class="pwa-update-content">
                <span>تحديث جديد متوفر! New update available!</span>
                <button onclick="window.location.reload()" class="btn btn-sm btn-light">
                    تحديث Update
                </button>
            </div>
        `;
        document.body.appendChild(updateBanner);
    }

    // Install prompt handling
    let deferredPrompt;

    window.addEventListener('beforeinstallprompt', (e) => {
        console.log('[PWA] Install prompt ready');
        e.preventDefault();
        deferredPrompt = e;

        // Show install button
        showInstallButton();
    });

    function showInstallButton() {
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'block';
            installBtn.addEventListener('click', installPWA);
        }
    }

    async function installPWA() {
        if (!deferredPrompt) {
            return;
        }

        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;

        console.log('[PWA] Install outcome:', outcome);

        if (outcome === 'accepted') {
            console.log('[PWA] User accepted installation');
        } else {
            console.log('[PWA] User dismissed installation');
        }

        deferredPrompt = null;

        // Hide install button
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'none';
        }
    }

    // Detect if app is installed
    window.addEventListener('appinstalled', () => {
        console.log('[PWA] App installed successfully');
        deferredPrompt = null;
    });

    // Check if running as PWA
    function isPWA() {
        return window.matchMedia('(display-mode: standalone)').matches ||
               window.navigator.standalone === true;
    }

    if (isPWA()) {
        console.log('[PWA] Running as installed app');
        document.body.classList.add('pwa-mode');
    }

    // Add CSS for update banner
    const style = document.createElement('style');
    style.textContent = `
        .pwa-update-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            z-index: 10000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            animation: slideDown 0.3s ease-out;
        }

        .pwa-update-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .pwa-update-content span {
            font-weight: 600;
            font-size: 14px;
        }

        .pwa-update-content button {
            margin-left: 15px;
            font-weight: 600;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
            }
            to {
                transform: translateY(0);
            }
        }

        #pwa-install-btn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .pwa-mode {
            /* Styles when running as PWA */
        }
    `;
    document.head.appendChild(style);

    // Expose PWA functions globally
    window.PWA = {
        isPWA,
        installPWA
    };
})();
