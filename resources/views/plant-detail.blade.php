<x-app-layout>

    <div class="min-h-screen w-full bg-green-950 overflow-x-hidden" style="scrollbar-width: none;">

        {{-- ── Top bar ── --}}
        <div class="flex items-center justify-between px-4 md:px-8 py-3 border-b"
            style="border-color: rgba(74,222,128,0.12); background: rgba(6,30,15,0.8);">
            <a href="{{ route('login') }}">
                <img src="{{ asset('images/FNLTransparent.png') }}" alt="Logo" class="h-10 md:h-12 w-auto object-contain">
            </a>
            <a href="{{ route('google.redirect', ['client' => 'web']) }}"
                class="flex items-center justify-center gap-3 px-4 py-2 bg-white border-[1.5px] border-green-100 text-green-900 rounded-full shadow-sm hover:border-green-400 hover:shadow-green-400/20 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                <svg style="width:20px;height:20px;flex-shrink:0;" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#4285F4" d="M533.5 278.4c0-17.4-1.6-34-4.8-50H272v95.2h146.9c-6.3 34-25 62.9-53.1 82l85.7 66.6c50-46.1 78-114 78-193.8z"/>
                    <path fill="#34A853" d="M272 544.3c71.7 0 132-23.8 176-64.6l-85.7-66.6c-23.8 16-54.2 25.5-90.3 25.5-69.3 0-128-46.9-149-110.1l-87.1 67.4c44 87.7 134.3 148.4 236.1 148.4z"/>
                    <path fill="#FBBC05" d="M123 323.1c-10-29.4-10-61.4 0-90.8l-87.1-67.4c-38.3 76.7-38.3 167.8 0 244.5l87.1 67.3z"/>
                    <path fill="#EA4335" d="M272 107.6c37.5 0 71 12.9 97.4 34.6l73-73C404 24 344.8 0 272 0 170.2 0 80 60.7 36 148.4l87.1 67.4c21-63.2 79.7-110.1 148.9-110.1z"/>
                </svg>
                <span class="text-sm font-medium tracking-wide">Sign In</span>
            </a>
        </div>

        {{-- ── Breadcrumb ── --}}
        <div class="px-4 md:px-8 pt-5 max-w-5xl mx-auto flex items-center gap-2 text-xs"
            style="color: rgba(134,239,172,0.35);">
            <a href="{{ route('login') }}"
                style="color: rgba(134,239,172,0.35); text-decoration:none;"
                onmouseover="this.style.color='rgba(134,239,172,0.7)'"
                onmouseout="this.style.color='rgba(134,239,172,0.35)'">Nurseries</a>
            <span>›</span>
            <a href="{{ route('nursery.public', $plant->nursery) }}"
                style="color: rgba(134,239,172,0.35); text-decoration:none;"
                onmouseover="this.style.color='rgba(134,239,172,0.7)'"
                onmouseout="this.style.color='rgba(134,239,172,0.35)'">{{ $plant->nursery->name }}</a>
            <span>›</span>
            <span style="color: rgba(134,239,172,0.6);">{{ $plant->name }}</span>
        </div>

        {{-- ── Main content ── --}}
        <div class="px-4 md:px-8 pt-5 pb-16 max-w-5xl mx-auto">
            <div class="rounded-2xl overflow-hidden"
                style="background: rgba(6,30,15,0.6); border: 1px solid rgba(74,222,128,0.14);">

                <div class="flex flex-col md:flex-row">

                    {{-- ── Image ── --}}
                    <div class="md:w-2/5 shrink-0">
                        @if ($plant->image)
                            <img src="{{ asset('storage/plants/' . $plant->image) }}"
                                alt="{{ $plant->name }}"
                                style="width:100%; height:320px; object-fit:cover; opacity:0.93;">
                        @else
                            <div style="width:100%; height:320px; background:rgba(74,222,128,0.04);
                                        border-right:1px solid rgba(74,222,128,0.08);
                                        display:flex; align-items:center; justify-content:center;">
                                <svg style="width:48px;height:48px;color:rgba(74,222,128,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                                        d="M12 22V12m0 0C12 7 8 4 4 5c0 4 2.5 7 8 7zm0 0c0-5 4-8 8-7-1 4-3 7-8 7"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- ── Info ── --}}
                    <div class="flex-1 px-6 py-6 flex flex-col gap-4">

                        {{-- Name + category tags --}}
                        <div>
                            <div class="flex flex-wrap gap-1.5 mb-2">
                                @if ($plant->category)
                                    <span class="text-[9px] font-semibold tracking-widest uppercase px-2 py-0.5 rounded-md"
                                        style="background:rgba(74,222,128,0.08);color:rgba(134,239,172,0.7);border:1px solid rgba(74,222,128,0.15);">
                                        {{ $plant->category }}
                                    </span>
                                @endif
                                @if ($plant->best_season)
                                    <span class="text-[9px] font-semibold tracking-widest uppercase px-2 py-0.5 rounded-md"
                                        style="background:rgba(74,222,128,0.08);color:rgba(134,239,172,0.7);border:1px solid rgba(74,222,128,0.15);">
                                        {{ $plant->best_season }}
                                    </span>
                                @endif
                            </div>

                            <h1 class="text-2xl md:text-3xl font-semibold leading-tight"
                                style="color: rgba(220,252,231,0.92);">{{ $plant->name }}</h1>

                            @if ($plant->scientific_name)
                                <p class="text-sm italic mt-1" style="color: rgba(134,239,172,0.4);">
                                    {{ $plant->scientific_name }}
                                </p>
                            @endif
                        </div>

                        {{-- Price --}}
                        <div class="flex items-baseline gap-3">
                            @if ($plant->offer_price && $plant->offer_price < $plant->selling_price)
                                <span class="text-2xl font-bold" style="color: rgba(134,239,172,0.85);">
                                    Rs. {{ number_format($plant->offer_price, 0) }}
                                </span>
                                <span class="text-base font-medium line-through" style="color: rgba(134,239,172,0.3);">
                                    Rs. {{ number_format($plant->selling_price, 0) }}
                                </span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                    style="background:rgba(74,222,128,0.1);color:rgba(134,239,172,0.7);border:1px solid rgba(74,222,128,0.2);">
                                    {{ round((1 - $plant->offer_price / $plant->selling_price) * 100) }}% off
                                </span>
                            @else
                                <span class="text-2xl font-bold" style="color: rgba(134,239,172,0.85);">
                                    Rs. {{ number_format($plant->selling_price, 0) }}
                                </span>
                            @endif
                        </div>

                        {{-- Stock --}}
                        @if ($plant->stock_quantity !== null)
                            <p class="text-xs" style="color: rgba(134,239,172,0.4);">
                                {{ $plant->stock_quantity > 0 ? $plant->stock_quantity . ' in stock' : 'Out of stock' }}
                            </p>
                        @endif

                        {{-- Description --}}
                        @if ($plant->description)
                            <p class="text-sm leading-relaxed" style="color: rgba(134,239,172,0.5);">
                                {{ $plant->description }}
                            </p>
                        @endif

                        {{-- Care requirements --}}
                        @if ($plant->sunlight_requirement || $plant->water_requirement)
                            <div style="border-top:1px solid rgba(74,222,128,0.08);padding-top:1rem;">
                                <p class="text-[10px] font-bold tracking-[2px] uppercase mb-3"
                                    style="color: rgba(134,239,172,0.3);">Care</p>
                                <div class="flex flex-col gap-2">
                                    @if ($plant->sunlight_requirement)
                                        <div class="flex items-center gap-2">
                                            <svg style="width:14px;height:14px;flex-shrink:0;color:rgba(134,239,172,0.45);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                                            </svg>
                                            <span class="text-xs" style="color:rgba(134,239,172,0.5);">
                                                Sunlight: {{ ucfirst($plant->sunlight_requirement) }}
                                            </span>
                                        </div>
                                    @endif
                                    @if ($plant->water_requirement)
                                        <div class="flex items-center gap-2">
                                            <svg style="width:14px;height:14px;flex-shrink:0;color:rgba(134,239,172,0.45);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 2C6 9 4 13 4 16a8 8 0 0016 0c0-3-2-7-8-14z"/>
                                            </svg>
                                            <span class="text-xs" style="color:rgba(134,239,172,0.5);">
                                                Water: {{ ucfirst($plant->water_requirement) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Nursery link --}}
                        <div class="mt-auto pt-4" style="border-top:1px solid rgba(74,222,128,0.08);">
                            <p class="text-[10px] font-bold tracking-[2px] uppercase mb-2"
                                style="color: rgba(134,239,172,0.3);">Sold by</p>
                            <a href="{{ route('nursery.public', $plant->nursery) }}"
                                class="inline-flex items-center gap-2 transition-colors duration-200"
                                style="text-decoration:none;"
                                onmouseover="this.querySelector('span').style.color='rgba(134,239,172,0.85)'"
                                onmouseout="this.querySelector('span').style.color='rgba(134,239,172,0.6)'">
                                <svg style="width:13px;height:13px;flex-shrink:0;color:rgba(134,239,172,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-sm font-medium transition-colors duration-200"
                                    style="color:rgba(134,239,172,0.6);">{{ $plant->nursery->name }}</span>
                                @if ($plant->nursery->location)
                                    <span class="text-xs" style="color:rgba(134,239,172,0.3);">· {{ $plant->nursery->location }}</span>
                                @endif
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
