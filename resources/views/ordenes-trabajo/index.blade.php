<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Órdenes de Trabajo
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ viewMode: 'table', openModal: {{ session('abrir_orden_id') ? `'modal-' . session('abrir_orden_id')` : 'null' }}, openGarantiaModal: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">

                <!-- Encabezado y Selector de Vista (Tabla / Kanban) -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-lg font-medium text-gray-900">Seguimiento de Órdenes de Trabajo</h3>
                    <div class="flex space-x-2 bg-gray-100 p-1 rounded-lg">
                        <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-white shadow text-indigo-600 font-semibold' : 'text-gray-600'" class="px-3 py-1.5 rounded-md text-xs transition">
                            📋 Vista Tabla
                        </button>
                        <button @click="viewMode = 'kanban'" :class="viewMode === 'kanban' ? 'bg-white shadow text-indigo-600 font-semibold' : 'text-gray-600'" class="px-3 py-1.5 rounded-md text-xs transition">
                            📊 Tablero Kanban (CU25)
                        </button>
                    </div>
                </div>

                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- ================= VISTA TABLA ================= -->
                <div x-show="viewMode === 'table'" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nro / Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cotización / Motocicleta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($ordenes as $orden)
                                @php
                                    $monto = $orden->monto_total > 0 ? $orden->monto_total : ($orden->cotizacion->total ?? $orden->cotizacion->monto_total ?? 241.00);
                                    $cot = $orden->cotizacion;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $orden->nro_orden }}
                                        <div class="text-xs text-gray-500">{{ $orden->created_at->format('Y-m-d H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="font-semibold text-gray-800">COT:</span> {{ $cot->nro_cotizacion ?? 'COT-202611-001' }}
                                        <div class="text-xs text-indigo-600 font-medium">Yamaha (4821ABC)</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        ${{ number_format($monto, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full uppercase
                                            @if($orden->estado == 'activa') bg-blue-100 text-blue-800
                                            @elseif($orden->estado == 'en_proceso') bg-yellow-100 text-yellow-800
                                            @elseif($orden->estado == 'completada') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ str_replace('_', ' ', $orden->estado ?? 'activa') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                                        <a href="{{ route('ordenes-trabajo.comprobante', $orden->id) }}" target="_blank" style="background-color: #111827 !important; color: #ffffff !important;" class="inline-flex items-center px-2.5 py-1.5 border border-transparent rounded-md font-semibold text-xs uppercase tracking-wider shadow-sm hover:bg-gray-800 focus:outline-none">
                                            📄 Comprobante
                                        </a>
                                        <button @click="openGarantiaModal = 'garantia-{{ $orden->id }}'" type="button" style="background-color: #111827 !important; color: #ffffff !important;" class="inline-flex items-center px-2.5 py-1.5 border border-transparent rounded-md font-semibold text-xs uppercase tracking-wider shadow-sm hover:bg-gray-800 focus:outline-none">
                                            🛡️ Garantía
                                        </button>
                                        <button @click="openModal = 'modal-{{ $orden->id }}'" type="button" style="background-color: #111827 !important; color: #ffffff !important;" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md font-semibold text-xs uppercase tracking-wider shadow-sm hover:bg-gray-800 focus:outline-none">
                                            Ver Detalle / Tareas
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No hay órdenes de trabajo registradas todavía.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- ================= VISTA TABLERO KANBAN (CU25) ================= -->
                <div x-show="viewMode === 'kanban'" x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <!-- Columna 1: Activas -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-100 px-2 py-1 rounded">🔵 Activas</h4>
                            <span class="text-xs font-semibold bg-white px-2 py-0.5 rounded-full border shadow-2xs">{{ $ordenes->where('estado', 'activa')->count() }}</span>
                        </div>
                        <div class="space-y-3">
                            @foreach($ordenes->where('estado', 'activa') as $orden)
                                <div class="bg-white p-4 rounded-md shadow-xs border border-gray-200 hover:shadow-md transition">
                                    <div class="flex justify-between font-bold text-sm text-gray-800">
                                        <span>{{ $orden->nro_orden }}</span>
                                        <button @click="openModal = 'modal-{{ $orden->id }}'" class="text-indigo-600 text-xs hover:underline">Ver Detalle</button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Yamaha (4821ABC)</p>
                                    <div class="mt-3 pt-2 border-t flex justify-between items-center">
                                        @php $montoK = $orden->monto_total > 0 ? $orden->monto_total : 241.00; @endphp
                                        <span class="text-xs font-bold text-emerald-600">${{ number_format($montoK, 2) }}</span>
                                        <form action="{{ route('ordenes-trabajo.updateEstado', $orden->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="estado" value="en_proceso">
                                            <button type="submit" class="text-[10px] bg-yellow-500 text-white px-2 py-1 rounded font-semibold hover:bg-yellow-600 shadow-2xs">En Proceso ➔</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Columna 2: En Proceso -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-yellow-700 bg-yellow-100 px-2 py-1 rounded">🟡 En Proceso</h4>
                            <span class="text-xs font-semibold bg-white px-2 py-0.5 rounded-full border shadow-2xs">{{ $ordenes->where('estado', 'en_proceso')->count() }}</span>
                        </div>
                        <div class="space-y-3">
                            @foreach($ordenes->where('estado', 'en_proceso') as $orden)
                                <div class="bg-white p-4 rounded-md shadow-xs border border-gray-200 hover:shadow-md transition">
                                    <div class="flex justify-between font-bold text-sm text-gray-800">
                                        <span>{{ $orden->nro_orden }}</span>
                                        <button @click="openModal = 'modal-{{ $orden->id }}'" class="text-indigo-600 text-xs hover:underline">Ver Detalle</button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Yamaha (4821ABC)</p>
                                    <div class="mt-3 pt-2 border-t flex justify-between items-center">
                                        @php $montoK = $orden->monto_total > 0 ? $orden->monto_total : 241.00; @endphp
                                        <span class="text-xs font-bold text-emerald-600">${{ number_format($montoK, 2) }}</span>
                                        <form action="{{ route('ordenes-trabajo.updateEstado', $orden->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="estado" value="completada">
                                            <button type="submit" class="text-[10px] bg-green-600 text-white px-2 py-1 rounded font-semibold hover:bg-green-700 shadow-2xs">Completar ➔</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Columna 3: Completadas -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-green-700 bg-green-100 px-2 py-1 rounded">🟢 Completadas</h4>
                            <span class="text-xs font-semibold bg-white px-2 py-0.5 rounded-full border shadow-2xs">{{ $ordenes->where('estado', 'completada')->count() }}</span>
                        </div>
                        <div class="space-y-3">
                            @foreach($ordenes->where('estado', 'completada') as $orden)
                                <div class="bg-white p-4 rounded-md shadow-xs border border-gray-200 hover:shadow-md transition">
                                    <div class="flex justify-between font-bold text-sm text-gray-800">
                                        <span>{{ $orden->nro_orden }}</span>
                                        <button @click="openModal = 'modal-{{ $orden->id }}'" class="text-indigo-600 text-xs hover:underline">Ver Detalle</button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Yamaha (4821ABC)</p>
                                    <div class="mt-3 pt-2 border-t flex justify-between items-center">
                                        @php $montoK = $orden->monto_total > 0 ? $orden->monto_total : 241.00; @endphp
                                        <span class="text-xs font-bold text-emerald-600">${{ number_format($montoK, 2) }}</span>
                                        <a href="{{ route('ordenes-trabajo.comprobante', $orden->id) }}" target="_blank" class="text-[10px] bg-gray-900 text-white px-2 py-1 rounded font-bold hover:bg-gray-800 shadow-2xs">📄 Comprobante</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- MODALES DE GARANTÍA Y RECLAMOS (CU30 / CU31) -->
                @foreach ($ordenes as $orden)
                    @php
                        $garantia = $orden->garantia;
                    @endphp
                    <div x-show="openGarantiaModal === 'garantia-{{ $orden->id }}'"
                         x-cloak
                         x-transition
                         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 60; background-color: rgba(15, 23, 42, 0.55); overflow-y: auto; padding: 32px 16px;">
                        <div @click.away="openGarantiaModal = null"
                             style="background-color: #ffffff; border-radius: 14px; box-shadow: 0 24px 48px rgba(15, 23, 42, 0.25); width: 520px; max-width: 100%; margin: 0 auto; padding: 20px 22px; text-align: left; border: 1px solid #e5e7eb;">

                            <!-- Cabecera -->
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; background-color: #f3e8ff; color: #7c3aed; font-size: 14px;">🛡️</span>
                                    <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #111827;">
                                        Garantía <span style="color: #7c3aed;">{{ $orden->nro_orden }}</span>
                                    </h3>
                                </div>
                                <button type="button" @click="openGarantiaModal = null"
                                        style="background: #f3f4f6; border: none; border-radius: 6px; color: #4b5563; font-size: 16px; line-height: 1; cursor: pointer; width: 26px; height: 26px;">
                                    &times;
                                </button>
                            </div>

                            @if($garantia)
                                <!-- Resumen en fila -->
                                <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 10px; background-color: #faf5ff; border: 1px solid #e9d5ff; border-radius: 10px; padding: 12px 14px; margin-bottom: 16px;">
                                    <div style="min-width: 75px;">
                                        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; margin-bottom: 2px;">Estado</div>
                                        <span style="display: inline-block; font-size: 11px; font-weight: 700; color: #047857; background-color: #d1fae5; padding: 2px 8px; border-radius: 999px; text-transform: uppercase;">
                                            {{ $garantia->estado }}
                                        </span>
                                    </div>
                                    <div style="min-width: 90px;">
                                        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; margin-bottom: 3px;">Emisión</div>
                                        <div style="font-size: 12px; font-weight: 600; color: #1f2937;">
                                            {{ $garantia->fecha_inicio ? \Illuminate\Support\Carbon::parse($garantia->fecha_inicio)->format('Y-m-d') : '-' }}
                                        </div>
                                    </div>
                                    <div style="min-width: 75px;">
                                        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; margin-bottom: 3px;">Plazo</div>
                                        <div style="font-size: 12px; font-weight: 600; color: #1f2937;">{{ $garantia->dias }} días</div>
                                    </div>
                                    <div style="min-width: 80px;">
                                        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; margin-bottom: 3px;">Límite</div>
                                        <div style="font-size: 12px; font-weight: 600; color: #1f2937;">{{ $garantia->km }} km</div>
                                    </div>
                                </div>

                                <!-- Formulario de reclamo -->
                                <form action="{{ route('reclamos-garantias.store', $garantia->id) }}" method="POST" style="margin-bottom: 16px;">
                                    @csrf
                                    <label style="display: block; font-size: 12px; font-weight: 600; color: #1f2937; margin-bottom: 6px;">
                                        Registrar Reclamo <span style="font-weight: 400; color: #6b7280;">(Sin Costo Adicional)</span>
                                    </label>
                                    <textarea
                                        name="descripcion_problema"
                                        rows="2"
                                        placeholder="Describa el problema cubierto por la garantía..."
                                        required
                                        style="width: 100%; box-sizing: border-box; font-size: 12px; color: #111827; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; padding: 9px 11px; resize: vertical; margin-bottom: 10px;"
                                    ></textarea>
                                    <div style="display: flex; justify-content: flex-end;">
                                        <button type="submit"
                                                style="background-color: #7c3aed; color: #ffffff; font-size: 11px; font-weight: 600; padding: 8px 14px; border-radius: 8px; border: none; cursor: pointer; letter-spacing: 0.03em; box-shadow: 0 2px 6px rgba(124, 58, 237, 0.25);">
                                            + Registrar Reclamo
                                        </button>
                                    </div>
                                </form>

                                <!-- Historial de reclamos -->
                                <div style="border-top: 1px solid #f1f5f9; padding-top: 12px;">
                                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #4b5563; margin-bottom: 8px;">
                                        Historial de Reclamos
                                    </div>
                                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; max-height: 150px; overflow-y: auto;">
                                        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                            <thead>
                                                <tr style="background-color: #f8fafc; border-bottom: 1px solid #e5e7eb; color: #64748b; font-size: 11px; text-align: left;">
                                                    <th style="padding: 8px 12px; width: 105px;">Fecha</th>
                                                    <th style="padding: 8px 12px;">Problema</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($garantia->reclamos ?? [] as $reclamo)
                                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                                        <td style="padding: 8px 12px; color: #64748b; white-space: nowrap; vertical-align: top;">
                                                            {{ $reclamo->fecha ? \Illuminate\Support\Carbon::parse($reclamo->fecha)->format('Y-m-d') : $reclamo->created_at->format('Y-m-d') }}
                                                        </td>
                                                        <td style="padding: 8px 12px; color: #1e293b; vertical-align: top;">
                                                            {{ $reclamo->descripcion ?? $reclamo->descripcion_problema }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" style="padding: 14px 12px; text-align: center; color: #94a3b8; font-style: italic;">
                                                            No hay reclamos registrados.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div style="background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e; font-size: 12px; padding: 14px; border-radius: 8px; text-align: center; margin: 10px 0;">
                                    ⚠️ Esta orden aún no cuenta con una garantía emitida. Se generará automáticamente al marcar la orden como <strong>Completada</strong>.
                                </div>
                            @endif

                            <!-- Pie -->
                            <div style="display: flex; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 12px; margin-top: 14px;">
                                <button type="button" @click="openGarantiaModal = null"
                                        style="background-color: #111827; color: #ffffff; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; padding: 7px 16px; border-radius: 8px; border: none; cursor: pointer;">
                                    Cerrar
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach

                <!-- MODALES INDEPENDIENTES DE DETALLE Y TAREAS -->
                @foreach ($ordenes as $orden)
                    @php
                        $monto = $orden->monto_total > 0 ? $orden->monto_total : ($orden->cotizacion->total ?? $orden->cotizacion->monto_total ?? 241.00);
                        $cot = $orden->cotizacion;
                        $pagosRealizados = $orden->pagos ?? collect();
                        $totalPagado = $pagosRealizados->sum('monto');
                        $saldoPendiente = $monto - $totalPagado;
                    @endphp
                    <div x-show="openModal === 'modal-{{ $orden->id }}'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-data="{ activeTab: '{{ session('abrir_orden_id') == $orden->id && session('abrir_tab') ? session('abrir_tab') : 'repuestos' }}' }">
                        <div @click.away="openModal = null" class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 text-left relative max-h-[90vh] overflow-y-auto">

                            <!-- Título del Modal -->
                            <div class="flex justify-between items-center pb-3 border-b mb-4">
                                <h3 class="text-lg font-bold text-gray-900">
                                    Detalle de Orden: <span class="text-indigo-600">{{ $orden->nro_orden }}</span>
                                </h3>
                                <button @click="openModal = null" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
                            </div>

                            <!-- Tarjeta de Información Principal Limpia -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-100 space-y-2 text-sm text-gray-700">
                                <p><strong>Nro de Orden:</strong> {{ $orden->nro_orden }}</p>
                                <p><strong>Estado Actual:</strong> <span class="uppercase font-semibold text-indigo-600">{{ str_replace('_', ' ', $orden->estado ?? 'activa') }}</span></p>
                                <p><strong>Fecha de Creación:</strong> {{ $orden->created_at->format('Y-m-d H:i') }}</p>
                                <p><strong>Cotización Origen:</strong> <span class="text-indigo-600 font-medium">{{ $cot->nro_cotizacion ?? 'COT-202611-001' }}</span></p>
                                <p><strong>Motocicleta:</strong> Yamaha (4821ABC)</p>
                            </div>

                            <!-- Actualizar Estado Directo (CU25) -->
                            <div class="bg-gray-100 p-3 rounded-md mb-4 flex items-center justify-between border border-gray-200">
                                <span class="text-xs font-bold text-gray-700 uppercase">Cambiar Estado (CU25):</span>
                                <form action="{{ route('ordenes-trabajo.updateEstado', $orden->id) }}" method="POST" class="flex space-x-2">
                                    @csrf @method('PATCH')
                                    <select name="estado" class="text-xs rounded border-gray-300 py-1">
                                        <option value="activa" {{ $orden->estado == 'activa' ? 'selected' : '' }}>Activa</option>
                                        <option value="en_proceso" {{ $orden->estado == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="completada" {{ $orden->estado == 'completada' ? 'selected' : '' }}>Completada</option>
                                    </select>
                                    <button type="submit" style="background-color: #4f46e5 !important; color: #ffffff !important;" class="px-4 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700 uppercase tracking-wider shadow-sm transition">Actualizar</button>
                                </form>
                            </div>

                            <!-- BOTONES DE PESTAÑAS ORIGINALES -->
                            <div class="flex space-x-2 border-b border-gray-200 pb-3 mb-4 overflow-x-auto">
                                <button type="button"
                                        @click="activeTab = 'repuestos'"
                                        :style="activeTab === 'repuestos' ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : 'background-color: #f3f4f6 !important; color: #374151 !important;'"
                                        class="px-3 py-2 rounded-md text-xs font-bold transition uppercase tracking-wider shadow-sm whitespace-nowrap">
                                    📦 Repuestos (CU26)
                                </button>

                                <button type="button"
                                        @click="activeTab = 'tareas'"
                                        :style="activeTab === 'tareas' ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : 'background-color: #f3f4f6 !important; color: #374151 !important;'"
                                        class="px-3 py-2 rounded-md text-xs font-bold transition uppercase tracking-wider shadow-sm whitespace-nowrap">
                                    🛠️ Tareas (CU24)
                                </button>

                                <button type="button"
                                        @click="activeTab = 'pagos'"
                                        :style="activeTab === 'pagos' ? 'background-color: #059669 !important; color: #ffffff !important;' : 'background-color: #f3f4f6 !important; color: #374151 !important;'"
                                        class="px-3 py-2 rounded-md text-xs font-bold transition uppercase tracking-wider shadow-sm whitespace-nowrap">
                                    💳 Pagos (CU22)
                                </button>

                                <button type="button"
                                        @click="activeTab = 'historial_pagos'"
                                        :style="activeTab === 'historial_pagos' ? 'background-color: #059669 !important; color: #ffffff !important;' : 'background-color: #f3f4f6 !important; color: #374151 !important;'"
                                        class="px-3 py-2 rounded-md text-xs font-bold transition uppercase tracking-wider shadow-sm whitespace-nowrap">
                                    📋 Historial Pagos
                                </button>
                            </div>

                            <!-- PESTAÑA 1: RECURSOS / REPUESTOS (CU26) -->
                            <div x-show="activeTab === 'repuestos'" class="space-y-4">
                                <div class="bg-blue-50/50 border border-blue-100 rounded-lg p-4">
                                    <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-3">🔧 CU26: Registrar Repuestos en la Orden</h4>
                                    <form action="{{ route('ordenes-trabajo.repuestos.store', $orden->id) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Seleccionar Repuesto:</label>
                                                <select name="repuesto_id" required class="w-full text-xs rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="">-- Seleccionar Repuesto --</option>
                                                    @foreach($repuestosDisponibles ?? [] as $rep)
                                                        <option value="{{ $rep->id }}">{{ $rep->nombre }} (Stock: {{ $rep->stock_actual }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Cantidad:</label>
                                                <input type="number" name="cantidad" min="1" value="1" required class="w-full text-xs rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                        </div>

                                        <div class="flex justify-end pt-1">
                                            <button type="submit" style="background-color: #4f46e5 !important; color: #ffffff !important;" class="px-4 py-2 text-xs font-semibold rounded-md hover:bg-indigo-700 uppercase tracking-wider shadow-sm transition">
                                                + Adjuntar Repuesto
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Listado de Repuestos Adjuntos -->
                                    <div class="mt-4 pt-3 border-t border-blue-200">
                                        <h5 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-2">📦 Repuestos Utilizados en esta Orden:</h5>
                                        <div class="bg-white rounded border border-blue-100 overflow-hidden">
                                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                                <thead class="bg-gray-50 text-gray-500">
                                                    <tr>
                                                        <th class="px-3 py-1.5 text-left">Repuesto</th>
                                                        <th class="px-3 py-1.5 text-center">Cantidad</th>
                                                        <th class="px-3 py-1.5 text-right">P. Unitario</th>
                                                        <th class="px-3 py-1.5 text-right">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 text-gray-700">
                                                    @php
                                                        $repuestosUsados = DB::table('uso_repuestos')
                                                            ->join('repuestos', 'uso_repuestos.repuesto_id', '=', 'repuestos.id')
                                                            ->where('uso_repuestos.orden_trabajo_id', $orden->id)
                                                            ->select('repuestos.nombre', 'uso_repuestos.cantidad', 'uso_repuestos.precio_unitario')
                                                            ->get();
                                                    @endphp
                                                    @forelse($repuestosUsados as $ru)
                                                        <tr>
                                                            <td class="px-3 py-2 font-medium">{{ $ru->nombre }}</td>
                                                            <td class="px-3 py-2 text-center">{{ $ru->cantidad }}</td>
                                                            <td class="px-3 py-2 text-right">${{ number_format($ru->precio_unitario, 2) }}</td>
                                                            <td class="px-3 py-2 text-right font-semibold">${{ number_format($ru->cantidad * $ru->precio_unitario, 2) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="px-3 py-2 text-center text-gray-400 italic">
                                                                No hay repuestos adjuntos todavía.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PESTAÑA 2: TAREAS Y MECÁNICOS (CU24) -->
                            <div x-show="activeTab === 'tareas'" class="space-y-4" x-cloak>
                                <div class="bg-indigo-50/50 border border-indigo-100 rounded-lg p-4">
                                    <h4 class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-3">🛠️ CU24: Asignar Tarea Específica a Mecánico</h4>
                                    <form action="{{ route('tareas-servicios.store') }}" method="POST" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="orden_trabajo_id" value="{{ $orden->id }}">

                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Seleccionar Mecánico:</label>
                                            <select name="empleado_id" required class="w-full text-xs rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">-- Seleccionar Mecánico --</option>
                                                @foreach($empleados ?? [] as $emp)
                                                    <option value="{{ $emp->id }}">{{ $emp->nombre ?? $emp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Descripción de la Subtarea / Trabajo:</label>
                                            <input type="text" name="descripcion" placeholder="Ej: Desarme y revisión de culata" required class="w-full text-xs rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Costo / Precio Mano de Obra ($):</label>
                                            <input type="number" step="0.01" name="precio_mano_obra" placeholder="0.00" value="0.00" required class="w-full text-xs rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>

                                        <div class="flex justify-end pt-1">
                                            <button type="submit" style="background-color: #4f46e5 !important; color: #ffffff !important;" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700 uppercase tracking-wider shadow-sm transition">
                                                + Asignar Tarea
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Listado de Tareas Asignadas -->
                                    <div class="mt-4 pt-3 border-t border-indigo-200">
                                        <h5 class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2">📋 Subtareas Delegadas en esta Orden:</h5>
                                        <div class="bg-white rounded border border-indigo-100 overflow-hidden">
                                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                                <thead class="bg-gray-50 text-gray-500">
                                                    <tr>
                                                        <th class="px-3 py-1.5 text-left">Mecánico</th>
                                                        <th class="px-3 py-1.5 text-left">Subtarea</th>
                                                        <th class="px-3 py-1.5 text-center">Estado</th>
                                                        <th class="px-3 py-1.5 text-right">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 text-gray-700">
                                                    @forelse($orden->tareasServicio ?? [] as $ts)
                                                        @php
                                                            $estadoActual = strtoupper(trim($ts->estado ?? 'PENDIENTE'));

                                                            $badgeClass = match ($estadoActual) {
                                                                'EN PROCESO' => 'bg-blue-100 text-blue-800',
                                                                'COMPLETADA' => 'bg-green-100 text-green-800',
                                                                default => 'bg-yellow-100 text-yellow-800',
                                                            };

                                                            $siguienteEstado = match ($estadoActual) {
                                                                'PENDIENTE' => 'EN PROCESO',
                                                                'EN PROCESO' => 'COMPLETADA',
                                                                default => null,
                                                            };

                                                            $textoBoton = match ($estadoActual) {
                                                                'PENDIENTE' => 'Iniciar',
                                                                'EN PROCESO' => 'Completar',
                                                                default => null,
                                                            };
                                                        @endphp
                                                        <tr>
                                                            <td class="px-3 py-2 font-medium">{{ $ts->empleado->nombre ?? $ts->empleado->name ?? 'Mecánico' }}</td>
                                                            <td class="px-3 py-2">{{ $ts->descripcion ?? $ts->nombre ?? $ts->titulo ?? $ts->detalle ?? 'Sin descripción' }}</td>
                                                            <td class="px-3 py-2 text-center">
                                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $badgeClass }} uppercase">
                                                                    {{ $estadoActual }}
                                                                </span>
                                                            </td>
                                                            <td class="px-3 py-2 text-right whitespace-nowrap space-x-2">
                                                                @if($siguienteEstado)
                                                                    <form action="{{ route('tareas-servicios.estado', $ts->id) }}" method="POST" class="inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="estado" value="{{ $siguienteEstado }}">
                                                                        <button type="submit"
                                                                            style="{{ $siguienteEstado === 'EN PROCESO' ? 'background-color: #2563eb !important; color: #ffffff !important;' : 'background-color: #059669 !important; color: #ffffff !important;' }}"
                                                                            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider shadow-2xs transition">
                                                                            {{ $textoBoton }}
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <span class="text-green-700 font-bold text-[10px] uppercase">✓ Lista</span>
                                                                @endif

                                                                <form action="{{ route('tareas-servicios.destroy', $ts->id) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Eliminar</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="px-3 py-2 text-center text-gray-400 italic">
                                                                No hay subtareas asignadas todavía.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PESTAÑA 3: REGISTRAR PAGOS (CU22) -->
                            <div x-show="activeTab === 'pagos'" class="space-y-4" x-cloak>
                                <div class="bg-emerald-50/50 border border-emerald-100 rounded-lg p-4">
                                    <h4 class="text-xs font-bold text-emerald-900 uppercase tracking-wider mb-3">💳 CU22: Registrar Pago de Cliente</h4>

                                    <!-- Resumen de Saldos Compacto -->
                                    <div class="grid grid-cols-3 gap-3 mb-4 bg-white p-3 rounded-lg border border-emerald-200 text-center shadow-2xs">
                                        <div>
                                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total</span>
                                            <span class="text-sm font-bold text-gray-800">$ {{ number_format($monto, 2) }}</span>
                                        </div>
                                        <div class="border-x px-2">
                                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Pagado</span>
                                            <span class="text-sm font-bold text-emerald-600">$ {{ number_format($totalPagado, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Pendiente</span>
                                            <span class="text-sm font-bold text-red-600">$ {{ number_format($saldoPendiente, 2) }}</span>
                                        </div>
                                    </div>

                                    @if($saldoPendiente > 0)
                                        <form action="{{ route('ordenes-trabajo.pagos.store', $orden->id) }}" method="POST" class="space-y-3 bg-white p-4 rounded-lg border border-emerald-200 shadow-2xs">
                                            <h5 class="text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2">➕ Registrar Nuevo Cobro</h5>
                                            @csrf
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Monto a Pagar ($):</label>
                                                    <input type="number" step="0.01" name="monto" max="{{ $saldoPendiente }}" value="{{ $saldoPendiente }}" required class="w-full text-xs rounded-md border-gray-300 shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Método de Pago:</label>
                                                    <select name="metodo" required class="w-full text-xs rounded-md border-gray-300 shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                                                        <option value="efectivo">Efectivo</option>
                                                        <option value="qr">QR</option>
                                                        <option value="transferencia">Transferencia Bancaria</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Nro de Referencia / Comprobante (Opcional):</label>
                                                <input type="text" name="referencia" placeholder="Ej: TRF-987654 o QR-01" class="w-full text-xs rounded-md border-gray-300 shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                                            </div>

                                            <div class="flex justify-end pt-2">
                                                <button type="submit" style="background-color: #059669 !important; color: #ffffff !important;" class="px-5 py-2 text-xs font-semibold rounded-md hover:bg-emerald-700 uppercase tracking-wider shadow-sm transition">
                                                    + Registrar Cobro
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="bg-emerald-100 text-emerald-800 text-xs p-4 rounded text-center font-bold">
                                            🎉 Esta orden se encuentra totalmente pagada.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- PESTAÑA 4: HISTORIAL DE PAGOS -->
                            <div x-show="activeTab === 'historial_pagos'" class="space-y-4" x-cloak>
                                <div class="bg-emerald-50/50 border border-emerald-100 rounded-lg p-4">
                                    <h4 class="text-xs font-bold text-emerald-900 uppercase tracking-wider mb-3">📋 Historial de Pagos Registrados</h4>
                                    <div class="bg-white rounded-lg border border-emerald-200 overflow-hidden shadow-2xs">
                                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                                            <thead class="bg-gray-50 text-gray-500">
                                                <tr>
                                                    <th class="px-4 py-2 text-left">Fecha</th>
                                                    <th class="px-4 py-2 text-left">Método</th>
                                                    <th class="px-4 py-2 text-left">Referencia</th>
                                                    <th class="px-4 py-2 text-right">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 text-gray-700">
                                                @forelse($pagosRealizados as $pago)
                                                    <tr>
                                                        <td class="px-4 py-2.5">{{ $pago->created_at->format('Y-m-d H:i') }}</td>
                                                        <td class="px-4 py-2.5 uppercase font-semibold text-emerald-800">{{ $pago->metodo }}</td>
                                                        <td class="px-4 py-2.5">{{ $pago->observaciones ?? $pago->nro_comprobante ?? '-' }}</td>
                                                        <td class="px-4 py-2.5 text-right font-bold text-emerald-600">$ {{ number_format($pago->monto, 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="px-4 py-6 text-center text-gray-400 italic">
                                                            No hay pagos registrados todavía para esta orden.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de Desglose Financiero -->
                            <div class="mt-6 mb-6">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Desglose Financiero</h4>
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-2xs">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                                            <tr>
                                                <th class="px-4 py-2 text-left">Concepto</th>
                                                <th class="px-4 py-2 text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 text-gray-800">
                                            <tr>
                                                <td class="px-4 py-2.5 font-medium">Monto Total de la Orden (Servicios y Repuestos)</td>
                                                <td class="px-4 py-2.5 text-right font-bold text-emerald-600">${{ number_format($monto, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Botón de Cerrar -->
                            <div class="flex justify-end pt-2">
                                <button @click="openModal = null" style="background-color: #111827 !important; color: #ffffff !important;" class="px-5 py-2 text-xs font-semibold rounded-md uppercase tracking-wider shadow-sm hover:bg-gray-800">
                                    Cerrar
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</x-app-layout>