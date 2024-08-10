<div>
    <h1>{{ $count }}</h1>

    <p>the current time is {{ time() }}</p>
    <button wire:click="increment">+</button>
 
    <button wire:click="decrement">-</button>
</div>