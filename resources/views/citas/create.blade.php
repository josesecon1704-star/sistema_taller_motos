<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Agendar Nueva Cita
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="max-w-xl mx-auto bg-gray-800 p-6 rounded-lg shadow-md border border-gray-700">
            <h2 class="text-2xl font-bold text-white mb-6">Registrar Cita</h2>

            @if ($errors->any())
                <div class="bg-red-500 text-white p-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('citas.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Seleccionar Motocicleta (Placa - Marca)</label>
                    <select name="idMotocicleta" style="color: #000000; background-color: #ffffff;" class="w-full border border-gray-600 rounded p-2 font-medium" required>
                        <option value="">— Seleccione una moto —</option>
                        @foreach($motocicletas as $moto)
                            <option value="{{ $moto->id }}">{{ $moto->placa }} - {{ $moto->marca }} {{ $moto->modelo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Motivo de la Cita</label>
                    <input type="text" name="motivo" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border border-gray-600 rounded p-2 font-medium" placeholder="Ej. Mantenimiento preventivo" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Fecha de la Cita</label>
                    <input type="date" name="fecha" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border border-gray-600 rounded p-2 font-medium" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Hora</label>
                    <input type="time" name="Hora" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border border-gray-600 rounded p-2 font-medium" required>
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('citas.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded mr-2 hover:bg-gray-500 font-semibold">Cancelar</a>
                    <button type="submit" style="background-color: #111827; color: #ffffff; padding: 8px 16px; border-radius: 6px; font-weight: bold; border: none; cursor: pointer;">Guardar Cita</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout><x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Agendar Nueva Cita
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="max-w-xl mx-auto bg-gray-800 p-6 rounded-lg shadow-md border border-gray-700">
            <h2 class="text-2xl font-bold text-white mb-6">Registrar Cita</h2>

            @if ($errors->any())
                <div class="bg-red-500 text-white p-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('citas.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Seleccionar Motocicleta (Placa - Marca)</label>
                    <select name="idMotocicleta" style="color: #000000; background-color: #ffffff;" class="w-full border border-gray-600 rounded p-2 font-medium" required>
                        <option value="">— Seleccione una moto —</option>
                        @foreach($motocicletas as $moto)
                            <option value="{{ $moto->id }}">{{ $moto->placa }} - {{ $moto->marca }} {{ $moto->modelo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Motivo de la Cita</label>
                    <input type="text" name="motivo" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border border-gray-600 rounded p-2 font-medium" placeholder="Ej. Mantenimiento preventivo" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Fecha de la Cita</label>
                    <input type="date" name="fecha" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border border-gray-600 rounded p-2 font-medium" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Hora</label>
                    <input type="time" name="Hora" style="color: #000000 !important; background-color: #ffffff !important;" class="w-full border border-gray-600 rounded p-2 font-medium" required>
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('citas.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded mr-2 hover:bg-gray-500 font-semibold">Cancelar</a>
                    <button type="submit" style="background-color: #111827; color: #ffffff; padding: 8px 16px; border-radius: 6px; font-weight: bold; border: none; cursor: pointer;">Guardar Cita</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>