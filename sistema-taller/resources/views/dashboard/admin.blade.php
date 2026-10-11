<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel del Administrador
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-lg">Bienvenido, {{ Auth::user()->name }}.</p>
                <p class="text-gray-500 mt-1">Aquí irá el resumen general: usuarios, órdenes y finanzas.</p>
            </div>
        </div>
    </div>
</x-app-layout>