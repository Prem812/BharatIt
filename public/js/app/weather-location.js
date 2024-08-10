document.addEventListener('livewire:load', function () {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            Livewire.emit('locationUpdated', position.coords.latitude, position.coords.longitude);
        });
    }
});