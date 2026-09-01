@extends('layouts.admin')

@section('title', 'Data Kelas')
@section('subtitle', 'Kelola informasi kelas dan wali kelas')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="fw-bold mb-0">Daftar Kelas</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addKelasModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Kelas
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

        @if (count($errors) > 0)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> Data tidak dapat disimpan. Silakan periksa kembali form input (berwarna merah) untuk melihat detail kesalahannya.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover table-borderless align-middle" id="kelas-table">
                <thead class="table-light rounded">
                    <tr>
                        <th class="text-muted fw-normal py-3 rounded-start">No</th>
                        <th class="text-muted fw-normal py-3">Kode Kelas</th>
                        <th class="text-muted fw-normal py-3">Nama Kelas</th>
                        <th class="text-muted fw-normal py-3">Wali Kelas</th>
                        <th class="text-muted fw-normal py-3">Mata Pelajaran</th>
                        <th class="text-muted fw-normal py-3">Jumlah Siswa</th>
                        <th class="text-muted fw-normal py-3">Keterangan</th>
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
    $('#kelas-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('kelas.index') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode_kelas', name: 'kode_kelas' },
            { data: 'nama_kelas', name: 'nama_kelas' },
            { data: 'wali_kelas', name: 'wali_kelas.nama' },
            { data: 'jumlah_mapel', name: 'jumlah_mapel', orderable: false, searchable: false },
            { data: 'jumlah_siswa', name: 'jumlah_siswa', orderable: false, searchable: false },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        }
    });
});
</script>
@endpush

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="addKelasModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
        <h5 class="modal-title fw-bold">Tambah Kelas Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('kelas.store') }}">
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
                  <label class="form-label fw-semibold text-muted">Kode Kelas</label>
                  <input type="text" name="kode_kelas" class="form-control form-control-lg rounded-3" placeholder="Contoh: X-MIPA-1" value="{{ old('kode_kelas') }}" required>
              </div>
              
              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Nama Kelas</label>
                  <input type="text" name="nama_kelas" class="form-control form-control-lg rounded-3" placeholder="Contoh: X MIPA 1" value="{{ old('nama_kelas') }}" required>
              </div>

              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Wali Kelas (Opsional)</label>
                  <select name="guru_id" class="form-select form-select-lg rounded-3">
                      <option value="">-- Pilih Wali Kelas --</option>
                      @foreach($gurus as $guru)
                          <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>{{ $guru->nama }}</option>
                      @endforeach
                  </select>
              </div>

              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Mata Pelajaran (Opsional)</label>
                  <select name="mapel_ids[]" class="form-select form-select-lg rounded-3" multiple style="height: 120px;">
                      @php
                          $allMapels = \App\Models\Mapel::with('guru_pengampu')->orderBy('nama_mapel', 'asc')->get();
                      @endphp
                      @foreach($allMapels as $mapel)
                          <option value="{{ $mapel->id }}" {{ (is_array(old('mapel_ids')) && in_array($mapel->id, old('mapel_ids'))) ? 'selected' : '' }}>
                              {{ $mapel->nama_mapel }} {{ $mapel->guru_pengampu ? '(' . $mapel->guru_pengampu->nama . ')' : '(Tanpa Guru)' }}
                          </option>
                      @endforeach
                  </select>
                  <div class="form-text">Tahan tombol Ctrl (Windows) atau Command (Mac) untuk memilih lebih dari satu.</div>
              </div>
              
              <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Keterangan</label>
                  <textarea name="keterangan" class="form-control rounded-3" rows="3" placeholder="Tambahkan catatan jika ada">{{ old('keterangan') }}</textarea>
              </div>
          </div>
          <div class="modal-footer border-top-0 pb-4 px-4">
              <button type="button" class="btn btn-light btn-lg rounded-3 px-4 border" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary btn-lg rounded-3 px-5">Simpan Kelas</button>
          </div>
      </form>
    </div>
  </div>
</div>

@if (count($errors) > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('addKelasModal'), {
            keyboard: false
        });
        myModal.show();
    });
</script>
@endif
@endsection
