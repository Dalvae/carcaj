<!-- Botón menú móvil con animación -->
<button @click="$store.header.isOpen = !$store.header.isOpen"
    class="lg:hidden relative w-10 h-10 focus:outline-none z-[999]"
    aria-label="Menu">
    <div class="absolute w-6 transform left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2">
        <span class="absolute h-0.5 w-6 bg-black transform transition duration-300 ease-in-out"
            :class="{'rotate-45': $store.header.isOpen, '-translate-y-2': !$store.header.isOpen}"></span>
        <span class="absolute h-0.5 w-6 bg-black transform transition duration-300 ease-in-out"
            :class="{'opacity-0': $store.header.isOpen, 'translate-y-0': !$store.header.isOpen}"></span>
        <span class="absolute h-0.5 w-6 bg-black transform transition duration-300 ease-in-out"
            :class="{'-rotate-45': $store.header.isOpen, 'translate-y-2': !$store.header.isOpen}"></span>
    </div>
</button>
