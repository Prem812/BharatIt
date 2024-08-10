@foreach ($items as $item)
    <div>
        <button class="folder-container w-full text-left" data-type="{{ $item['type'] }}" data-path="{{ $item['name'] }}">
            <div class="flex items-center">
                <div class="folder-icon mr-3">
                    @if ($item['type'] === 'file' && pathinfo($item['name'], PATHINFO_EXTENSION) === 'php')
                        <i class="fa-brands fa-php text-blue-500"></i>
                    @else
                        <i class="fas fa-{{ $item['type'] === 'folder' ? 'folder' : 'file' }} {{ $item['type'] === 'folder' ? 'text-yellow-400' : 'text-gray-400' }}"></i>
                    @endif
                </div>
                <div class="folder-name">{{ $item['name'] }}</div>
            </div>
        </button>
    </div>
@endforeach