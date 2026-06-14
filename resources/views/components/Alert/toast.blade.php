@props([
    'position' => 'top-right', // top-right, top-left, bottom-right, bottom-left, center, etc.
    'duration' => 5000,
    'maxToasts' => 5,
])

@php
    $positionClasses = [
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'center' => 'top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2',
        'center-right' => 'top-1/2 right-4 transform -translate-y-1/2',
        'center-left' => 'top-1/2 left-4 transform -translate-y-1/2',
        'center-bottom' => 'bottom-4 left-1/2 transform -translate-x-1/2',
        'center-top' => 'top-4 left-1/2 transform -translate-x-1/2',
    ];
@endphp

{{-- Container untuk semua toast --}}
<div id="toast-container" class="{{ $positionClasses[$position] ?? $positionClasses['top-right'] }} fixed z-[9999] w-full max-w-md space-y-3" data-duration="{{ $duration }}" data-max-toasts="{{ $maxToasts }}" data-position="{{ $position }}">
</div>

<style>
    /* Base style untuk setiap toast */
    .toast-element {
        /* Efek Glassmorphism */
        background-color: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);

        border-radius: 0.75rem;
        /* rounded-xl */
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        transition: transform 0.2s ease-in-out, opacity 0.2s ease-in-out;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    /* Hover effect */
    .toast-element:hover {
        transform: scale(1.03);
    }

    /* Dark mode styles */
    .dark .toast-element {
        background-color: rgba(30, 41, 59, 0.8);
        /* dark:bg-slate-800/80 */
        border: 1px solid rgb(51 65 85);
        /* dark:border-slate-700 */
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.2), 0 4px 6px -4px rgb(0 0 0 / 0.2);
    }

    /* Animasi Progress Bar */
    @keyframes progress {
        0% {
            width: 100%;
        }

        100% {
            width: 0%;
        }
    }

    .progress-bar {
        animation: progress var(--duration) linear forwards;
    }

    .toast-element:hover .progress-bar {
        animation-play-state: paused;
    }

    /* --- Keyframes Animasi --- */
    /* Menggunakan kurva cubic-bezier yang lebih modern untuk feel yang lebih halus */
    .toast-slide-in-right {
        animation: slideInRight 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .toast-slide-out-right {
        animation: slideOutRight 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .toast-slide-in-left {
        animation: slideInLeft 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .toast-slide-out-left {
        animation: slideOutLeft 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes slideInLeft {
        from {
            transform: translateX(-100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutLeft {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(-100%);
            opacity: 0;
        }
    }

    .toast-slide-in-down {
        animation: slideInDown 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .toast-slide-out-up {
        animation: slideOutUp 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes slideInDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideOutUp {
        from {
            transform: translateY(0);
            opacity: 1;
        }

        to {
            transform: translateY(-100%);
            opacity: 0;
        }
    }

    .toast-slide-in-up {
        animation: slideInUp 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .toast-slide-out-down {
        animation: slideOutDown 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes slideInUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideOutDown {
        from {
            transform: translateY(0);
            opacity: 1;
        }

        to {
            transform: translateY(100%);
            opacity: 0;
        }
    }

    .toast-fade-in-scale {
        animation: fadeInScale 0.3s ease-out forwards;
    }

    .toast-fade-out-scale {
        animation: fadeOutScale 0.3s ease-in forwards;
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes fadeOutScale {
        from {
            opacity: 1;
            transform: scale(1);
        }

        to {
            opacity: 0;
            transform: scale(0.9);
        }
    }
</style>

<script>
    class ToastManager {
        constructor() {
            this.container = document.getElementById('toast-container');
            if (!this.container) return;

            this.duration = parseInt(this.container.dataset.duration, 10) || 5000;
            this.maxToasts = parseInt(this.container.dataset.maxToasts, 10) || 5;
            this.position = this.container.dataset.position || 'top-right';
            this.toasts = [];

            this.checkFlashMessages();
            this.setupEventListeners();
        }

        checkFlashMessages() {
            @if (session()->has('success'))
                this.show('success', {!! json_encode(session('success')) !!}, 'Success');
            @endif
            @if (session()->has('error'))
                this.show('error', {!! json_encode(session('error')) !!}, 'Error');
            @endif
            @if (session()->has('warning'))
                this.show('warning', {!! json_encode(session('warning')) !!}, 'Warning');
            @endif
            @if (session()->has('info'))
                this.show('info', {!! json_encode(session('info')) !!}, 'Information');
            @endif
        }

        setupEventListeners() {
            window.addEventListener('show-toast', e => this.show(e.detail.type, e.detail.message, e.detail.title));

            if (typeof Livewire !== 'undefined') {
                document.addEventListener('livewire:init', () => {
                    Livewire.on('show-toast', data => {
                        const toastData = Array.isArray(data) ? data[0] : data;
                        this.show(toastData.type, toastData.message, toastData.title);
                    });
                });
            }
        }

        show(type, message, title = null) {
            if (!this.container) return;
            if (this.toasts.length >= this.maxToasts) {
                this.remove(this.toasts[0]);
            }

            const toast = this.createToast(type, message, title);
            this.container.appendChild(toast);
            this.toasts.push(toast);

            toast.autoRemove = setTimeout(() => this.remove(toast), this.duration);
            this.setupHoverEvents(toast);
        }

        createToast(type, message, title) {
            const toast = document.createElement('div');
            const toastId = 'toast-' + Date.now();
            toast.id = toastId;

            const config = this.getToastConfig(type);
            const [enterAnimation, exitAnimation] = this.getAnimationClasses();
            toast.dataset.exitAnimation = exitAnimation;

            // Menerapkan class base dan animasi masuk
            toast.className = `toast-element w-full flex items-start p-4 gap-4 overflow-hidden ${enterAnimation}`;

            const safeMessage = this.escapeHtml(message);
            const safeTitle = title ? this.escapeHtml(title) : null;

            // Struktur HTML baru untuk ikon dengan background lingkaran
            toast.innerHTML = `
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center ${config.iconBgColor} text-white">
                    <i class="${config.icon} text-base"></i>
                </div>
                <div class="flex-1">
                    ${safeTitle ? `<h3 class="font-semibold text-sm ${config.titleColor}">${safeTitle}</h3>` : ''}
                    <p class="text-sm ${config.textColor} mt-1">${safeMessage}</p>
                </div>
                <button class="flex-shrink-0 text-lg p-1 -m-1 ${config.closeColor} hover:opacity-70 transition-opacity" 
                        onclick="window.toastManager.remove(this.closest('.toast-element'))">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div class="progress-bar absolute bottom-0 left-0 h-1 ${config.iconBgColor}" style="--duration: ${this.duration}ms;"></div>
            `;
            return toast;
        }

        // Palet warna modern
        getToastConfig(type) {
            const configs = {
                success: {
                    titleColor: 'text-slate-800 ',
                    textColor: 'text-slate-600 ',
                    closeColor: 'text-slate-400 ',
                    iconBgColor: 'bg-emerald-500',
                    icon: 'fa-solid fa-check'
                },
                error: {
                    titleColor: 'text-slate-800 ',
                    textColor: 'text-slate-600 ',
                    closeColor: 'text-slate-400 ',
                    iconBgColor: 'bg-rose-500',
                    icon: 'fa-solid fa-xmark'
                },
                warning: {
                    titleColor: 'text-slate-800 ',
                    textColor: 'text-slate-600 ',
                    closeColor: 'text-slate-400 ',
                    iconBgColor: 'bg-amber-500',
                    icon: 'fa-solid fa-exclamation'
                },
                info: {
                    titleColor: 'text-slate-800 ',
                    textColor: 'text-slate-600 ',
                    closeColor: 'text-slate-400 ',
                    iconBgColor: 'bg-sky-500',
                    icon: 'fa-solid fa-info'
                }
            };
            return configs[type] || configs.info;
        }

        getAnimationClasses() {
            const pos = this.position;
            if (pos.includes('right')) return ['toast-slide-in-right', 'toast-slide-out-right'];
            if (pos.includes('left')) return ['toast-slide-in-left', 'toast-slide-out-left'];
            if (pos.includes('bottom')) return ['toast-slide-in-up', 'toast-slide-out-down'];
            if (pos.includes('top')) return ['toast-slide-in-down', 'toast-slide-out-up'];
            return ['toast-fade-in-scale', 'toast-fade-out-scale']; // Default untuk 'center'
        }

        setupHoverEvents(toast) {
            toast.addEventListener('mouseenter', () => clearTimeout(toast.autoRemove));
            toast.addEventListener('mouseleave', () => {
                const remainingTime = this.getRemainingTime(toast.querySelector('.progress-bar'));
                toast.autoRemove = setTimeout(() => this.remove(toast), remainingTime);
            });
        }

        getRemainingTime(progressBar) {
            if (!progressBar) return 0;
            const computedStyle = window.getComputedStyle(progressBar);
            const width = parseFloat(computedStyle.width);
            const parentWidth = progressBar.parentElement.offsetWidth;
            return (width / parentWidth) * this.duration;
        }

        remove(toast) {
            if (!toast || !toast.parentElement) return;
            clearTimeout(toast.autoRemove);

            const exitClass = toast.dataset.exitAnimation;
            toast.classList.add(exitClass);

            const onAnimationEnd = () => {
                toast.remove();
                this.toasts = this.toasts.filter(t => t !== toast);
                toast.removeEventListener('animationend', onAnimationEnd);
            };
            toast.addEventListener('animationend', onAnimationEnd);

            // Fallback jika event animationend tidak terpicu
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                    this.toasts = this.toasts.filter(t => t !== toast);
                }
            }, 500);
        }

        escapeHtml(text) {
            return text.toString()
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    }

    // Inisialisasi Toast Manager
    window.showToast = (type, message, title) => window.toastManager?.show(type, message, title);
    document.addEventListener('DOMContentLoaded', () => window.toastManager = new ToastManager());
</script>
