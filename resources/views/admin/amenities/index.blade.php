@extends('admin.layout-subpage')

@section('title', 'Amenities')

@section('content')
    <nav class="admin-user-show__breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}" class="admin-user-show__back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Admin dashboard
        </a>
        <span class="admin-user-show__crumb-sep" aria-hidden="true">/</span>
        <span class="admin-user-show__crumb-current">Amenities</span>
    </nav>

    @if(session('success'))
        <div class="admin-user-show__flash admin-user-show__flash--success">{{ session('success') }}</div>
    @endif

    <header class="admin-crud-header">
        <div>
            <h1 class="admin-user-show__title">Amenities</h1>
            <p class="admin-user-show__subtitle">Options landlords and tenants see when listing or filtering properties.</p>
        </div>
        <a href="{{ route('admin.amenities.create') }}" class="admin-crud-btn admin-crud-btn--primary">
            <i class="fas fa-plus" aria-hidden="true"></i> New amenity
        </a>
    </header>

    <section class="admin-user-card" aria-labelledby="amenities-list-heading">
        <h2 class="admin-user-card__title" id="amenities-list-heading"><i class="fas fa-concierge-bell"></i> All amenities</h2>

        @if($amenities->isEmpty())
            <p class="admin-user-show__empty">No amenities yet. Create one to use on property forms.</p>
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
                        @foreach($amenities as $amenity)
                            <tr>
                                <td data-label="Name">{{ $amenity->name }}</td>
                                <td data-label="Updated">{{ $amenity->updated_at->format('M j, Y g:i A') }}</td>
                                <td class="admin-crud-actions">
                                    <a href="{{ route('admin.amenities.edit', $amenity) }}" class="admin-crud-link">Edit</a>
                                    <form action="{{ route('admin.amenities.destroy', $amenity) }}" method="POST" class="admin-crud-inline-form" onsubmit="return confirm('Remove this amenity? It will be detached from all properties.');">
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
                {{ $amenities->links() }}
            </div>
        @endif
    </section>
@endsection
