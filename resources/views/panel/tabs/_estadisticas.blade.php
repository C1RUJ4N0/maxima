<div x-show="pestañaActiva === 'Estadísticas'" class="space-y-6 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-sky-50 p-6 rounded-lg shadow-xl text-center"><h3 class="text-lg font-semibold text-gray-500">Ventas del Día</h3><p class="text-4xl font-bold mt-2" x-text="`$${parseFloat(estadisticas.ventasHoy).toFixed(2)}`"></p></div>
        <div class="bg-sky-50 p-6 rounded-lg shadow-xl text-center"><h3 class="text-lg font-semibold text-gray-500">Ventas del Mes</h3><p class="text-4xl font-bold mt-2" x-text="`$${parseFloat(estadisticas.ventasMes).toFixed(2)}`"></p></div>
        <div class="bg-sky-50 p-6 rounded-lg shadow-xl text-center"><h3 class="text-lg font-semibold text-gray-500">Egresos Totales (Pagados)</h3><p class="text-4xl font-bold mt-2 text-red-500" x-text="`$${parseFloat(estadisticas.egresos).toFixed(2)}`"></p></div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-sky-50 p-6 rounded-lg shadow-xl"><h3 class="text-xl font-bold mb-4">Productos con Bajo Stock</h3><ul class="divide-y"><template x-for="p in estadisticas.productosBajoStock" :key="p.id"><li class="py-2 flex justify-between"><span x-text="p.nombre"></span><span class="font-semibold text-red-500" x-text="`Stock: ${p.existencias}`"></span></li></template></ul></div>
        <div class="bg-sky-50 p-6 rounded-lg shadow-xl"><h3 class="text-xl font-bold mb-4">Apartados Vigentes</h3><ul class="divide-y"><template x-for="a in estadisticas.apartadosVigentes" :key="a.id"><li class="py-2"><span x-text="a.cliente_nombre"></span><div class="flex justify-between text-sm"><span class="font-semibold" x-text="`$${parseFloat(a.monto_total).toFixed(2)}`"></span><span class="text-gray-500" x-text="`Vence: ${a.fecha_vencimiento}`"></span></div></li></template></ul></div>
    </div>
</div>