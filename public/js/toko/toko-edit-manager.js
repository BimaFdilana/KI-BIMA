// Global Variables
const tokoId = document.currentScript?.dataset.tokoId || window.tokoId;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || window.csrfToken;

/**
 * Employee Manager - Alpine.js Data Object
 * Manages employee list and CRUD operations
 */
function employeeManager() {
    return {
        showAddForm: false,
        isLoading: false,
        employees: window.employeesData || [],
        jabatans: window.jabatansData || [],
        newEmployee: {
            userId: '',
            jabatanId: ''
        },

        /**
         * Add new employee
         */
        addEmployee() {
            if (!this.newEmployee.userId || !this.newEmployee.jabatanId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih karyawan dan jabatan!',
                    confirmButtonColor: '#dc2626'
                });
                return;
            }

            this.isLoading = true;
            const self = this;

            $.ajax({
                url: `/toko/${tokoId}/employees`,
                method: 'POST',
                data: {
                    _token: csrfToken,
                    user_id: self.newEmployee.userId,
                    jabatan_id: self.newEmployee.jabatanId
                },
                success: function(response) {
                    self.isLoading = false;
                    
                    if (response.success) {
                        // Add to employees array
                        self.employees.push({
                            id: response.user.id,
                            name: response.user.name,
                            email: response.user.email,
                            thumbnail: response.user.thumbnail,
                            jabatan_id: parseInt(self.newEmployee.jabatanId)
                        });

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                            confirmButtonColor: '#dc2626'
                        });

                        // Reset form
                        self.newEmployee.userId = '';
                        self.newEmployee.jabatanId = '';
                        self.showAddForm = false;
                    }
                },
                error: function(xhr) {
                    self.isLoading = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                        confirmButtonColor: '#dc2626'
                    });
                }
            });
        },

        /**
         * Remove employee
         */
        removeEmployee(id, name) {
            const self = this;
            Swal.fire({
                title: 'Hapus Karyawan?',
                text: `Apakah Anda yakin ingin menghapus ${name}? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: `/toko/${tokoId}/employees/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                self.employees = self.employees.filter(emp => emp.id !== id);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false,
                                    confirmButtonColor: '#dc2626'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    });
                }
            });
        },

        /**
         * Update employee position/jabatan
         */
        updateEmployeeJabatan(id, jabatanId) {
            if (!jabatanId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih jabatan!',
                    confirmButtonColor: '#dc2626'
                });
                return;
            }

            const self = this;
            $.ajax({
                url: `/toko/${tokoId}/employees/${id}/jabatan`,
                method: 'PATCH',
                data: {
                    _token: csrfToken,
                    jabatan_id: jabatanId
                },
                success: function(response) {
                    if (response.success) {
                        const employee = self.employees.find(emp => emp.id === id);
                        if (employee) {
                            employee.jabatan_id = parseInt(jabatanId);
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                            confirmButtonColor: '#dc2626'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                        confirmButtonColor: '#dc2626'
                    });
                }
            });
        }
    }
}

/**
 * Delete product from store
 */
function deleteProduct(id, name) {
    Swal.fire({
        title: 'Hapus Barang?',
        text: `Apakah Anda yakin ingin menghapus "${name}"? Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `/toko/${tokoId}/products/${id}`,
                method: 'DELETE',
                data: {
                    _token: csrfToken
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                            confirmButtonColor: '#dc2626'
                        });
                        location.reload();
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                        confirmButtonColor: '#dc2626'
                    });
                }
            });
        }
    });
}

/**
 * Handle toko info form submission
 */
$(document).ready(function() {
    // Initialize HS components
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }

    // Handle form submission
    $('#tokoInfoForm').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Menyimpan Perubahan...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: `/toko/${tokoId}`,
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonColor: '#dc2626',
                        timer: 2000
                    }).then(() => window.location.reload());
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                    confirmButtonColor: '#dc2626'
                });
            }
        });
    });
});