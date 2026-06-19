<h1><a class="dashboard-header" href="{{ route('dashboard') }}">Dashboard</a></h1>
<p>Welcome back, {{ auth()->user()->name }}.</p>

<div class="dashboard-actions">
    @can('users.view')
        <x-dashboard-action
            :href="route('dashboard.users.index')"
            label="Users"
            description="Edit users and review their access rights."
        />
    @endcan

    @can('pages.view')
        <x-dashboard-action
            :href="route('dashboard.cms.pages.index')"
            label="Pages"
            description="Create CMS pages with paragraph templates."
        />
    @endcan

    <x-dashboard-action
        :href="route('two-factor.show')"
        label="Security"
        description="Manage your two-factor authentication setup."
    />
</div>