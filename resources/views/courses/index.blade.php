@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Courses Management</h3>
        <a class="btn btn-primary" href="javascript:void(0)" id="createNewCourse"> Create New Course</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover data-table w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Teacher</th>
                        <th>Status</th>
                        <th width="280px">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Course Modal -->
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="courseForm" name="courseForm" class="form-horizontal">
                    <input type="hidden" name="course_id" id="course_id">
                    
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="title" class="control-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter Course Title" value="" maxlength="255" required="">
                        </div>
                        
                        <div class="col-md-6 form-group mb-3">
                            <label for="slug" class="control-label">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug" placeholder="Enter Slug" value="" maxlength="255" required="">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="category_id" class="control-label">Category</label>
                            <select class="form-control" name="category_id" id="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 form-group mb-3">
                            <label for="teacher_id" class="control-label">Teacher</label>
                            <select class="form-control" name="teacher_id" id="teacher_id" required>
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="control-label">Description</label>
                        <textarea id="description" name="description" required="" placeholder="Enter Description" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="status" class="control-label">Status</label>
                        <select class="form-control" name="status" id="status" required>
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
  $(function () {
      // CSRF token setup
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      
      // Initialize DataTable
      var table = $('.data-table').DataTable({
          processing: true,
          serverSide: true,
          responsive: true,
          ajax: "{{ route('courses.index') }}",
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'title', name: 'title'},
              {data: 'category_name', name: 'category_name'},
              {data: 'teacher_name', name: 'teacher_name'},
              {data: 'status', name: 'status'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ]
      });
      
      // Create new Course
      $('#createNewCourse').click(function () {
          $('#saveBtn').val("create-course");
          $('#course_id').val('');
          $('#courseForm').trigger("reset");
          $('#modelHeading').html("Create New Course");
          $('#ajaxModel').modal('show');
      });
      
      // Edit Course
      $('body').on('click', '.editCourse', function () {
          var course_id = $(this).data('id');
          $.get("{{ route('courses.index') }}" +'/' + course_id +'/edit', function (data) {
              $('#modelHeading').html("Edit Course");
              $('#saveBtn').val("edit-course");
              $('#ajaxModel').modal('show');
              $('#course_id').val(data.id);
              $('#title').val(data.title);
              $('#slug').val(data.slug);
              $('#category_id').val(data.category_id);
              $('#teacher_id').val(data.teacher_id);
              $('#description').val(data.description);
              $('#status').val(data.status);
          })
      });
      
      // Save Course
      $('#courseForm').on('submit', function (e) {
          e.preventDefault();
          $(this).find('#saveBtn').html('Sending..').prop('disabled', true);
      
          $.ajax({
            data: $('#courseForm').serialize(),
            url: "{{ route('courses.store') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#courseForm').trigger("reset");
                $('#ajaxModel').modal('hide');
                table.draw();
                Swal.fire('Success', data.success, 'success');
                $('#saveBtn').html('Save changes').prop('disabled', false);
            },
            error: function (data) {
                console.log('Error:', data);
                Swal.fire('Error', 'Something went wrong. Check validation.', 'error');
                $('#saveBtn').html('Save changes').prop('disabled', false);
            }
        });
      });
      
      // Delete Course
      $('body').on('click', '.deleteCourse', function () {
          var course_id = $(this).data("id");
          
          Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7367f0',
            cancelButtonColor: '#ea5455',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('courses.store') }}"+'/'+course_id,
                    success: function (data) {
                        table.draw();
                        Swal.fire('Deleted!', data.success, 'success');
                    },
                    error: function (data) {
                        console.log('Error:', data);
                        Swal.fire('Error', 'Something went wrong', 'error');
                    }
                });
            }
          });
      });
      
  });
</script>
@endpush
