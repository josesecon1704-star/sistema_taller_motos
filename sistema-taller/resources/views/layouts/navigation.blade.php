@php
    $esCliente = Auth::user()->esCliente();
    $items = [
        ['modulo' => null,           'ruta' => 'dashboard',    'activo' => 'dashboard',      'icono' => '🏠', 'texto' => 'Inicio'],
        ['modulo' => 'servicios',    'ruta' => 'servicios.index',    'activo' => 'servicios.*',    'icono' => '🔧', 'texto' => 'Servicios'],
        ['modulo' => 'repuestos',    'ruta' => 'repuestos.index',    'activo' => 'repuestos.*',    'icono' => '📦', 'texto' => 'Repuestos'],
        ['modulo' => 'proveedores',  'ruta' => 'proveedores.index',  'activo' => 'proveedores.*',  'icono' => '🚚', 'texto' => 'Proveedores'],
        ['modulo' => 'clientes',     'ruta' => 'clientes.index',     'activo' => 'clientes.*',     'icono' => '👥', 'texto' => 'Clientes'],
        ['modulo' => 'empleados',    'ruta' => 'empleados.index',    'activo' => 'empleados.*',    'icono' => '🧑‍🔧', 'texto' => 'Empleados'],
        ['modulo' => 'motocicletas', 'ruta' => 'motocicletas.index', 'activo' => 'motocicletas.*', 'icono' => '🏍️', 'texto' => $esCliente ? 'Mis motocicletas' : 'Motocicletas'],
        ['modulo' => 'bitacora',     'ruta' => 'bitacora.index',     'activo' => 'bitacora.*',     'icono' => '📋', 'texto' => 'Bitácora'],
        ['modulo' => 'roles',        'ruta' => 'roles.index',        'activo' => 'roles.*',        'icono' => '🔐', 'texto' => 'Roles y permisos'],
    ];
@endphp

<div x-data="{ open: false }">
    <!-- Barra superior: solo en celular y tableta -->
    <div class="lg:hidden sticky top-0 z-30 flex items-center justify-between h-14 px-4 bg-gray-800 border-b border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-white font-semibold">
            <x-application-logo class="block h-7 w-auto fill-current text-gray-200" />
            Taller de Motos
        </a>
        <button @click="open = true" class="p-2 rounded-md text-gray-300 hover:bg-gray-700 focus:outline-none" aria-label="Abrir menú">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Fondo oscuro al abrir el menú en celular -->
    <div x-show="open" x-cloak @click="open = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>

    <!-- Menú lateral -->
    <aside :class="open ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-gray-800 text-gray-200 shadow-xl transform transition-transform duration-200 lg:translate-x-0">

        <div class="flex items-center justify-between h-16 px-5 border-b border-gray-700 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-white font-semibold text-lg">
                <x-application-logo class="block h-8 w-auto fill-current text-gray-200" />
                Taller de Motos
            </a>
            <button @click="open = false" class="lg:hidden p-1 rounded-md text-gray-400 hover:text-white" aria-label="Cerrar menú">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            @foreach ($items as $item)
                @if ($item['modulo'] === null || Auth::user()->can('permiso', [$item['modulo'], 'mostrar']))
                    @php $activo = request()->routeIs($item['activo']); @endphp
                    <a href="{{ route($item['ruta']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                              {{ $activo ? 'bg-white text-gray-900' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <span class="w-6 text-center text-lg leading-none">{{ $item['icono'] }}</span>
                        <span>{{ $item['texto'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <!-- Usuario, perfil y cerrar sesión -->
        <div class="shrink-0 border-t border-gray-700 px-3 py-4 space-y-1">
            <div class="px-3 pb-2">
                <div class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</div>
            </div>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                      {{ request()->routeIs('profile.*') ? 'bg-white text-gray-900' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <span class="w-6 text-center text-lg leading-none">⚙️</span>
                <span>Mi perfil</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition">
                    <span class="w-6 text-center text-lg leading-none">🚪</span>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>
</div>