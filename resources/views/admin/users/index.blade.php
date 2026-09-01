@extends('layouts.admin')

@section('title', 'Kelola User')
@section('subtitle', 'Daftar pengguna terdaftar di sistem')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="fw-bold mb-0">Daftar Pengguna</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-danger rounded-pill px-4 d-none" id="btn-bulk-delete">
                <i class="bi bi-trash me-1"></i> Hapus Terpilih
            </button>
            <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#downloadAccountModal">
                <i class="bi bi-file-earmark-excel me-1"></i> Download Akun (Excel)
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus me-1"></i> Tambah User Baru
            </button>
        </div>
    </div>
    <div class="card-body p-4">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover table-borderless align-middle" id="users-table">
                <thead class="table-light rounded">
                    <tr>
                        <th class="text-muted fw-normal py-3 rounded-start" width="40px">
                            <input type="checkbox" id="check-all" class="form-check-input">
                        </th>
                        <th class="text-muted fw-normal py-3">No</th>
                        <th class="text-muted fw-normal py-3">Nama Lengkap</th>
                        <th class="text-muted fw-normal py-3">Email</th>
                        <th class="text-muted fw-normal py-3">Role</th>
                        <th class="text-muted fw-normal py-3 rounded-end text-end" width="200px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('users.index') }}',
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'user_name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'roles_badge', name: 'roles.name', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        }
    });

    // Handle check all
    $('#check-all').on('click', function() {
        $('.item-checkbox').prop('checked', this.checked);
        toggleBulkDeleteButton();
    });

    // Handle individual checkbox
    $('#users-table').on('change', '.item-checkbox', function() {
        if ($('.item-checkbox:checked').length == $('.item-checkbox').length) {
            $('#check-all').prop('checked', true);
        } else {
            $('#check-all').prop('checked', false);
        }
        toggleBulkDeleteButton();
    });

    function toggleBulkDeleteButton() {
        if ($('.item-checkbox:checked').length > 0) {
            $('#btn-bulk-delete').removeClass('d-none');
        } else {
            $('#btn-bulk-delete').addClass('d-none');
        }
    }

    // Handle bulk delete
    $('#btn-bulk-delete').on('click', function() {
        var ids = [];
        $('.item-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            Swal.fire({
                title: 'Konfirmasi Hapus Terpilih',
                text: 'Apakah Anda yakin ingin menghapus ' + ids.length + ' data pengguna terpilih?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus Terpilih!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'rounded-pill px-4',
                    cancelButton: 'rounded-pill px-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('users.bulk-destroy') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: ids
                        },
                        success: function(response) {
                            table.ajax.reload();
                            $('#check-all').prop('checked', false);
                            toggleBulkDeleteButton();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.success,
                                timer: 2500,
                                showConfirmButton: false,
                                customClass: { popup: 'rounded-4 shadow-lg' }
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menghapus data.',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'Tutup',
                                customClass: { popup: 'rounded-4 shadow-lg', confirmButton: 'rounded-pill px-4' }
                            });
                        }
                    });
                }
            });
        }
    });
});
</script>
@endpush

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
        <h5 class="modal-title fw-bold" id="addUserModalLabel">Tambah User Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('users.store') }}">
          @csrf
          <div class="modal-body p-4">
              @if (count($errors) > 0)
                  <div class="alert alert-danger rounded-3">
                      <strong>Whoops!</strong> Ada masalah dengan input Anda.<br><br>
                      <ul class="mb-0">
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                      </ul>
                  </div>
              @endif
              
              <div class="mb-3">
                  <label for="name" class="form-label fw-semibold text-muted">Nama Lengkap</label>
                  <input type="text" name="name" class="form-control form-control-lg rounded-3" id="name" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
              </div>

              <div class="mb-3">
                  <label for="email" class="form-label fw-semibold text-muted">Alamat Email</label>
                  <input type="email" name="email" class="form-control form-control-lg rounded-3" id="email" placeholder="contoh: budi@sekolah.com" value="{{ old('email') }}" required>
              </div>

              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label for="password" class="form-label fw-semibold text-muted">Password</label>
                      <input type="password" name="password" class="form-control form-control-lg rounded-3" id="password" placeholder="Minimal 8 karakter" required>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label for="confirm-password" class="form-label fw-semibold text-muted">Konfirmasi Password</label>
                      <input type="password" name="confirm-password" class="form-control form-control-lg rounded-3" id="confirm-password" placeholder="Ulangi password" required>
                  </div>
              </div>

              <div class="mb-2">
                  <label for="roles" class="form-label fw-semibold text-muted">Jabatan (Role)</label>
                  <select name="roles[]" id="roles" class="form-select form-select-lg rounded-3" required>
                      <option value="">-- Pilih Role Pengguna --</option>
                      @foreach($roles as $role)
                          <option value="{{ $role }}" {{ old('roles') && in_array($role, old('roles')) ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                      @endforeach
                  </select>
                  <small class="text-muted">Hak akses yang dimiliki oleh akun ini dalam aplikasi.</small>
              </div>
          </div>
          <div class="modal-footer border-top-0 pb-4 px-4">
              <button type="button" class="btn btn-light btn-lg rounded-3 px-4 border" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary btn-lg rounded-3 px-5">Simpan Pengguna</button>
          </div>
      </form>
    </div>
  </div>
</div>

@if (count($errors) > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('addUserModal'), {
            keyboard: false
        });
        myModal.show();
    });
