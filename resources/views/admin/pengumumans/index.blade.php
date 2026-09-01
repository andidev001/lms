@extends('layouts.admin')

@section('title', 'Kelola Pengumuman')
@section('subtitle', 'Daftar semua pengumuman sistem')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Daftar Pengumuman</h5>
        <a href="{{ route('pengumumans.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> Buat Pengumuman
        </a>
    </div>
    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="pengumumanTable">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 rounded-start">No</th>
                        <th class="border-0">Judul</th>
                        <th class="border-0">Target</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Tanggal</th>
                        <th class="border-0 rounded-end text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#pengumumanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('pengumumans.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'judul', name: 'judul'},
                {data: 'target_badge', name: 'target'},
                {data: 'status_badge', name: 'is_active'},
                {data: 'created_at', name: 'created_at', render: function(data) {
                    return new Date(data).toLocaleDateString('id-ID');
                }},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end'}
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            }
        });
    });
</script>
@endpush
