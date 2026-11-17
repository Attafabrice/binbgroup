<a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>
    Tableau de bord
</a>
@can('view_quote_request')
    <a href="{{ route('admin.quote-requests.index') }}" @class(['active' => request()->routeIs('admin.quote-requests.*')])>Demandes de devis</a>
@endcan
@can('view_quote')
    <a href="{{ route('admin.quotes.index') }}" @class(['active' => request()->routeIs('admin.quotes.*')])>Devis</a>
@endcan
@can('view_project')
    <a href="{{ route('admin.projects.index') }}" @class(['active' => request()->routeIs('admin.projects.*')])>Projets</a>
@endcan
@can('view_service')
    <a href="{{ route('admin.services.index') }}" @class(['active' => request()->routeIs('admin.services.*')])>Services</a>
@endcan
@role('super_admin')
    <a href="{{ route('admin.users.index') }}" @class(['active' => request()->routeIs('admin.users.*')])>
        Utilisateurs
    </a>
    <a href="{{ route('admin.roles.index') }}" @class(['active' => request()->routeIs('admin.roles.*')])>
        Rôles
    </a>
    <a href="{{ route('admin.permissions.index') }}" @class(['active' => request()->routeIs('admin.permissions.*')])>
        Permissions
    </a>
@endrole