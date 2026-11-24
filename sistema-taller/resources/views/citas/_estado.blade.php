@php
    $colores = [
        'programada' => 'bg-yellow-100 text-yellow-800',
        'confirmada' => 'bg-blue-100 text-blue-800',
        'completada' => 'bg-green-100 text-green-800',
        'cancelada'  => 'bg-gray-200 text-gray-700',
        'no_asistio' => 'bg-red-100 text-red-800',
    ];
@endphp
<span class="px-2 py-1 text-xs rounded whitespace-nowrap {{ $colores[$estado] ?? 'bg-gray-100 text-gray-700' }}">
    {{ \App\Models\Cita::ESTADOS[$estado] ?? $estado }}
</span>
