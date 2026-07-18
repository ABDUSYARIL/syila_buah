@php
    $textCol = $textColor ?? 'text-gray-dark';
    $spanCol = $spanColor ?? 'text-primary';
@endphp
<div class="flex items-center gap-2 flex-shrink-0 group">
    <!-- 3D Styled SVG Fruit Logo -->
    <svg class="w-8 h-8 filter drop-shadow-[0_4px_6px_rgba(76,175,80,0.2)] transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-500" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Leaf with green gradient -->
        <path d="M17.5 7.5C17.5 7.5 19.5 3.5 23 2.5C26 2.5 25.5 5.5 23.5 8.5C21.5 11.5 17.5 12 17.5 12C17.5 12 15.5 10.5 15.8 8.8C16 7.1 17.5 7.5 17.5 7.5Z" fill="url(#logoLeafGrad)"/>
        <!-- Fruit Body with vibrant orange/red gradient for 3D sphere look -->
        <circle cx="16" cy="18" r="10" fill="url(#logoFruitGrad)"/>
        <!-- 3D Glossy Highlight -->
        <ellipse cx="12.5" cy="13.5" rx="3.5" ry="2" transform="rotate(-30 12.5 13.5)" fill="white" fill-opacity="0.6"/>
        <!-- Curved Smile line representing satisfaction/freshness -->
        <path d="M12 20C13.5 21.5 18.5 21.5 20 20" stroke="white" stroke-width="1.5" stroke-linecap="round" opacity="0.8"/>
        
        <defs>
            <linearGradient id="logoLeafGrad" x1="15.8" y1="2.5" x2="25" y2="12" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#A5D6A7"/>
                <stop offset="50%" stop-color="#4CAF50"/>
                <stop offset="100%" stop-color="#2E7D32"/>
            </linearGradient>
            <linearGradient id="logoFruitGrad" x1="6" y1="8" x2="26" y2="28" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFCC80"/>
                <stop offset="40%" stop-color="#FF9800"/>
                <stop offset="80%" stop-color="#F57C00"/>
                <stop offset="100%" stop-color="#E65100"/>
            </linearGradient>
        </defs>
    </svg>
    <span class="font-extrabold {{ $textCol }} text-lg leading-none tracking-tight">
        Syila<span class="{{ $spanCol }}">Buah</span>
    </span>
</div>
