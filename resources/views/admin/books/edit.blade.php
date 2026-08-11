@extends('layouts.app')
@section('content')
<div class="max-w-[640px] mx-auto bg-white border border-zinc-200 rounded-2xl p-6">
    <h1 class="font-semibold">Edit buku</h1>
    <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data" class="mt-4">@csrf @method('PUT') @include('admin.books._form')<button type="submit" class="mt-6 w-full bg-zinc-900 text-white rounded-full py-2.5 text-sm font-medium">Perbarui</button></form>
</div>
@endsection
