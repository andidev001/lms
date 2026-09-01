@extends('layouts.admin')

@section('title', 'Tahun Ajaran')
@section('subtitle', 'Kelola tahun ajaran dan semester aktif')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Daftar Tahun Ajaran</h5>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addTahunAjaranModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Tahun Ajaran
            </button>
        </div>
    </div>
    <div class="card-body p-4">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover table-borderless align-middle" id="tahun-ajaran-table">
                <thead class="table-light rounded">
                    <tr>
                        <th class="text-muted fw-normal py-3 rounded-start">No</th>
                        <th class="text-muted fw-normal py-3">Nama Tahun Ajaran</th>
                        <th class="text-muted fw-normal py-3">Semester</th>
                        <th class="text-muted fw-normal py-3">Status</th>
                        <th class="text-muted fw-normal py-3 rounded-end text-end" width="250px">Aksi</th>
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
    $('#tahun-ajaran-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('tahun-ajarans.index') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_tahun', name: 'nama_tahun' },
            { data: 'semester', name: 'semester' },
            { data: 'status', name: 'is_active', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        }
    });
});
</script>
@endpush

<!-- Modal Tambah Tahun Ajaran -->
<div class="modal fade" id="addTahunAjaranModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
        <h5 class="modal-title fw-bold">Tambah Tahun Ajaran Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('tahun-ajarans.store') }}">
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
                  <label class="form-label fw-semibold text-muted">Nama Tahun Ajaran</label>
                  <input type="text" name="nama_tahun" class="form-control form-control-lg rounded-3" placeholder="Contoh: 2023/2024" value="{{ old('nama_tahun') }}" required>
              </div>
              
              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Semester</label>
                  <select name="semester" class="form-select form-select-lg rounded-3" required>
                      <option value="">-- Pilih Semester --</option>
                      <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                      <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                  </select>
              </div>
          </div>
          <div class="modal-footer border-top-0 pb-4 px-4">
              <button type="button" class="btn btn-light btn-lg rounded-3 px-4 border" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary btn-lg rounded-3 px-5">Simpan Tahun Ajaran</button>
          </div>
      </form>
    </div>
  </div>
</div>

@if (count($errors) > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('addTahunAjaranModal'), {
            keyboard: false
        });
        myModal.show();
    });
</script>
@endif
@endsection
