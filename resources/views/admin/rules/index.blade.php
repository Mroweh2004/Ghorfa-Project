@extends('admin.layout-subpage')

@section('title', 'House rules')

@section('content')
    <nav class="admin-user-show__breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}" class="admin-user-show__back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Admin dashboard
        </a>
        <span class="admin-user-show__crumb-sep" aria-hidden="true">/</span>
        <span class="admin-user-show__crumb-current">House rules</span>
    </nav>

    @if(session('success'))
        <div class="admin-user-show__flash admin-user-show__flash--success">{{ session('success') }}</div>
    @endif

    <header class="admin-crud-header">
        <div>
            <h1 class="admin-user-show__title">Rules</h1>
            <p class="admin-user-show__subtitle">Preset rules landlords can attach to listings; tenants see them on requests.</p>
        </div>
        <a href="{{ route('admin.rules.create') }}" class="admin-crud-btn admin-crud-btn--primary">
            <i class="fas fa-plus" aria-hidden="true"></i> New rule
        </a>
    </header>

    <section class="admin-user-card" aria-labelledby="rules-list-heading">
        <h2 class="admin-user-card__title" id="rules-list-heading"><i class="fas fa-clipboard-list"></i> All rules</h2>

        @if($rules->isEmpty())
            <p class="admin-user-show__empty">No rules yet. Create presets for property forms.</p>
        @else
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Updated</th>
                            <th scope="col"><span class="admin-crud-sr-actions">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rules as $rule)
                            <tr>
                                <td data-label="Name">{{ $rule->name }}</td>
                                <td data-label="Updated">{{ $rule->updated_at->format('M j, Y g:i A') }}</td>
                                <td class="admin-crud-actions">
                                    <a href="{{ route('admin.rules.edit', $rule) }}" class="admin-crud-link">Edit</a>
                                    <form action="{{ route('admin.rules.destroy', $rule) }}" method="POST" class="admin-crud-inline-form" onsubmit="return confirm('Remove this rule? It will be detached from all properties.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-crud-link admin-crud-link--danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="admin-crud-pagination">
                {{ $rules->links() }}
            </div>
        @endif
    </section>
@endsection
