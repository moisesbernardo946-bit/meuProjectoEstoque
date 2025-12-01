@extends('layouts.almoxarife')

@section('title', 'Perfil')
@section('page-title', 'Perfil do usuário')
@section('page-subtitle', 'Gerencie seus dados de acesso')

@section('content')

    {{-- Alerts de perfil --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alerts de senha --}}
    @if (session('success_password'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success_password') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error_password'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error_password') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

@endsection
