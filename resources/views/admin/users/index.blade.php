@extends('layouts.admin')
@section('admin-content')
<h1 class="page-title mb-4">Peserta</h1>
<div class="cat-card p-3 table-responsive">
<table class="table"><thead><tr><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>HP</th><th>Instansi</th></tr></thead><tbody>
@foreach ($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->username }}</td><td>{{ $user->email }}</td><td>{{ $user->role }}</td><td>{{ $user->phone }}</td><td>{{ $user->institution }}</td></tr>@endforeach
</tbody></table>{{ $users->links() }}</div>
@endsection
