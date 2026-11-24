<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar repuesto</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('repuestos.update', $repuesto) }}">
                    @method('PUT')
                    @include('repuestos._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>