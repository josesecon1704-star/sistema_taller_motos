<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Programar Mantenimiento
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="max-w-xl mx-auto bg-gray-800 p-6 rounded-lg shadow-md border border-gray-700">
            <h2 class="text-2xl font-bold text-white mb-6">Nuevo Mantenimiento Preventivo</h2>

            <form action="{{ route('mantenimientos.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Seleccionar Motocicleta</label>
                    <select name="motocicleta_id" style="color: #000000; background-color: #ffffff;" class="w-full border rounded p-2 font-medium" required>
                        <option value="">— Seleccione una moto —</option>
                        @foreach($motocicletas as $moto)
                            <option value="{{ $moto->id }}">{{ $moto->placa }} {{ $moto->marca }} {{ $moto->modelo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Tipo de Mantenimiento</label>
                    <input type="text" name="tipo_mantenimiento" placeholder="Ej. Cambio de aceite y filtros" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border rounded p-2 font-medium" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Kilometraje Límite (Opcional)</label>
                    <input type="number" name="proximo_km" placeholder="Ej. 5000" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border rounded p-2 font-medium">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Fecha Límite</label>
                    <input type="date" name="proxima_fecha" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border rounded p-2 font-medium" required>
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('mantenimientos.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded mr-2 font-semibold">Cancelar</a>
                    <button type="submit" style="background-color: #111827; color: #ffffff; padding: 8px 16px; border-radius: 6px; font-weight: bold; border: none; cursor: pointer;">Guardar Mantenimiento</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>