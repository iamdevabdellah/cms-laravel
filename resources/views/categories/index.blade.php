@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-end mb-2">
    <a href="{{ route('categories.create') }}" class="btn btn-success">Add Category</a>
</div>

<div class="card card-default">
    <div class="card-header">Categories</div>
    <div class="card-body">
        @if ($categories->count() > 0)
            <table class="table">
                <thead>
                    <th>Name</th>
                    <th>Post Count</th>
                    <th></th>
                </thead>

                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>
                                {{ $category->name }}
                            </td>
                            <td>
                                {{ $category->posts->count() }}
                            </td>
                            <td>
                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-info btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm" data-id="{{ $category->id }}" data-name="{{ $category->name }}" onclick="handleDelete(this)">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <h3 class="text-center">No categories yet</h3>
        @endif
            
        <!-- Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">

                <form action="" method="post" id="deleteCategoryForm">
                    @csrf
                    @method('DELETE')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Delete Category</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p class="text-center text-bold" id="pTag">Are you sure you want to delete this category ?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Go back</button>
                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                            </div>
                        </div>
                </form>

            </div>
        </div>


    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        
        function handleDelete(el) {
            let form = document.getElementById('deleteCategoryForm');
            const p = document.getElementById('pTag');
            let name = el.dataset.name;
            let id = el.dataset.id;
            p.innerHTML = "Are you sure you want to delete " + name.bold() + " from the categories ?";
            //console.log(name);
            //console.log(el.dataset.id);
            //console.log(el.dataset.name);
             form.action = '/categories/' + id;
             $('#deleteModal').modal('show')
        }

    </script>
@endsection