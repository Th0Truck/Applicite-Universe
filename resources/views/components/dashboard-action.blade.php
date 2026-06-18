@props([
    'description',
    'href',
    'label',
])

<a class="dashboard-action" href="{{ $href }}">
    <strong>{{ $label }}</strong>
    <span>{{ $description }}</span>
</a>
