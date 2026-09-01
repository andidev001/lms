@extends('layouts.admin')

@section('title', 'Data Siswa')
@section('subtitle', 'Kelola informasi dan akun siswa terdaftar')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="fw-bold mb-0">Daftar Siswa</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-danger rounded-pill px-4 d-none" id="btn-bulk-delete">
                <i class="bi bi-trash me-1"></i> Hapus Terpilih
            </button>
            <form action="{{ route('siswas.generate-all') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning rounded-pill px-4" onclick="return confirm('Sistem akan membuatkan akun untuk semua siswa yang belum memiliki akun. Lanjutkan?')">
                    <i class="bi bi-magic me-1"></i> Generate Semua Akun
                </button>
            </form>
            <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#importSiswaModal">
                <i class="bi bi-file-earmark-excel me-1"></i> Impor Excel
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addSiswaModal">
                <i class="bi bi-person-plus me-1"></i> Tambah Data Siswa
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
        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($message = Session::get('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
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

        <div class="table-responsive">
            <table class="table table-hover table-borderless align-middle" id="siswas-table">
                <thead class="table-light rounded">
                    <tr>
                        <th class="text-muted fw-normal py-3 rounded-start" width="40px">
                            <input type="checkbox" id="check-all" class="form-check-input">
                        </th>
                        <th class="text-muted fw-normal py-3">NIS</th>
                        <th class="text-muted fw-normal py-3">Nama Lengkap</th>
                        <th class="text-muted fw-normal py-3">L/P</th>
                        <th class="text-muted fw-normal py-3">Kelas</th>
                        <th class="text-muted fw-normal py-3">Status Akun</th>
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
    var table = $('#siswas-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('siswas.index') }}',
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'nis', name: 'nis' },
            { data: 'nama_siswa', name: 'nama' },
            { data: 'jk', name: 'jenis_kelamin' },
            { data: 'nama_kelas', name: 'kelas.nama_kelas' },
            { data: 'status_akun', name: 'user_id', orderable: false, searchable: false },
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
    $('#siswas-table').on('change', '.item-checkbox', function() {
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
                text: 'Apakah Anda yakin ingin menghapus ' + ids.length + ' data siswa terpilih?',
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
                        url: '{{ route('siswas.bulk-destroy') }}',
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

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="addSiswaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
        <h5 class="modal-title fw-bold">Tambah Data Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('siswas.store') }}">
          @csrf
          <div class="modal-body p-4">
              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Nomor Induk Siswa (NIS)</label>
                  <input type="text" name="nis" class="form-control form-control-lg rounded-3" placeholder="Masukkan NIS" value="{{ old('nis') }}" required>
              </div>
              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Nama Lengkap</label>
                  <input type="text" name="nama" class="form-control form-control-lg rounded-3" placeholder="Masukkan nama lengkap" value="{{ old('nama') }}" required>
              </div>
              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Jenis Kelamin</label>
                  <select name="jenis_kelamin" class="form-select form-select-lg rounded-3" required>
                      <option value="">-- Pilih Jenis Kelamin --</option>
                      <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                      <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                  </select>
              </div>
              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Kelas (Opsional)</label>
                  <select name="kelas_id" class="form-select form-select-lg rounded-3">
                      <option value="">-- Pilih Kelas --</option>
                      @foreach($kelases as $kelas)
                          <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                      @endforeach
                  </select>
              </div>
          </div>
          <div class="modal-footer border-top-0 pb-4 px-4">
              <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary rounded-3 px-5">Simpan Data</button>
          </div>
      </form>
    </div>
  </div>
</div>

@if (count($errors) > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('addSiswaModal'), {
            keyboard: false
        });
        myModal.show();
    });
</script>
@endif

<!-- Modal Import Siswa -->
<div class="modal fade" id="importSiswaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
        <h5 class="modal-title fw-bold">Impor Data Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('siswas.import') }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-body p-4">
              <div class="alert alert-info rounded-3">
                  <i class="bi bi-info-circle me-1"></i> Silakan unduh template Excel terlebih dahulu sebelum mengunggah data. 
                  <a href="{{ route('siswas.template') }}" class="fw-bold alert-link">Unduh Template</a>
              </div>
              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">File Excel (.csv, .xls, .xlsx)</label>
                  <input type="file" name="file_excel" class="form-control form-control-lg rounded-3" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
              </div>
          </div>
          <div class="modal-footer border-top-0 pb-4 px-4">
              <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-success rounded-3 px-5"><i class="bi bi-upload me-1"></i> Upload & Impor</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection
