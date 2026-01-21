@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Recycle Bin</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sample Name</th>
                <th>Deleted At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($samples as $sample)
            <tr>
                <td>{{ $sample->id }}</td>
                <td>{{ $sample->name }}</td>
                <td>{{ $sample->deleted_at }}</td>
                <td>
                    <form action="{{ route('samples.restore', $sample->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success btn-sm">Restore</button>
                    </form>
                    <form action="{{ route('samples.forceDelete', $sample->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Delete Permanently</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
