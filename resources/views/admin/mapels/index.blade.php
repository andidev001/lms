@extends('layouts.admin')

@section('title', 'Data Mata Pelajaran')
@section('subtitle', 'Kelola informasi mata pelajaran dan guru pengampunya')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="fw-bold mb-0">Daftar Mata Pelajaran</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addMapelModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Mapel
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
            <table class="table table-hover table-borderless align-middle" id="mapels-table">
                <thead class="table-light rounded">
                    <tr>
                        <th class="text-muted fw-normal py-3 rounded-start">No</th>
                        <th class="text-muted fw-normal py-3">Kode Mapel</th>
                        <th class="text-muted fw-normal py-3">Nama Mapel</th>
                        <th class="text-muted fw-normal py-3">Kategori</th>
                        <th class="text-muted fw-normal py-3">Guru Pengampu</th>
                        <th class="text-muted fw-normal py-3 rounded-end text-end" width="150px">Aksi</th>
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
    $('#mapels-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('mapels.index') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode_mapel', name: 'kode_mapel' },
            { data: 'nama_mapel', name: 'nama_mapel' },
            { data: 'kategori_badge', name: 'kategori' },
            { data: 'guru_pengampu', name: 'guru_pengampu.nama' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        }
    });
});
</script>
@endpush

<!-- Modal Tambah Mapel -->
<div class="modal fade" id="addMapelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
        <h5 class="modal-title fw-bold">Tambah Mata Pelajaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('mapels.store') }}">
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
                  <label class="form-label fw-semibold text-muted">Kode Mapel</label>
                  <input type="text" name="kode_mapel" class="form-control form-control-lg rounded-3" placeholder="Misal: MAT-X" value="{{ old('kode_mapel') }}" required>
              </div>
              
              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Nama Mapel</label>
                  <input type="text" name="nama_mapel" class="form-control form-control-lg rounded-3" placeholder="Misal: Matematika Wajib" value="{{ old('nama_mapel') }}" required>
              </div>

              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Kategori</label>
                  <select name="kategori" class="form-select form-select-lg rounded-3">
                      <option value="">-- Pilih Kategori --</option>
                      <option value="Muatan Nasional" {{ old('kategori') == 'Muatan Nasional' ? 'selected' : '' }}>Muatan Nasional</option>
                      <option value="Muatan Kewilayahan" {{ old('kategori') == 'Muatan Kewilayahan' ? 'selected' : '' }}>Muatan Kewilayahan</option>
                      <option value="Muatan Peminatan Kejuruan" {{ old('kategori') == 'Muatan Peminatan Kejuruan' ? 'selected' : '' }}>Muatan Peminatan Kejuruan</option>
                      <option value="Lintas Minat" {{ old('kategori') == 'Lintas Minat' ? 'selected' : '' }}>Lintas Minat</option>
                  </select>
              </div>

              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Guru Pengampu (Opsional)</label>
                  <select name="guru_id" class="form-select form-select-lg rounded-3">
                      <option value="">-- Pilih Guru Pengampu --</option>
                      @foreach($gurus as $guru)
                          <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>{{ $guru->nama }}</option>
                      @endforeach
                  </select>
              </div>
              
              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Keterangan</label>
                  <textarea name="keterangan" class="form-control rounded-3" rows="3" placeholder="Deskripsi opsional...">{{ old('keterangan') }}</textarea>
              </div>
          </div>
          <div class="modal-footer border-top-0 pb-4 px-4">
              <button type="button" class="btn btn-light btn-lg rounded-3 px-4 border" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary btn-lg rounded-3 px-5">Simpan Mapel</button>
          </div>
      </form>
    </div>
  </div>
</div>

@if (count($errors) > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('addMapelModal'), {
            keyboard: false
        });
        myModal.show();
    });
</script>
@endif
@endsection
