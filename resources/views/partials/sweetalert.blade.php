<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sembunyikan alert statis flash message dari session agar tidak muncul ganda (double notif) dengan SweetAlert
        const flashAlerts = document.querySelectorAll('.alert-dismissible, .alert-success');
        flashAlerts.forEach(alert => alert.style.display = 'none');

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: {!! json_encode(session('success')) !!},
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal / Kesalahan!',
                text: {!! json_encode(session('error')) !!},
                confirmButtonColor: '#d33',
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'rounded-pill px-4'
                }
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: {!! json_encode(session('warning')) !!},
                confirmButtonColor: '#f8bb86',
                confirmButtonText: 'Mengerti',
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'rounded-pill px-4'
                }
            });
        @endif

        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: {!! json_encode(session('info')) !!},
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'rounded-pill px-4'
                }
            });
        @endif

        @if($errors->any())
            @php
                $errorHtml = '<ul class="text-start mb-0">' . implode('', array_map(function($err) { return '<li>' . e($err) . '</li>'; }, $errors->all())) . '</ul>';
            @endphp
            Swal.fire({
                icon: 'error',
                title: 'Input Tidak Valid',
                html: {!! json_encode($errorHtml) !!},
                confirmButtonColor: '#d33',
                confirmButtonText: 'Perbaiki',
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'rounded-pill px-4'
                }
            });
            // Sembunyikan alert error statis form agar tidak double
            document.querySelectorAll('.alert-danger.alert-dismissible, .alert-danger:not(.alert-permanent)').forEach(function(el) {
                if (el.querySelector('ul') || el.classList.contains('alert-dismissible')) {
                    el.style.display = 'none';
                }
            });
        @endif

        // Fungsi konversi tombol hapus / konfirmasi native confirm() menjadi dialog SweetAlert2
        function bindSweetAlertConfirm() {
            // 1. Tangkap tombol/elemen dengan onclick confirm (termasuk tombol yang di-render AJAX/DataTables)
            const confirmClickElements = document.querySelectorAll('[onclick*="confirm("]:not([data-swal-bound])');
            confirmClickElements.forEach(function(el) {
                el.setAttribute('data-swal-bound', 'true');
                const attr = el.getAttribute('onclick');
                const match = attr ? attr.match(/confirm\(\s*['"]([^'"]+)['"]\s*\)/) : null;
                const message = match ? match[1] : 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                
                el.removeAttribute('onclick');
                
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Tindakan',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Ya, Lanjutkan!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rounded-4 shadow-lg',
                            confirmButton: 'rounded-pill px-4',
                            cancelButton: 'rounded-pill px-4'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (el.tagName.toLowerCase() === 'button' || el.tagName.toLowerCase() === 'input') {
                                const form = el.closest('form');
                                if (form) {
                                    form.submit();
                                    return;
                                }
                            }
                            if (el.tagName.toLowerCase() === 'a' && el.getAttribute('href') && el.getAttribute('href') !== '#') {
                                window.location.href = el.getAttribute('href');
                            }
                        }
                    });
                });
            });

            // 2. Tangkap form dengan onsubmit confirm
            const confirmSubmitElements = document.querySelectorAll('form[onsubmit*="confirm("]:not([data-swal-bound])');
            confirmSubmitElements.forEach(function(form) {
                form.setAttribute('data-swal-bound', 'true');
                const attr = form.getAttribute('onsubmit');
                const match = attr ? attr.match(/confirm\(\s*['"]([^'"]+)['"]\s*\)/) : null;
                const message = match ? match[1] : 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                
                form.removeAttribute('onsubmit');
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Tindakan',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Ya, Lanjutkan!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rounded-4 shadow-lg',
                            confirmButton: 'rounded-pill px-4',
                            cancelButton: 'rounded-pill px-4'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        }

        // Jalankan saat load awal
        bindSweetAlertConfirm();

        // Gunakan MutationObserver agar elemen tombol yang dimuat belakangan via AJAX/DataTables otomatis dipasangi SweetAlert
        const observer = new MutationObserver(function(mutations) {
            let shouldBind = false;
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    shouldBind = true;
                }
            });
            if (shouldBind) {
                bindSweetAlertConfirm();
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });
</script>
