<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestión de Cotizaciones (CU19) - Nueva Cotización
        </h2>
    </x-slot>

    <div class="py-6" x-data="cotizacionApp()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <form action="{{ route('cotizaciones.store') }}" method="POST" class="space-y-6">
                @csrf

                @if($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-lg shadow-sm">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>- {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- DATOS GENERALES -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white border-b pb-2">1. Información General</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Nro. Cotización</label>
                            <input type="text" name="nro_cotizacion" value="{{ $nroCotizacion }}" readonly class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 border-gray-300 rounded-md shadow-sm text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Seleccionar Diagnóstico (CU18)</label>
                            <select name="diagnostico_id" required class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white rounded-md shadow-sm text-sm">
                                <option value="">-- Seleccione un Diagnóstico --</option>
                                @foreach($diagnosticos as $diag)
                                    <option value="{{ $diag->id }}">
                                        Moto: {{ $diag->motocicleta->marca ?? 'N/A' }} (Placa: {{ $diag->motocicleta->placa ?? 'N/A' }}) - Km: {{ $diag->kilometraje_ingreso }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Tipo de Cotización</label>
                            <select name="tipo" class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white rounded-md shadow-sm text-sm">
                                <option value="inicial">Inicial</option>
                                <option value="revisión">Revisión posterior</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Fecha de Emisión</label>
                            <input type="datetime-local" name="fecha_emision" value="{{ date('Y-m-d\TH:i') }}" required class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white rounded-md shadow-sm text-sm">
                        </div>
                    </div>
                </div>

                <!-- MANO DE OBRA / SERVICIOS -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
                    <div class="flex justify-between items-center border-b pb-3">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">2. Mano de Obra y Servicios</h3>
                        <!-- Botón con el estilo oscuro idéntico y bien visible -->
                        <button type="button" @click="addServicio()" style="background-color: #111827; color: #ffffff; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;" class="shadow hover:opacity-90 transition flex items-center gap-2">
                            + Agregar Servicio
                        </button>
                    </div>

                    <template x-for="(serv, index) in servicios" :key="index">
                        <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Servicio</label>
                                <select :name="`servicios[${index}][servicio_id]`" x-model="serv.servicio_id" class="w-full text-xs border-gray-300 rounded-md dark:bg-gray-700 dark:text-white">
                                    <option value="">-- Seleccionar Servicio --</option>
                                    @foreach($servicios as $s)
                                        <option value="{{ $s->id }}">{{ $s->nombre ?? $s->descripcion ?? 'Servicio #' . $s->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-40">
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Costo Mano Obra</label>
                                <input type="number" step="0.01" :name="`servicios[${index}][precio_mano_obra]`" x-model.number="serv.precio_mano_obra" class="w-full text-xs border-gray-300 rounded-md dark:bg-gray-700 dark:text-white" placeholder="0.00">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Observaciones</label>
                                <input type="text" :name="`servicios[${index}][observaciones]`" x-model="serv.observaciones" class="w-full text-xs border-gray-300 rounded-md dark:bg-gray-700 dark:text-white" placeholder="Detalle opcional">
                            </div>
                            <button type="button" @click="removeServicio(index)" class="text-rose-600 hover:text-rose-800 self-end mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- REPUESTOS -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
                    <div class="flex justify-between items-center border-b pb-3">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">3. Repuestos Requeridos</h3>
                        <!-- Botón con el estilo oscuro idéntico y bien visible -->
                        <button type="button" @click="addRepuesto()" style="background-color: #111827; color: #ffffff; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;" class="shadow hover:opacity-90 transition flex items-center gap-2">
                            + Agregar Repuesto
                        </button>
                    </div>

                    <template x-for="(rep, index) in repuestos" :key="index">
                        <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Repuesto</label>
                                <select :name="`repuestos[${index}][repuesto_id]`" x-model="rep.repuesto_id" class="w-full text-xs border-gray-300 rounded-md dark:bg-gray-700 dark:text-white">
                                    <option value="">-- Seleccionar Repuesto --</option>
                                    @foreach($repuestos as $r)
                                        <option value="{{ $r->id }}">{{ $r->nombre ?? 'Repuesto #' . $r->id }} (Stock: {{ $r->stock ?? 0 }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-28">
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Cantidad</label>
                                <input type="number" min="1" :name="`repuestos[${index}][cantidad]`" x-model.number="rep.cantidad" class="w-full text-xs border-gray-300 rounded-md dark:bg-gray-700 dark:text-white" placeholder="1">
                            </div>
                            <div class="w-36">
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Precio Unitario</label>
                                <input type="number" step="0.01" :name="`repuestos[${index}][precio_unitario]`" x-model.number="rep.precio_unitario" class="w-full text-xs border-gray-300 rounded-md dark:bg-gray-700 dark:text-white" placeholder="0.00">
                            </div>
                            <div class="w-32 text-right pr-2">
                                <span class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Subtotal</span>
                                <span class="text-xs font-bold text-gray-800 dark:text-white" x-text="`$ ${(rep.cantidad * rep.precio_unitario || 0).toFixed(2)}`"></span>
                            </div>
                            <button type="button" @click="removeRepuesto(index)" class="text-rose-600 hover:text-rose-800 self-end mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- RESUMEN DE TOTALES Y BOTÓN GUARDAR -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="space-y-1 text-sm text-gray-700 dark:text-gray-300 w-full md:w-auto">
                        <div>Total Mano de Obra: <span class="font-bold text-indigo-600 dark:text-indigo-400" x-text="`$ ${totalManoObra.toFixed(2)}`"></span></div>
                        <div>Total Repuestos: <span class="font-bold text-indigo-600 dark:text-indigo-400" x-text="`$ ${totalRepuestos.toFixed(2)}`"></span></div>
                        <div class="text-lg font-extrabold pt-1 border-t border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white">
                            Monto Total: <span class="text-emerald-600 dark:text-emerald-400" x-text="`$ ${montoTotal.toFixed(2)}`"></span>
                        </div>
                    </div>

                    <button type="submit" style="background-color: #111827; color: #ffffff; padding: 10px 20px; border-radius: 6px; font-weight: bold; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;" class="shadow hover:opacity-90 transition">
                        Guardar Cotización
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- SCRIPT ALPINE.JS PARA CÁLCULOS AUTOMÁTICOS -->
    <script>
        function cotizacionApp() {
            return {
                servicios: [
                    { servicio_id: '', precio_mano_obra: 0, observaciones: '' }
                ],
                repuestos: [
                    { repuesto_id: '', cantidad: 1, precio_unitario: 0 }
                ],
                addServicio() {
                    this.servicios.push({ servicio_id: '', precio_mano_obra: 0, observaciones: '' });
                },
                removeServicio(index) {
                    if(this.servicios.length > 1) {
                        this.servicios.splice(index, 1);
                    } else {
                        alert('Debe mantener al menos una línea de servicio o dejarla vacía.');
                    }
                },
                addRepuesto() {
                    this.repuestos.push({ repuesto_id: '', cantidad: 1, precio_unitario: 0 });
                },
                removeRepuesto(index) {
                    if(this.repuestos.length > 1) {
                        this.repuestos.splice(index, 1);
                    } else {
                        alert('Debe mantener al menos una línea de repuesto o dejarla vacía.');
                    }
                },
                get totalManoObra() {
                    return this.servicios.reduce((sum, s) => sum + (parseFloat(s.precio_mano_obra) || 0), 0);
                },
                get totalRepuestos() {
                    return this.repuestos.reduce((sum, r) => sum + ((parseInt(r.cantidad) || 0) * (parseFloat(r.precio_unitario) || 0)), 0);
                },
                get montoTotal() {
                    return this.totalManoObra + this.totalRepuestos;
                }
            }
        }
    </script>
</x-app-layout>