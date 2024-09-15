@extends('layouts.app')

@section('content')
<button class="btn btn-primary mb-3" id="addNewButton">Add New</button>
<table id="entriesTable" class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="modal fade" id="addNewModal" tabindex="-1" role="dialog" aria-labelledby="addNewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNewModalLabel">Add New Entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="entryForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editName">Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editDescription">Description</label>
                        <input type="text" class="form-control" id="editDescription" name="description" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#entriesTable').DataTable({
            ajax: '{{ url('entries/data') }}',
            columns: [
                { data: 'name' },
                { data: 'description' },
                {
                    data: 'id',
                    render: function(data) {
                        return `
                            <button class="btn btn-warning btn-sm editBtn" data-id="${data}">Edit</button>
                            <button class="btn btn-danger btn-sm deleteBtn" data-id="${data}">Delete</button>
                        `;
                    }
                }
            ]
        });

        $('#addNewButton').on('click', function() {
            $('#addNewModal').modal('show');
        });

        $('#entryForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ url('entries/store') }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addNewModal').modal('hide');
                        $('#entryForm')[0].reset(); 
                        table.ajax.reload(); 
                    } else {
                        alert('Failed to add entry.');
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });

        $('#entriesTable').on('click', '.editBtn', function() {
            const id = $(this).data('id');
            $.get('{{ url('entries/edit') }}/' + id, function(entry) {
                $('#editName').val(entry.name);
                $('#editDescription').val(entry.description);
                $('#editForm').attr('data-id', entry.id);
                $('#editModal').modal('show');
            });
        });

        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $.ajax({
                url: '{{ url('entries/update') }}/' + id,
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#editModal').modal('hide');
                        table.ajax.reload(); 
                    } else {
                        alert('Failed to update entry.');
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });

        $('#entriesTable').on('click', '.deleteBtn', function() {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this entry?')) {
                $.ajax({
                    url: '{{ url('entries/destroy') }}/' + id,
                    method: 'DELETE', 
                    success: function(response) {
                        if (response.success) {
                            $('#entriesTable').DataTable().ajax.reload(); 
                        } else {
                            alert('Failed to delete entry.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText); 
                        alert('An error occurred: ' + xhr.responseText);
                    }
                });
            }
        });

    });
</script>
@endsection
