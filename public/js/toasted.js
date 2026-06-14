// Toast Helper Class
class Toast {
    static show(message, type = 'info', title = null, duration = 4000) {
        window.dispatchEvent(new CustomEvent('toast-show', {
            detail: {
                message: message,
                type: type,
                title: title,
                duration: duration
            }
        }));
    }

    static success(message, title = 'Success') {
        this.show(message, 'success', title);
    }

    static error(message, title = 'Error') {
        this.show(message, 'error', title);
    }

    static info(message, title = 'Info') {
        this.show(message, 'info', title);
    }

    static warning(message, title = 'Warning') {
        this.show(message, 'warning', title);
    }
}

// Make Toast available globally
window.Toast = Toast;