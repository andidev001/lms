@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Categories Management</h3>
        <a class="btn btn-primary" href="javascript:void(0)" id="createNewCategory"> Create New Category</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover data-table w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th width="280px">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm" name="categoryForm" class="form-horizontal">
                    <input type="hidden" name="category_id" id="category_id">
                    
                    <div class="form-group mb-3">
                        <label for="name" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Category Name" value="" maxlength="255" required="">
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="slug" class="col-sm-2 control-label">Slug</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="slug" name="slug" placeholder="Enter Slug" value="" maxlength="255" required="">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-12">
                            <textarea id="description" name="description" required="" placeholder="Enter Description" class="form-control"></textarea>
                        </div>
                    </div>
                    
                    <div class="col-sm-offset-2 col-sm-10 mt-4">
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
          ajax: "{{ route('categories.index') }}",
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'name', name: 'name'},
              {data: 'slug', name: 'slug'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ]
      });
      
      // Create new Category
      $('#createNewCategory').click(function () {
          $('#saveBtn').val("create-category");
          $('#category_id').val('');
          $('#categoryForm').trigger("reset");
          $('#modelHeading').html("Create New Category");
          $('#ajaxModel').modal('show');
      });
      
      // Edit Category
      $('body').on('click', '.editCategory', function () {
          var category_id = $(this).data('id');
          $.get("{{ route('categories.index') }}" +'/' + category_id +'/edit', function (data) {
              $('#modelHeading').html("Edit Category");
              $('#saveBtn').val("edit-user");
              $('#ajaxModel').modal('show');
              $('#category_id').val(data.id);
              $('#name').val(data.name);
              $('#slug').val(data.slug);
              $('#description').val(data.description);
          })
      });
      
      // Save Category
      $('#categoryForm').on('submit', function (e) {
          e.preventDefault();
          $(this).html('Sending..');
      
          $.ajax({
            data: $('#categoryForm').serialize(),
            url: "{{ route('categories.store') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#categoryForm').trigger("reset");
                $('#ajaxModel').modal('hide');
                table.draw();
                Swal.fire('Success', data.success, 'success');
                $('#saveBtn').html('Save changes');
            },
            error: function (data) {
                console.log('Error:', data);
                Swal.fire('Error', 'Something went wrong', 'error');
                $('#saveBtn').html('Save changes');
            }
        });
      });
      
      // Delete Category
      $('body').on('click', '.deleteCategory', function () {
          var category_id = $(this).data("id");
          
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
                    url: "{{ route('categories.store') }}"+'/'+category_id,
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
