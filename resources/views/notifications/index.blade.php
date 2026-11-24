<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔔 Notificaciones del Sistema
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        Resumen de Alertas
                    </h1>
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                        Total: {{ count($notifications) }}
                    </span>
                </div>

                @if($notifications->isEmpty())
                    <div class="bg-gray-50 border border-gray-200 shadow-sm rounded-lg p-6 text-center text-gray-500">
                        No hay notificaciones ni alertas registradas en este momento.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                            <div class="bg-white border-l-4 shadow-sm border-gray-200 rounded-r-lg p-4 flex items-start justify-between border-{{ $notification['color'] }}-500">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold uppercase px-2 py-0.5 rounded bg-{{ $notification['color'] }}-100 text-{{ $notification['color'] }}-800">
                                            {{ $notification['tipo'] }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $notification['fecha'] }}</span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mt-1">{{ $notification['titulo'] }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $notification['mensaje'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>