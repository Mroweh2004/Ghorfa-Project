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

        <form class="admin-crud-search" action="{{ route('admin.rules.index') }}" method="get" role="search">
            <label class="admin-crud-search__field">
                <span class="admin-crud-sr-actions">Search rules by name</span>
                <i class="fas fa-search" aria-hidden="true"></i>
                <input type="search" name="search" value="{{ $search }}" placeholder="Search by name" autocomplete="off" maxlength="255" enterkeyhint="search">
            </label>
            <button type="submit" class="admin-crud-btn admin-crud-btn--primary admin-crud-search__submit">Search</button>
        </form>

        @if($rules->isEmpty())
            @if($search !== '')
                <p class="admin-user-show__empty">No rules match your search. <a href="{{ route('admin.rules.index') }}" class="admin-crud-link">Show all</a></p>
            @else
                <p class="admin-user-show__empty">No rules yet. Create presets for property forms.</p>
            @endif
        @else
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table admin-crud-table--row-phone">
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
                                <td class="admin-crud-actions" data-label="Actions">
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
