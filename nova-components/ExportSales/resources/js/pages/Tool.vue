<template>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <Head title="Export Sales"/>
        <Heading class="mb-8 text-gray-900 dark:text-white">Exportar Ventas</Heading>

        <Card class="rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="p-6 space-y-6">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-5">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Configurar Exportación</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Seleccione los parámetros para generar el
                        reporte</p>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Fecha Inicio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha Inicio
                                <span class="text-gray-500 text-xs ml-1">(requerido)</span>
                            </label>
                            <input
                                type="date"
                                v-model="form.start_date"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                                required
                            />
                        </div>

                        <!-- Fecha Fin -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha Fin
                                <span class="text-gray-500 text-xs ml-1">(requerido)</span>
                            </label>
                            <input
                                type="date"
                                v-model="form.end_date"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                                required
                            />
                        </div>
                    </div>

                    <!-- Categoría y Subcategoría -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Categoría -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Categoría
                                <span class="text-gray-500 text-xs ml-1">(requerido)</span>
                            </label>
                            <div v-if="loadingCategories" class="relative">
                                <div class="absolute inset-0 bg-gray-100 dark:bg-gray-800 opacity-50 rounded-md"></div>
                                <div class="flex items-center justify-center h-10 text-gray-500">
                                    <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            <select
                                v-else
                                v-model="form.category"
                                @change="onCategoryChange"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                                required
                            >
                                <option value="">Seleccione una categoría</option>
                                <option
                                    v-for="category in orderedCategories"
                                    :key="category.id"
                                    :value="category.id"
                                    class="dark:bg-gray-800"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Subcategoría -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subcategoría
                            </label>
                            <div v-if="loadingSubcategories" class="relative">
                                <div class="absolute inset-0 bg-gray-100 dark:bg-gray-800 opacity-50 rounded-md"></div>
                                <div class="flex items-center justify-center h-10 text-gray-500">
                                    <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            <select
                                v-else
                                v-model="form.subcategory"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 disabled:opacity-50"
                                :disabled="!form.category || loadingSubcategories"
                            >
                                <option value="">Seleccione una subcategoría</option>
                                <option
                                    v-for="subcategory in orderedSubcategories"
                                    :key="subcategory.id"
                                    :value="subcategory.id"
                                    class="dark:bg-gray-800"
                                >
                                    {{ subcategory.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Errores de formulario -->
                    <div v-if="Object.keys(errors).length > 0"
                         class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-900">
                        <div class="flex items-center text-red-600 dark:text-red-400">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Error en el formulario:</span>
                        </div>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            <li v-for="(errorMessages, field) in errors" :key="field">
                                {{ errorMessages[0] }}
                            </li>
                        </ul>
                    </div>

                    <!-- Botón de envío -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="submit"
                            :disabled="processing || loadingCategories || loadingSubcategories"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg
                                v-if="processing"
                                class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ processing ? 'Procesando...' : 'Iniciar Exportación' }}
                        </button>
                    </div>
                </form>
            </div>
        </Card>

        <!-- Lista de exportaciones recientes -->
        <Card class="mt-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 inline-block mr-4">Exportaciones Recientes</h3>
                    </div>
                    <button
                        @click="loadTasks"
                        :disabled="loadingTasks"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg
                            :class="{'animate-spin': loadingTasks}"
                            class="w-4 h-4 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        {{ loadingTasks ? 'Actualizando...' : 'Actualizar lista' }}
                    </button>
                </div>

                <div v-if="loadingTasks && tasks.length === 0" class="py-8">
                    <div class="flex flex-col items-center justify-center space-y-3 text-gray-500">
                        <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm">Cargando historial...</p>
                    </div>
                </div>

                <div v-else-if="tasks.length === 0" class="py-8 text-center">
                    <div class="text-gray-400 dark:text-gray-500">
                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="mt-2 text-sm">No se encontraron exportaciones recientes</p>
                    </div>
                </div>

                <div v-else class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/4">
                                Rango de fechas
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/5">
                                Categoría
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/5">
                                Subcategoría
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/6">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/6">
                                Análisis
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="task in tasks" :key="task.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 w-1/4">
                                {{ formatDate(task.start_date) }} – {{ formatDate(task.end_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 w-1/5">
                                {{ task.category || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 w-1/5">
                                {{ task.subcategory || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap w-1/6">
                                <span :class="getStatusClass(task.status)"
                                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium leading-4">
                                    {{ getStatusLabel(task.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium w-1/6">
                                <div v-if="task.status === 'completed'" class="flex items-center">
                                    <button
                                        @click="analyzeFile(task)"
                                        :disabled="analyzingTasks[task.id]"
                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 transition-colors duration-200 flex items-center"
                                    >
                                        <svg v-if="analyzingTasks[task.id]" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        {{ analyzingTasks[task.id] ? 'Analizando...' : 'Analizar Datos' }}
                                    </button>
                                </div>
                                <span v-else class="text-gray-500 dark:text-gray-400 text-sm">
                                    {{ task.status === 'processing' ? 'En progreso...' : 'No disponible' }}
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </Card>

        <!-- Modal de resultados de análisis -->
        <Modal v-if="showAnalysisModal" @close="closeAnalysisModal">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Análisis de Datos de Venta</h3>
                    <button @click="closeAnalysisModal" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div v-if="analysisError" class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg text-red-700 dark:text-red-400 mb-4">
                    <p>{{ analysisError }}</p>
                </div>

                <div v-else-if="!analysisResults" class="flex justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <div v-else class="space-y-6 max-h-96 overflow-y-auto">
                    <!-- Aquí puedes mostrar los resultados del análisis según la estructura de datos que devuelva tu microservicio -->
                    <div v-if="typeof analysisResults === 'object'" class="space-y-4">
                        <div v-for="(value, key) in analysisResults" :key="key" class="border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">{{ formatKey(key) }}</h4>

                            <!-- Si el valor es un objeto o array, mostrarlo recursivamente -->
                            <div v-if="typeof value === 'object' && value !== null" class="pl-4 space-y-2">
                                <div v-for="(subValue, subKey) in value" :key="subKey" class="text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ formatKey(subKey) }}:</span>
                                    <span class="ml-2 text-gray-900 dark:text-gray-100">{{ typeof subValue === 'object' ? JSON.stringify(subValue) : subValue }}</span>
                                </div>
                            </div>

                            <!-- Si es un valor simple, mostrarlo directamente -->
                            <div v-else class="text-sm text-gray-900 dark:text-gray-100">
                                {{ value }}
                            </div>
                        </div>
                    </div>

                    <!-- Si es un string u otro tipo de datos simple -->
                    <div v-else class="text-gray-900 dark:text-gray-100">
                        {{ analysisResults }}
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="closeAnalysisModal" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Cerrar
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                start_date: '',
                end_date: '',
                category: '',
                subcategory: ''
            },
            errors: {},
            processing: false,
            tasks: [],
            loadingTasks: false,
            loadingCategories: false,
            loadingSubcategories: false,
            categories: [], // Array de objetos {id, name}
            orderedCategories: [], // Referencia a categories, ya ordenado del backend
            orderedSubcategories: [], // Array de objetos {id, name} para subcategorías
            // Nuevas variables para el análisis
            analyzingTasks: {},
            showAnalysisModal: false,
            analysisResults: null,
            analysisError: null
        }
    },

    mounted() {
        console.log('Component mounted');
        this.loadTasks(); // Solo cargamos las tareas al inicio, que incluirá cargar categorías si es necesario
    },

    methods: {
        async loadCategories() {
            this.loadingCategories = true;

            try {
                console.log('Loading categorias...');
                const response = await Nova.request().get('/nova-vendor/export-sales/categories');

                // La respuesta viene como un array de objetos {id, name} ya ordenado
                this.categories = response.data;
                this.orderedCategories = response.data;

            } catch (error) {
                console.error('Error loading categorias:', error);
                Nova.error('Error cargando categorías');
            } finally {
                this.loadingCategories = false;
            }
        },

        async onCategoryChange() {
            this.form.subcategory = '';
            this.orderedSubcategories = [];

            if (!this.form.category) return;

            this.loadingSubcategories = true;

            try {
                const categoryId = this.form.category;
                console.log(`Loading subcategories for category ID: ${categoryId}`);

                const response = await Nova.request().get('/nova-vendor/export-sales/subcategories', {
                    params: { category: categoryId }
                });

                // Solo esperamos recibir un array de objetos {id, name}
                if (!Array.isArray(response.data)) {
                    console.error('Error: Las subcategorías no tienen el formato esperado (array)');
                    this.orderedSubcategories = [];
                    return;
                }

                // Asignamos directamente el array
                this.orderedSubcategories = response.data;

            } catch (error) {
                console.error('Error loading subcategorias:', error);
                Nova.error('Error cargando subcategorías');
            } finally {
                this.loadingSubcategories = false;
            }
        },

        async submitForm() {
            console.log('Form submitted:', this.form);
            this.processing = true;
            this.errors = {};

            try {
                const response = await Nova.request().post('/nova-vendor/export-sales/export', this.form);
                console.log('Export response:', response);
                Nova.success(response.data.message || 'Exportación iniciada');

                // Limpiar formulario
                this.form = {
                    start_date: '',
                    end_date: '',
                    category: '',
                    subcategory: ''
                };
                this.orderedSubcategories = [];

                // Recargar tareas
                await this.loadTasks();
            } catch (error) {
                console.error('Error submitting form:', error);
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                    Nova.error('Por favor corrige los errores en el formulario');
                } else {
                    Nova.error('Error al iniciar exportación');
                }
            } finally {
                this.processing = false;
            }
        },

        async loadTasks() {
            this.loadingTasks = true;

            try {
                // Primero cargar las categorías si no están cargadas
                if (this.categories.length === 0) {
                    await this.loadCategories();
                }

                // Luego cargar las tareas
                console.log('Loading tasks...');
                const response = await Nova.request().get('/nova-vendor/export-sales/tasks');
                console.log('Tasks response:', response);
                this.tasks = response.data;

            } catch (error) {
                console.error('Error loading tasks or categories:', error);
                Nova.error('Error cargando datos');
            } finally {
                this.loadingTasks = false;
            }
        },

        getStatusClass(status) {
            const classes = {
                pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            };
            return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        },

        getStatusLabel(status) {
            const labels = {
                pending: 'Pendiente',
                processing: 'Procesando',
                completed: 'Completado',
                failed: 'Error'
            };
            return labels[status] || status;
        },

        formatDate(date) {
            if (!date) return '';

            // Asegurarse de que la fecha se interprete correctamente, sin ajustes de zona horaria
            const [year, month, day] = date.split('T')[0].split('-').map(Number);
            // Months in JavaScript are 0-indexed, so we subtract 1 from the month
            const formattedDate = new Date(year, month - 1, day);

            return formattedDate.toLocaleDateString('es-ES');
        },

        // Nueva función para analizar un archivo
        async analyzeFile(task) {
            console.log('Analyzing file for task:', task.id);

            // Evitar análisis múltiples simultáneos
            if (this.analyzingTasks[task.id]) {
                return;
            }

            // Inicializar el modal
            this.showAnalysisModal = true;
            this.analysisResults = null;
            this.analysisError = null;

            // Marcar esta tarea como en análisis
            this.analyzingTasks[task.id] = true;

            try {
                const response = await Nova.request().post(`/nova-vendor/export-sales/analyze/${task.id}`);
                console.log('Analysis response:', response);

                // Almacenar los resultados
                this.analysisResults = response.data.data;

            } catch (error) {
                console.error('Error analyzing file:', error);
                this.analysisError = error.response?.data?.message || 'Error al analizar el archivo';
                Nova.error('Error al analizar el archivo');
            } finally {
                // Marcar como completado
                this.analyzingTasks[task.id] = false;
            }
        },

        // Cierra el modal de análisis
        closeAnalysisModal() {
            this.showAnalysisModal = false;
            this.analysisResults = null;
            this.analysisError = null;
        },

        // Formatea claves de objeto para mejor visualización
        formatKey(key) {
            // Convierte camelCase o snake_case a palabras con espacios y capitaliza la primera letra
            return key
                .replace(/_/g, ' ')
                .replace(/([A-Z])/g, ' $1')
                .replace(/^./, str => str.toUpperCase());
        }
    },
    // Necesitamos registrar el componente Modal
    components: {
        Modal: {
            props: ['width', 'height'],
            template: `
                <div class="fixed inset-0 z-50 flex items-center justify-center">
                    <div class="absolute inset-0 bg-black opacity-50" @click="$emit('close')"></div>
                    <div class="relative bg-white dark:bg-gray-900 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
                        <slot></slot>
                    </div>
                </div>
            `
        }
    }
}
</script>

<style scoped>
/* Optional CSS if needed */
</style>
