<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestionar Cita (CU16)
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="max-w-xl mx-auto bg-gray-800 p-6 rounded-lg shadow-md border border-gray-700">
            <h2 class="text-2xl font-bold text-white mb-6">Reprogramar o Cancelar Cita</h2>

            @if ($errors->any())
                <div class="bg-red-500 text-white p-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('citas.update', $cita->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Motocicleta</label>
                    <select name="motocicleta_id" style="color: #000000; background-color: #ffffff;" class="w-full border border-gray-600 rounded p-2 font-medium" required>
                        @foreach($motocicletas as $moto)
                            <option value="{{ $moto->id }}" {{ $cita->motocicleta_id == $moto->id ? 'selected' : '' }}>
                                {{ $moto->placa }} - {{ $moto->marca }} {{ $moto->modelo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Motivo</label>
                    <input type="text" name="motivo" value="{{ $cita->motivo }}" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border border-gray-600 rounded p-2 font-medium" required>
                </div>

                @php
                    $partes = explode(' ', $cita->fecha_hora);
                    $fFecha = $partes[0] ?? '';
                    $fHora = isset($partes[1]) ? substr($partes[1], 0, 5) : '';
                @endphp

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Nueva Fecha</label>
                    <input type="date" name="fecha" value="{{ $fFecha }}" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border border-gray-600 rounded p-2 font-medium" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Nueva Hora</label>
                    <input type="time" name="Hora" value="{{ $fHora }}" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border border-gray-600 rounded p-2 font-medium" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Estado de la Cita</label>
                    <select name="estado" style="color: #000000; background-color: #ffffff;" class="w-full border border-gray-600 rounded p-2 font-medium" required>
                        <option value="PENDIENTE" {{ $cita->estado == 'PENDIENTE' ? 'selected' : '' }}>PENDIENTE (Reprogramada)</option>
                        <option value="COMPLETADA" {{ $cita->estado == 'COMPLETADA' ? 'selected' : '' }}>COMPLETADA</option>
                        <option value="CANCELADA" {{ $cita->estado == 'CANCELADA' ? 'selected' : '' }}>CANCELADA</option>
                    </select>
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('citas.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded mr-2 hover:bg-gray-500 font-semibold">Cancelar</a>
                    <button type="submit" style="background-color: #111827; color: #ffffff; padding: 8px 16px; border-radius: 6px; font-weight: bold; border: none; cursor: pointer;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>