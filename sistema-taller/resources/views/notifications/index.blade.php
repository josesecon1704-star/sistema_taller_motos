@php
    $etiquetas = [
        'cita' => 'Cita',
        'cotizacion_lista' => 'Cotización',
        'cotizacion' => 'Cotización',
        'orden' => 'Orden de trabajo',
        'moto_lista' => 'Moto lista',
        'tarea_asignada' => 'Tarea asignada',
        'pago' => 'Pago',
        'garantia' => 'Garantía',
        'mantenimiento' => 'Mantenimiento',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notificaciones</h2>
            <form method="POST" action="{{ route('notifications.leer-todas') }}">
                @csrf
                @method('PATCH')
                <button class="text-sm text-gray-600 underline">Marcar todas como leídas</button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            @if ($stockBajo->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">⚠️ Repuestos con stock bajo</h3>
                    <ul class="divide-y text-sm">
                        @foreach ($stockBajo as $repuesto)
                            <li class="py-2 flex justify-between">
                                <span>{{ $repuesto->codigo }} — {{ $repuesto->nombre }}</span>
                                <span class="text-red-700 font-medium">{{ $repuesto->stock_actual }} / mín. {{ $repuesto->stock_minimo }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                @forelse ($notificaciones as $n)
                    <div class="py-3 border-b last:border-0 flex gap-3 items-start {{ $n->leida ? 'opacity-70' : '' }}">
                        <span class="mt-1 h-2 w-2 rounded-full shrink-0 {{ $n->leida ? 'bg-gray-300' : 'bg-indigo-600' }}"></span>
                        <div class="flex-1">
                            <div class="flex flex-wrap gap-2 items-center text-xs text-gray-500">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">{{ $etiquetas[$n->tipo] ?? $n->tipo }}</span>
                                <span>{{ $n->created_at->format('d/m/Y H:i') }}</span>
                                @unless ($n->leida) <span class="text-indigo-600 font-semibold">Nueva</span> @endunless
                            </div>
                            <p class="text-sm text-gray-800 mt-1">{{ $n->mensaje }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-6">No tienes notificaciones.</p>
                @endforelse

                <div class="mt-4">{{ $notificaciones->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
