@extends('layouts.admin')

@section('title', 'User Management')

@section('admin-content')
<div class="recent-grid" style="margin-top: 5rem;">
    <div class="projects">
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.users') }}</h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <form method="GET" action="{{ route('user') }}" style="display: flex; gap: 10px; align-items: center;">
                        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') }}" class="search-input">
                        <select name="per_page" class="per-page-select">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table width="100%">
                        <thead>
                            <tr>
                                <td>{{ __('messages.name') }}</td>
                                <td>{{ __('messages.email') }}</td>
                                <td>{{ __('messages.role') }}</td>
                                <td>Status</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role ?? 'User' }}</td>
                                <td><span class="status {{ strtolower($user->status) == 'active' ? 'pink' : 'orange' }}"></span> {{ $user->status ?? 'Active' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 20px;">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
