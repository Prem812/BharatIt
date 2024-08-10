<x-filament::layouts.base :livewire="$livewire">
    {{-- Your existing layout content --}}
    
    @push('scripts')
    <script>
        function handlePaymentClick(event, phonePeUrl, webUrl) {
            event.preventDefault();
            
            // Try to open the PhonePe app
            window.location.href = phonePeUrl;
            
            // If the app doesn't open within 1 second, redirect to Wikipedia
            setTimeout(function() {
                window.location.href = 'https://www.wikipedia.org';
            }, 1000);
        }
    </script>
    @endpush
</x-filament::layouts.base>