@extends('layouts.admin')

@section('title', 'Edit Pertanyaan DASS-21')

@section('content')
    @include('admin.questions._form', ['question' => $question])
@endsection