</script>
@endif

<!-- Modal Download Akun Excel -->
<div class="modal fade" id="downloadAccountModal" tabindex="-1" aria-labelledby="downloadAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pt-4 pb-2 px-4">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="downloadAccountModalLabel"><i class="bi bi-file-earmark-excel text-success me-2"></i> Download Data Akun & Password (Excel)</h5>
                    <p class="text-muted small mb-0">Pilih daftar akun yang ingin Anda unduh untuk didistribusikan kepada Guru dan Siswa.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Opsi Download Akun Guru -->
                    <div class="col-md-6">
                        <div class="card h-100 border rounded-4 shadow-sm p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="bi bi-person-badge fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Data Akun Guru</h6>
                                        <span class="badge bg-success bg-opacity-10 text-success">Format Excel (.xlsx)</span>
                                    </div>
                                </div>
                                <p class="text-muted small mb-3">
                                    Berisi daftar Nama Guru, NIP, Email Login, dan Password default (<code>password</code>) yang siap dicetak atau dibagikan kepada dewan guru.
                                </p>
                            </div>
                            <a href="{{ route('users.export-guru') }}" class="btn btn-success rounded-pill w-100 py-2 fw-semibold shadow-sm mt-3" onclick="setTimeout(() => { $('#downloadAccountModal').modal('hide'); }, 1000);">
                                <i class="bi bi-download me-2"></i> Download Akun Guru
                            </a>
                        </div>
                    </div>

                    <!-- Opsi Download Akun Siswa -->
                    <div class="col-md-6">
                        <div class="card h-100 border rounded-4 shadow-sm p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="bi bi-people fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Data Akun Siswa</h6>
                                        <span class="badge bg-primary bg-opacity-10 text-primary">Format Excel (.xlsx)</span>
                                    </div>
                                </div>
                                <p class="text-muted small mb-3">
                                    Berisi daftar Nama Siswa, Kelas, Email Login, dan Password default (<code>NIS Siswa</code>). Bisa diunduh sekaligus untuk semua kelas atau per kelas.
                                </p>
                            </div>
                            <form action="{{ route('users.export-siswa') }}" method="GET" class="mt-2" onsubmit="setTimeout(() => { $('#downloadAccountModal').modal('hide'); }, 1000);">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-filter me-1"></i> Filter Kelas:</label>
                                    <select name="kelas_id" class="form-select rounded-3 small">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach(\App\Models\Kelas::orderBy('nama_kelas')->get() as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm">
                                    <i class="bi bi-download me-2"></i> Download Akun Siswa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection
