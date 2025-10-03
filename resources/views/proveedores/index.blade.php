@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@section('content')
<main class="container mx-auto p-6 lg:p-12" x-data="providers()">

    
    <div class="flex flex-col lg:flex-row gap-6">

        
        <div class="lg:w-2/3 bg-white p-6 rounded-xl shadow-lg border border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-gray-800">Lista de Proveedores</h2>
                <input type="text" x-model="searchProvider" placeholder="Buscar Proveedor..." class="w-full md:max-w-xs pl-4 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200">
            </div>
            
            <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs sm:text-sm font-semibold">
                        <tr>
                            <th class="py-4 px-6 text-left">Proveedor</th>
                            <th class="py-4 px-6 text-left">Teléfono</th>
                            <th class="py-4 px-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm font-light divide-y divide-gray-200">
                        <template x-for="provider in filteredProviders" :key="provider.id">
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-3 px-6 text-left font-medium" x-text="provider.name"></td>
                                <td class="py-3 px-6 text-left" x-text="provider.phone"></td>
                                <td class="py-3 px-6 text-center">
                                    <button @click="openInvoiceModal(provider)" class="bg-gray-200 text-gray-800 text-xs font-semibold px-3 py-1.5 rounded-full hover:bg-gray-300 transition-colors">Ver Facturas</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="lg:w-1/3 bg-white p-6 rounded-xl shadow-lg border border-gray-200 lg:h-fit lg:sticky lg:top-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Registrar Nuevo Proveedor</h2>
            <div class="space-y-4">
                <div>
                    <label for="newProviderName" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="newProviderName" x-model="newProviderName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-colors">
                </div>
                <div>
                    <label for="newProviderPhone" class="block text-sm font-medium text-gray-700">Número de Teléfono</label>
                    <input type="text" id="newProviderPhone" x-model="newProviderPhone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-colors">
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="saveNewProvider()" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-md">Guardar</button>
            </div>
        </div>
    </div>
    
    
    <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" @click.away="showModal = false" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl overflow-y-auto max-h-[90vh]" @click.stop>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Facturas de <span x-text="currentProvider.name"></span></h3>
                <button @click="showModal = false" class="text-gray-500 hover:text-gray-800 text-3xl transition-colors">&times;</button>
            </div>
            
            <div x-show="currentProvider.invoices && currentProvider.invoices.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="invoice in currentProvider.invoices" :key="invoice.id">
                    <div class="bg-gray-100 p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                        <img :src="invoice.url" :alt="'Factura ' + invoice.id" class="w-full h-auto mb-4 rounded-lg border border-gray-300">
                        <button @click.prevent class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">Imprimir</button>
                    </div>
                </template>
            </div>

            <div x-show="!currentProvider.invoices || currentProvider.invoices.length === 0" class="text-center text-gray-500 py-10">
                <p>No hay facturas disponibles para este proveedor.</p>
            </div>
        </div>
    </div>

    
    <div x-show="showNotification" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed bottom-5 right-5 p-4 rounded-xl shadow-2xl text-white z-50 transform transition-all" :class="notification.success ? 'bg-green-600' : 'bg-red-600'">
        <span x-text="notification.message"></span>
    </div>
</main>

@endsection

@section('scripts')
<script>
function providers() {
return {
providers: [
{ id: 1, name: 'Maxi Consumo', phone: '03754867429', invoices: [{ id: 1, url: 'https://placehold.co/400x500/F5F5F5/000?text=Factura+1' }, { id: 2, url: 'https://placehold.co/400x500/F5F5F5/000?text=Factura+2' }] },
{ id: 2, name: 'INDUQUIMIKA', phone: '03758948632', invoices: [] },
{ id: 3, name: 'MAJORISTA Vini', phone: '03756984372', invoices: [{ id: 3, url: 'https://placehold.co/400x500/F5F5F5/000?text=Factura+3' }] },
],
searchProvider: '',
newProviderName: '',
newProviderPhone: '',
showModal: false,
currentProvider: {},
showNotification: false,
notification: { message: '', success: true },

            get filteredProviders() {
                if (this.searchProvider === '') {
                    return this.providers;
                }
                const searchTerm = this.searchProvider.toLowerCase();
                return this.providers.filter(p => p.name.toLowerCase().includes(searchTerm));
            },

            saveNewProvider() {
                if (!this.newProviderName || !this.newProviderPhone) {
                    this.notify('Todos los campos son obligatorios.', false);
                    return;
                }
                const newId = this.providers.length ? Math.max(...this.providers.map(p => p.id)) + 1 : 1;
                const newProvider = {
                    id: newId,
                    name: this.newProviderName,
                    phone: this.newProviderPhone,
                    invoices: []
                };
                this.providers.push(newProvider);
                this.notify('Proveedor añadido con éxito.');
                this.newProviderName = '';
                this.newProviderPhone = '';
            },
            
            openInvoiceModal(provider) {
                this.currentProvider = provider;
                this.showModal = true;
            },

            notify(message, success = true) {
                this.notification = { message, success };
                this.showNotification = true;
                setTimeout(() => this.showNotification = false, 3000);
            }
        }
    }
</script>

@endsection