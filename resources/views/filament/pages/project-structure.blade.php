<x-filament::page>
    <div class="container mx-auto p-4">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Project Directory</h2>
                    <div class="flex space-x-2">
                        <button class="btn-view" id="btn-list"><i class="fas fa-th-list"></i></button>
                        <button class="btn-view active" id="btn-grid"><i class="fas fa-th-large"></i></button>
                    </div>
                </div>
            </div>
            <div class="p-4" id="directoryContent">
                <nav class="mb-4">
                    <ol class="flex flex-wrap items-center space-x-2 text-gray-500" id="breadcrumb">
                        <li><a href="#" class="hover:text-gray-700" data-path=""><i class="far fa-folder mr-1"></i>ramatechnologies.in</a></li>
                    </ol>
                </nav>
                <div id="main-folders" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @include('filament.pages.partials.directory-tree', ['items' => $structure])
                </div>
            </div>
        </div>
    </div>
</x-filament::page>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const structure = @json($structure);

        function renderDirectory(items, path = []) {
            let html = '';
            items.forEach(item => {
                const itemPath = [...path, item.name];
                const isPhpFile = item.type === 'file' && item.name.endsWith('.php');
                html += `
                    <div>
                        <button class="folder-container w-full text-left" data-type="${item.type}" data-path="${itemPath.join('/')}">
                            <div class="flex items-center">
                                <div class="folder-icon mr-3">
                                    ${isPhpFile 
                                        ? '<i class="fa-brands fa-php text-blue-500"></i>'
                                        : `<i class="fas fa-${item.type === 'folder' ? 'folder' : 'file'} ${item.type === 'folder' ? 'text-yellow-400' : 'text-gray-400'}"></i>`
                                    }
                                </div>
                                <div class="folder-name">${item.name}</div>
                            </div>
                        </button>
                    </div>
                `;
            });
            return html;
        }

        function updateBreadcrumb(path) {
            const breadcrumb = document.getElementById('breadcrumb');
            breadcrumb.innerHTML = `
                <li><a href="#" class="hover:text-gray-700" data-path=""><i class="far fa-folder mr-1"></i>ramatechnologies.in</a></li>
                ${path.map((item, index) => `
                    <li class="flex items-center">
                        <svg class="w-3 h-3 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="#" class="hover:text-gray-700" data-path="${path.slice(0, index + 1).join('/')}">${item}</a>
                    </li>
                `).join('')}
            `;
        }

        function navigateDirectory(path) {
            let currentItems = structure;
            path.forEach(item => {
                currentItems = currentItems.find(i => i.name === item)?.children || [];
            });
            document.getElementById('main-folders').innerHTML = renderDirectory(currentItems, path);
            updateBreadcrumb(path);
        }

        document.getElementById('directoryContent').addEventListener('click', function(e) {
            const button = e.target.closest('.folder-container');
            if (button) {
                const type = button.dataset.type;
                const path = button.dataset.path.split('/');
                if (type === 'folder') {
                    navigateDirectory(path);
                }
            }

            const breadcrumbLink = e.target.closest('#breadcrumb a');
            if (breadcrumbLink) {
                e.preventDefault();
                const path = breadcrumbLink.dataset.path.split('/').filter(Boolean);
                navigateDirectory(path);
            }
        });

        // Grid or list selection
        document.getElementById('btn-list').addEventListener('click', function() {
            document.getElementById('main-folders').classList.remove('grid', 'grid-cols-2', 'sm:grid-cols-3', 'md:grid-cols-4', 'lg:grid-cols-6', 'gap-4');
            document.getElementById('main-folders').classList.add('space-y-2');
            document.getElementById('btn-grid').classList.remove('active');
            this.classList.add('active');
        });

        document.getElementById('btn-grid').addEventListener('click', function() {
            document.getElementById('main-folders').classList.add('grid', 'grid-cols-2', 'sm:grid-cols-3', 'md:grid-cols-4', 'lg:grid-cols-6', 'gap-4');
            document.getElementById('main-folders').classList.remove('space-y-2');
            document.getElementById('btn-list').classList.remove('active');
            this.classList.add('active');
        });
    });
</script>
@endpush

@push('styles')
<style>
    .folder-container {
        @apply flex items-center p-4 rounded-lg transition-colors duration-200 hover:bg-gray-100;
    }

    .folder-icon {
        @apply text-2xl;
    }

    .folder-name {
        @apply text-sm break-words;
    }

    .btn-view {
        @apply p-2 rounded-md text-gray-500 hover:bg-gray-200 transition-colors duration-200;
    }

    .btn-view.active {
        @apply bg-gray-200 text-gray-700;
    }

    #main-folders.space-y-2 .folder-container {
        @apply w-full;
    }
</style>
@endpush