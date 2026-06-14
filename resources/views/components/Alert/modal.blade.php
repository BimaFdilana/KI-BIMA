@php
    $iconClass =
        [
            'success' => 'fa-check-circle text-green-600',
            'error' => 'fa-times-circle text-red-600',
            'pending' => 'fa-spinner fa-spin text-blue-600',
        ][$type] ?? 'fa-check-circle text-green-600';

    $bgColor =
        [
            'success' => 'bg-green-100',
            'error' => 'bg-red-100',
            'pending' => 'bg-blue-100',
        ][$type] ?? 'bg-green-100';

    $progressBgColor =
        [
            'success' => 'bg-green-200',
            'error' => 'bg-red-200',
            'pending' => 'bg-blue-200',
        ][$type] ?? 'bg-green-200';

    $progressBarColor =
        [
            'success' => 'bg-green-500',
            'error' => 'bg-red-500',
            'pending' => 'bg-blue-500',
        ][$type] ?? 'bg-green-500';

    $primaryBtnColor =
        [
            'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
            'error' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
            'pending' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        ][$type] ?? 'bg-green-600 hover:bg-green-700 focus:ring-green-500';
@endphp

<div id="{{ $modalId }}" class="fixed inset-0 z-50 flex hidden items-center justify-center">
    <div class="modal-overlay absolute inset-0 bg-black bg-opacity-50"></div>

    <div class="modal-content relative mx-4 w-full max-w-md transform overflow-hidden rounded-xl bg-white shadow-2xl transition-all">
        @if ($autoClose)
            <div class="{{ $progressBgColor }} absolute left-0 right-0 top-0 h-1">
                <div class="progress-bar {{ $progressBarColor }} h-full"></div>
            </div>
        @endif

        <div class="p-6 text-center">
            <div class="modal-icon {{ $bgColor }} mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full">
                <i class="fas {{ $iconClass }} text-5xl"></i>
            </div>

            <h3 class="mb-2 text-2xl font-bold text-gray-800">{{ $title }}</h3>
            <p class="mb-6 text-gray-600">{{ $message }}</p>

            <div class="flex justify-center gap-3">
                @if ($secondaryButtonText)
                    <button data-modal-close="{{ $modalId }}" class="rounded-lg bg-gray-200 px-6 py-2 text-gray-800 transition-colors hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                        {{ $secondaryButtonText }}
                    </button>
                @endif

                <button data-modal-primary="{{ $modalId }}" class="{{ $primaryBtnColor }} rounded-lg px-6 py-2 text-white transition-colors focus:outline-none focus:ring-2 focus:ring-opacity-50">
                    {{ $primaryButtonText }}
                </button>
            </div>
        </div>
    </div>
</div>

@once
    <style>
        /* Custom animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateY(0);
                opacity: 1;
            }

            to {
                transform: translateY(20px);
                opacity: 0;
            }
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-20px);
            }

            60% {
                transform: translateY(-10px);
            }
        }

        .modal-overlay {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .modal-overlay.hidden {
            animation: fadeOut 0.3s ease-out forwards;
        }

        .modal-content {
            animation: slideIn 0.3s ease-out forwards;
        }

        .modal-content.hidden {
            animation: slideOut 0.3s ease-out forwards;
        }

        .modal-icon {
            animation: bounce 0.8s ease-out;
        }

        .progress-bar {
            animation: progress 3s linear forwards;
        }

        @keyframes progress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal handling logic
            document.querySelectorAll('[data-modal-close]').forEach(button => {
                const modalId = button.getAttribute('data-modal-close');
                button.addEventListener('click', function() {
                    closeModal(document.getElementById(modalId));
                });
            });

            document.querySelectorAll('[data-modal-primary]').forEach(button => {
                const modalId = button.getAttribute('data-modal-primary');
                button.addEventListener('click', function() {
                    const modalEvent = new CustomEvent('modal.action.primary', {
                        detail: {
                            modalId: modalId
                        }
                    });
                    document.dispatchEvent(modalEvent);
                    closeModal(document.getElementById(modalId));
                });
            });

            // Close modal function with animation
            function closeModal(modal) {
                if (!modal) return;

                const overlay = modal.querySelector('.modal-overlay');
                const content = modal.querySelector('.modal-content');

                // Start closing animations
                overlay.classList.add('hidden');
                content.classList.add('hidden');

                // Remove modal from DOM after animation completes
                setTimeout(() => {
                    modal.classList.add('hidden');
                    // Reset animations for next open
                    overlay.classList.remove('hidden');
                    content.classList.remove('hidden');

                    const closeEvent = new CustomEvent('modal.closed', {
                        detail: {
                            modalId: modal.id
                        }
                    });
                    document.dispatchEvent(closeEvent);
                }, 300);
            }

            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                document.querySelectorAll('.fixed.inset-0.z-50:not(.hidden)').forEach(modal => {
                    if (event.target === modal) {
                        closeModal(modal);
                    }
                });
            });
        });

        // Function to show modal
        function showModal(modalId, autoCloseTime = null) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.classList.remove('hidden');

            // Auto close after specified time
            if (autoCloseTime) {
                setTimeout(() => {
                    closeModal(modal);
                }, autoCloseTime);
            }

            // Dispatch event
            const openEvent = new CustomEvent('modal.opened', {
                detail: {
                    modalId: modalId
                }
            });
            document.dispatchEvent(openEvent);
        }

        // Function to close modal
        function closeModal(modal) {
            if (typeof modal === 'string') {
                modal = document.getElementById(modal);
            }

            if (!modal) return;

            const overlay = modal.querySelector('.modal-overlay');
            const content = modal.querySelector('.modal-content');

            // Start closing animations
            overlay.classList.add('hidden');
            content.classList.add('hidden');

            // Remove modal from DOM after animation completes
            setTimeout(() => {
                modal.classList.add('hidden');
                // Reset animations for next open
                overlay.classList.remove('hidden');
                content.classList.remove('hidden');

                const closeEvent = new CustomEvent('modal.closed', {
                    detail: {
                        modalId: modal.id
                    }
                });
                document.dispatchEvent(closeEvent);
            }, 300);
        }
    </script>
@endonce
