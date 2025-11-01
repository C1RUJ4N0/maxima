function appTPV() {
    return {
        // State
        cargandoInicial: true, cargando: false, productos: [], clientes: [], carrito: [],
        pestañaActiva: 'Inventario', busqueda: '', clienteSeleccionadoId: null, montoRecibido: null,
        metodoPago: 'efectivo', 
        modalActivo: null,
        nuevoCliente: { nombre: '', telefono: '', email: '' },
        nuevoApartado: { monto_pagado: null, fecha_vencimiento: '' },
        nuevoProducto: { nombre: '', precio: null, existencias: null },
        nuevoProveedor: { nombre: '', telefono: '', email: '', descripcion: '' },
        nuevaFactura: { numero_factura: '', monto: null, fecha_emision: '', imagen_factura: null },
        clienteGeneralId: null,
        mostrarNotificacion: false, notificacion: { mensaje: '', exito: true },
        estadisticas: { ventasHoy: 0, ventasMes: 0, egresos: 0, productosBajoStock: [], apartadosVigentes: [] },
        proveedores: [],
        proveedorSeleccionado: null,
        apartados: [],
        
        // --- INICIO CAMBIO PAGINACIÓN ---
        ventas: [],
        ventasTotalAmount: 0, // Esto será el total de la PÁGINA
        ventasPaginacion: {
            current_page: 1,
            last_page: 1,
            total: 0
        },
        // --- FIN CAMBIO PAGINACIÓN ---
        
        productoEditando: { id: null, nombre: '', precio: null, existencias: null },
        apartadoEditando: { id: null, nombre_cliente: '', monto_total: 0, monto_pagado: 0, fecha_vencimiento: '', estado: 'vigente' },
        proveedorEditando: { id: null, nombre: '', telefono: '', email: '', descripcion: '' },
        ventaEditando: { id: null, metodo_pago: '' },
        facturaEditando: { id: null, numero_factura: '', monto: 0, fecha_emision: '', estado: 'pendiente', imagen_url: '', nueva_imagen: null },
        
        pestañas: [
            { nombre: 'Inventario', icono: 'fas fa-cash-register' }, 
            { nombre: 'Ventas', icono: 'fas fa-receipt' },
            { nombre: 'Estadísticas', icono: 'fas fa-chart-line' },
            { nombre: 'Proveedores', icono: 'fas fa-truck-fast' }, 
            { nombre: 'Apartados', icono: 'fas fa-inbox' },
        ],

        async init() {
            this.cargandoInicial = true;
            try {
                await this.obtenerClientes(); 
                await this.obtenerEstadisticas();
            } catch (error) {
                console.error("Error fatal al iniciar la aplicación:", error);
                this.notificar("No se pudo conectar con el servidor.", false);
            } finally {
                this.cargandoInicial = false;
            }
        },

        get totalVenta() { return this.carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0).toFixed(2); },
        get textoCambio() {
            if (this.montoRecibido === null || this.montoRecibido < this.totalVenta) return `$0.00`;
            return `$${(this.montoRecibido - this.totalVenta).toFixed(2)}`;
        },
        esClienteGeneral() { return this.clienteSeleccionadoId == this.clienteGeneralId; },

        async fetchAPI(endpoint, options = {}) {
            this.cargando = true;
            try {
                if (!(options.body instanceof FormData)) {
                     options.headers = {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                         ...options.headers,
                    };
                } else {
                     options.headers = {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                         ...options.headers,
                    };
                }

                const response = await fetch(`/api${endpoint}`, options);
                if (response.status === 204) {
                    return { success: true, message: 'Recurso eliminado.' }; 
                }
                const data = await response.json();
                if (!response.ok) {
                    const error = new Error(data.message || `Error ${response.status}`);
                    error.errors = data.errors;
                    throw error;
                }
                return data;
            } catch (error) {
                console.error('Error en fetchAPI:', error);
                if (error.errors) {
                    this.notificar(Object.values(error.errors)[0][0], false);
                } else {
                    this.notificar(error.message || 'Error de comunicación con la API.', false);
                }
                throw error;
            } finally { this.cargando = false; }
        },

        async obtenerClientes() {
            const data = await this.fetchAPI('/inventario/clientes');
            if (data && Array.isArray(data.clientes)) {
                this.clientes = data.clientes;
                if (data.clienteGeneralId) {
                    this.clienteGeneralId = data.clienteGeneralId;
                    if (this.clienteSeleccionadoId === null) {
                        this.$nextTick(() => {
                            this.clienteSeleccionadoId = data.clienteGeneralId;
                        });
                    }
                }
            } else { throw new Error("La respuesta de la API de clientes no tiene el formato esperado."); }
        },
        async buscarProductos() {
            if (this.busqueda.trim() === '') { this.productos = []; return; }
            const data = await this.fetchAPI(`/inventario/productos?q=${this.busqueda}`);
            this.productos = data.productos || [];
        },
        async obtenerEstadisticas() {
            const data = await this.fetchAPI('/estadisticas');
            this.estadisticas = { ...data, loaded: true };
        },
        async obtenerProveedores() {
            const data = await this.fetchAPI('/proveedores');
            this.proveedores = data.proveedores;
            this.proveedorSeleccionado = null; 
        },
        async obtenerApartados() {
            const data = await this.fetchAPI('/apartados');
            this.apartados = data.sort((a, b) => a.id - b.id);
        },
        
        // --- INICIO CAMBIO PAGINACIÓN ---
        async obtenerVentas(page = 1) {
            // Pedimos la página específica
            const data = await this.fetchAPI(`/ventas?page=${page}`);
            
            // data ahora es { paginacion: {...}, montoTotalPagina: Y }
            this.ventas = data.paginacion.data; // Ya vienen ordenadas por fecha desde el backend
            this.ventasTotalAmount = data.montoTotalPagina;
            
            // Guardamos la info de paginación
            this.ventasPaginacion = {
                current_page: data.paginacion.current_page,
                last_page: data.paginacion.last_page,
                total: data.paginacion.total
            };
        },
        // --- FIN CAMBIO PAGINACIÓN ---
        
        async guardarNuevoCliente() {
            try {
                const data = await this.fetchAPI('/inventario/clientes', { method: 'POST', body: JSON.stringify(this.nuevoCliente) });
                this.notificar(`Cliente '${data.cliente.nombre}' añadido.`);
                this.modalActivo = null;
                await this.obtenerClientes();
                this.clienteSeleccionadoId = data.cliente.id; 
            } catch (error) { /* Ya notificado */ }
        },
        async guardarNuevoApartado() {
            try {
                const apartadoData = { 
                    cliente_id: this.clienteSeleccionadoId, 
                    monto_total: this.totalVenta, 
                    monto_pagado: this.nuevoApartado.monto_pagado, fecha_vencimiento: this.nuevoApartado.fecha_vencimiento, 
                    items: this.carrito.map(p => ({ id: p.id, cantidad: p.cantidad })) 
                };
                await this.fetchAPI('/apartados', { method: 'POST', body: JSON.stringify(apartadoData) });
                this.notificar('Apartado creado exitosamente.');
                this.modalActivo = null;
                this.restablecerVenta();
                if(this.pestañaActiva === 'Apartados') await this.obtenerApartados();
                await this.obtenerEstadisticas();
            } catch (error) { /* Ya notificado */ }
        },
        async guardarNuevoProducto() {
            try {
                const productoCreado = await this.fetchAPI('/inventario', { method: 'POST', body: JSON.stringify(this.nuevoProducto) });
                this.notificar(`Producto '${productoCreado.nombre}' añadido.`);
                this.modalActivo = null;
                this.buscarProductos(); 
            } catch (error) { /* Ya notificado */ }
        },
        async finalizarVenta() {
            if (this.clienteSeleccionadoId === null) {
                this.notificar('Error: No hay ningún cliente seleccionado.', false);
                return;
            }
            const ventaData = {
                carrito: this.carrito.map(item => ({ id: item.id, cantidad: item.cantidad, precio_venta: item.precio })),
                cliente_id: this.clienteSeleccionadoId,
                monto_recibido: this.montoRecibido,
                metodo_pago: this.metodoPago
            };
            try {
                const resultado = await this.fetchAPI('/ventas', { method: 'POST', body: JSON.stringify(ventaData) });
                this.notificar(resultado.message || 'Venta finalizada con éxito.', true);
                this.restablecerVenta();
                await this.obtenerEstadisticas();
            } catch (error) { console.error("Error al finalizar la venta:", error); }
        },
        
        async cambiarPestaña(pestaña) {
            this.pestañaActiva = pestaña;
            try {
                if (pestaña === 'Estadísticas') await this.obtenerEstadisticas();
                if (pestaña === 'Proveedores') await this.obtenerProveedores();
                if (pestaña === 'Apartados') await this.obtenerApartados();
                // --- INICIO CAMBIO PAGINACIÓN (Pedir página 1 al cambiar) ---
                if (pestaña === 'Ventas') await this.obtenerVentas(1); 
                // --- FIN CAMBIO PAGINACIÓN ---
            } catch (error) { /* Ya notificado */ }
        },
        
        // --- INICIO CAMBIO PAGINACIÓN (Nuevas funciones) ---
        async ventasPaginaSiguiente() {
            if (this.ventasPaginacion.current_page < this.ventasPaginacion.last_page) {
                await this.obtenerVentas(this.ventasPaginacion.current_page + 1);
            }
        },
        async ventasPaginaAnterior() {
            if (this.ventasPaginacion.current_page > 1) {
                await this.obtenerVentas(this.ventasPaginacion.current_page - 1);
            }
        },
        // --- FIN CAMBIO PAGINACIÓN ---

        iniciarEdicionProducto(producto) {
            this.productoEditando = { ...producto }; 
            this.abrirModal('editarProducto');
        },
        async guardarEdicionProducto() {
            try {
                const data = await this.fetchAPI(`/inventario/${this.productoEditando.id}`, {
                    method: 'PUT',
                    body: JSON.stringify(this.productoEditando)
                });
                this.notificar(data.message || 'Producto actualizado.');
                this.modalActivo = null;
                await this.buscarProductos(); 
            } catch (error) { /* Ya notificado */ }
        },
        confirmarEliminarProducto(id) {
            if (confirm('¿Seguro que quieres eliminar este producto? Esta acción es irreversible.')) {
                this.eliminarProducto(id);
            }
        },
        async eliminarProducto(id) {
            try {
                const data = await this.fetchAPI(`/inventario/${id}`, { method: 'DELETE' });
                this.notificar(data.message || 'Producto eliminado.');
                await this.buscarProductos();
            } catch (error) { /* Ya notificado */ }
        },

        iniciarEdicionApartado(apartado) {
            this.apartadoEditando = { ...apartado };
            this.apartadoEditando.fecha_vencimiento = apartado.fecha_vencimiento.split('T')[0];
            this.abrirModal('editarApartado');
        },
        async guardarEdicionApartado() {
            try {
                const data = await this.fetchAPI(`/apartados/${this.apartadoEditando.id}`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        monto_pagado: this.apartadoEditando.monto_pagado,
                        fecha_vencimiento: this.apartadoEditando.fecha_vencimiento,
                        estado: this.apartadoEditando.estado
                    })
                });
                this.notificar(data.message || 'Apartado actualizado.');
                this.modalActivo = null;
                await this.obtenerApartados();
                await this.obtenerEstadisticas(); 
            } catch (error) { /* Ya notificado */ }
        },
        confirmarEliminarApartado(id) {
            if (confirm('¿Seguro que quieres eliminar este apartado? El stock será devuelto si el estado era "vigente" o "cancelado".')) {
                this.eliminarApartado(id);
            }
        },
        async eliminarApartado(id) {
            try {
                const data = await this.fetchAPI(`/apartados/${id}`, { method: 'DELETE' });
                this.notificar(data.message || 'Apartado eliminado.');
                await this.obtenerApartados();
                await this.obtenerEstadisticas(); 
            } catch (error) { /* Ya notificado */ }
        },

        async seleccionarProveedor(id) {
            try {
                const data = await this.fetchAPI(`/proveedores/${id}`);
                this.proveedorSeleccionado = data;
            } catch (error) { /* Ya notificado */ }
        },
        async guardarNuevoProveedor() {
            try {
                const proveedorData = { nombre: this.nuevoProveedor.nombre, telefono: this.nuevoProveedor.telefono, email: this.nuevoProveedor.email, descripcion: this.nuevoProveedor.descripcion };
                const data = await this.fetchAPI('/proveedores', { method: 'POST', body: JSON.stringify(proveedorData) });
                this.notificar(`Proveedor '${data.nombre}' añadido.`);
                this.modalActivo = null;
                await this.obtenerProveedores();
                this.seleccionarProveedor(data.id);
            } catch (error) {
                /* Ya notificado por fetchAPI */
            }
        },
        iniciarEdicionProveedor(proveedor) {
            this.proveedorEditando = { ...proveedor };
            this.abrirModal('editarProveedor');
        },
        async guardarEdicionProveedor() {
            try {
                const data = await this.fetchAPI(`/proveedores/${this.proveedorEditando.id}`, {
                    method: 'PUT',
                    body: JSON.stringify(this.proveedorEditando)
                });
                this.notificar(data.message || 'Proveedor actualizado.');
                this.modalActivo = null;
                await this.obtenerProveedores(); 
            } catch (error) { /* Ya notificado */ }
        },
        confirmarEliminarProveedor(id) {
            if (confirm('¿Seguro que quieres eliminar este proveedor? Todas sus facturas asociadas también serán eliminadas.')) {
                this.eliminarProveedor(id);
            }
        },
        async eliminarProveedor(id) {
            try {
                const data = await this.fetchAPI(`/proveedores/${id}`, { method: 'DELETE' });
                this.notificar(data.message || 'Proveedor eliminado.');
                await this.obtenerProveedores(); 
            } catch (error) { /* Ya notificado */ }
        },

        async guardarNuevaFactura() {
            try {
                const formData = new FormData();
                formData.append('numero_factura', this.nuevaFactura.numero_factura);
                formData.append('monto', this.nuevaFactura.monto);
                formData.append('fecha_emision', this.nuevaFactura.fecha_emision);
                
                const inputFile = document.querySelector('#factura_imagen');
                if (inputFile.files.length > 0) {
                    formData.append('imagen_factura', inputFile.files[0]);
                }
                
                this.cargando = true;
                const response = await fetch(`/api/proveedores/${this.proveedorSeleccionado.id}/facturas`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                this.cargando = false;
                const data = await response.json();
                
                if (!response.ok) {
                     const error = new Error(data.message || `Error ${response.status}`);
                     error.errors = data.errors;
                     throw error;
                }

                this.notificar(`Factura #${data.numero_factura} añadida.`);
                this.modalActivo = null;
                await this.seleccionarProveedor(this.proveedorSeleccionado.id);

            } catch (error) { 
                this.cargando = false;
                if (error.errors) this.notificar(Object.values(error.errors)[0][0], false);
                else this.notificar(error.message, false);
            }
        },
        
        iniciarEdicionFactura(factura) {
            this.facturaEditando = { ...factura };
            this.facturaEditando.fecha_emision = factura.fecha_emision.split('T')[0];
            this.facturaEditando.nueva_imagen = null; 
            this.abrirModal('editarFactura');
        },
        
        async guardarEdicionFactura() {
            try {
                const formData = new FormData();
                formData.append('numero_factura', this.facturaEditando.numero_factura);
                formData.append('monto', this.facturaEditando.monto);
                formData.append('fecha_emision', this.facturaEditando.fecha_emision);
                formData.append('estado', this.facturaEditando.estado);
                
                const inputFile = document.querySelector('#edit_factura_imagen');
                if (inputFile.files.length > 0) {
                    formData.append('imagen_factura', inputFile.files[0]);
                }
                
                formData.append('_method', 'PUT');

                this.cargando = true;
                const response = await fetch(`/api/facturas/${this.facturaEditando.id}`, {
                    method: 'POST', 
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                this.cargando = false;
                const data = await response.json();
                
                if (!response.ok) {
                     const error = new Error(data.message || `Error ${response.status}`);
                     error.errors = data.errors;
                     throw error;
                }

                this.notificar(data.message || 'Factura actualizada.');
                this.modalActivo = null;
                await this.seleccionarProveedor(this.proveedorSeleccionado.id); 
                await this.obtenerEstadisticas(); 

            } catch (error) { 
                this.cargando = false;
                if (error.errors) this.notificar(Object.values(error.errors)[0][0], false);
                else this.notificar(error.message, false);
            }
        },
        confirmarEliminarFactura(id) {
            if (confirm('¿Seguro que quieres eliminar esta factura? Solo puedes eliminar facturas "pendientes".')) {
                this.eliminarFactura(id);
            }
        },
        async eliminarFactura(id) {
            try {
                const data = await this.fetchAPI(`/facturas/${id}`, { method: 'DELETE' });
                this.notificar(data.message || 'Factura eliminada.');
                await this.seleccionarProveedor(this.proveedorSeleccionado.id); 
            } catch (error) { /* Ya notificado */ }
        },

        iniciarEdicionVenta(venta) {
            this.ventaEditando.id = venta.id;
            this.ventaEditando.metodo_pago = venta.metodo_pago;
            this.abrirModal('editarVenta');
        },
        async guardarEdicionVenta() {
            try {
                const data = await this.fetchAPI(`/ventas/${this.ventaEditando.id}`, {
                    method: 'PUT',
                    body: JSON.stringify({ metodo_pago: this.ventaEditando.metodo_pago })
                });
                this.notificar(data.message || 'Método de pago actualizado.');
                this.modalActivo = null;
                await this.obtenerVentas(this.ventasPaginacion.current_page); // Recargar la página actual
            } catch (error) { /* Ya notificado */ }
        },
        confirmarEliminarVenta(id) {
            if (confirm('¿Seguro que quieres eliminar esta venta? Esta acción es irreversible y devolverá el stock al inventario.')) {
                this.eliminarVenta(id);
            }
        },
        async eliminarVenta(id) {
            try {
                const data = await this.fetchAPI(`/ventas/${id}`, { method: 'DELETE' });
                this.notificar(data.message || 'Venta eliminada.');
                await this.obtenerVentas(this.ventasPaginacion.current_page); // Recargar la página actual
                await this.obtenerEstadisticas();
            } catch (error) { /* Ya notificado */ }
        },

        abrirModal(tipo) {
            if (tipo === 'cliente') this.nuevoCliente = { nombre: '', telefono: '', email: '' };
            if (tipo === 'apartado') this.nuevoApartado = { monto_pagado: null, fecha_vencimiento: '' };
            if (tipo === 'producto') this.nuevoProducto = { nombre: '', precio: null, existencias: null };
            if (tipo === 'proveedor') this.nuevoProveedor = { nombre: '', telefono: '', email: '', descripcion: '' };
            if (tipo === 'factura') {
                this.nuevaFactura = { numero_factura: '', monto: null, fecha_emision: '', imagen_factura: null };
                const inputFile = document.querySelector('#factura_imagen');
                if(inputFile) inputFile.value = '';
            }
            if (tipo === 'editarFactura') {
                const editInputFile = document.querySelector('#edit_factura_imagen');
                if(editInputFile) editInputFile.value = '';
            }
            this.modalActivo = tipo;
        },
        añadirACarrito(producto) {
            const item = this.carrito.find(i => i.id === producto.id);
            if (item) {
                if (item.cantidad < producto.existencias) item.cantidad++;
                else this.notificar('Stock máximo alcanzado', false);
            } else {
                if (producto.existencias > 0) this.carrito.push({ ...producto, cantidad: 1 });
            }
        },
        actualizarCantidad(id, qty) {
            const item = this.carrito.find(i => i.id === id);
            if (!item) return;
            const cant = parseInt(qty);
            if (cant < 1) item.cantidad = 1;
            else if (cant > item.existencias) {
                item.cantidad = item.existencias;
                this.notificar('Stock máximo alcanzado', false);
            } else item.cantidad = cant;
        },
        eliminarDeCarrito(id) { this.carrito = this.carrito.filter(i => i.id !== id); },
        restablecerVenta() {
            this.carrito = [];
            this.montoRecibido = null;
            this.clienteSeleccionadoId = this.clienteGeneralId; // REGLA DE NEGOCIO: Vuelve a Cliente General
            this.busqueda = '';
            this.productos = [];
            this.metodoPago = 'efectivo';
        },
        notificar(mensaje, exito = true) {
            this.notificacion = { mensaje, exito };
            this.mostrarNotificacion = true;
            setTimeout(() => this.mostrarNotificacion = false, 3000);
        },
    }
}