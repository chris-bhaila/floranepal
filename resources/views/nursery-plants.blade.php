<x-app-layout>

    <div class="min-h-screen w-full bg-green-950 overflow-x-hidden" style="scrollbar-width: none;">

        {{-- ── Top bar ── --}}
        <div class="flex items-center justify-between px-4 md:px-8 py-3 border-b"
            style="border-color: rgba(74,222,128,0.12); background: rgba(6,30,15,0.8);">
            <a href="{{ route('login') }}" class="flex items-center gap-2 group">
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

        {{-- ── Back link ── --}}
        <div class="px-4 md:px-8 pt-5 max-w-7xl mx-auto">
            <a href="{{ route('login') }}"
                class="inline-flex items-center gap-1.5 text-xs font-medium tracking-wide transition-colors duration-200"
                style="color: rgba(134,239,172,0.45);"
                onmouseover="this.style.color='rgba(134,239,172,0.8)'"
                onmouseout="this.style.color='rgba(134,239,172,0.45)'">
                <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to nurseries
            </a>
        </div>

        {{-- ── Nursery info header ── --}}
        <div class="px-4 md:px-8 pt-5 pb-6 max-w-7xl mx-auto">
            <div class="rounded-2xl px-6 py-6"
                style="background: rgba(6,30,15,0.6); border: 1px solid rgba(74,222,128,0.14);">

                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold tracking-[2px] uppercase mb-2"
                            style="color: rgba(134,239,172,0.35);">Nursery</p>
                        <h1 class="text-2xl md:text-3xl font-semibold leading-tight"
                            style="color: rgba(220,252,231,0.92);">{{ $nursery->name }}</h1>

                        @if ($nursery->location)
                            <div class="flex items-center gap-1.5 mt-2">
                                <svg style="width:13px;height:13px;flex-shrink:0;color:rgba(134,239,172,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-sm" style="color: rgba(134,239,172,0.55);">{{ $nursery->location }}</span>
                            </div>
                        @endif

                        @if ($nursery->description)
                            <p class="text-sm mt-3 leading-relaxed" style="color: rgba(134,239,172,0.45);">
                                {{ $nursery->description }}
                            </p>
                        @endif
                    </div>

                    {{-- Contact info --}}
                    <div class="flex flex-col gap-2" style="flex-shrink:0;">
                        @if ($nursery->contact_phone)
                            <div class="flex items-center gap-2">
                                <svg style="width:13px;height:13px;flex-shrink:0;color:rgba(134,239,172,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="text-xs" style="color: rgba(134,239,172,0.5);">{{ $nursery->contact_phone }}</span>
                            </div>
                        @endif
                        @if ($nursery->contact_email)
                            <div class="flex items-center gap-2">
                                <svg style="width:13px;height:13px;flex-shrink:0;color:rgba(134,239,172,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs" style="color: rgba(134,239,172,0.5);">{{ $nursery->contact_email }}</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold tracking-wider uppercase px-2.5 py-1 rounded-full"
                                style="{{ $nursery->is_active ? 'background: rgba(74,222,128,0.1); color: rgba(134,239,172,0.75); border: 1px solid rgba(74,222,128,0.2);' : 'background: rgba(239,68,68,0.08); color: rgba(252,165,165,0.6); border: 1px solid rgba(239,68,68,0.15);' }}">
                                <span style="width:6px;height:6px;border-radius:9999px;flex-shrink:0;background:{{ $nursery->is_active ? 'rgb(74,222,128)' : 'rgb(248,113,113)' }};"></span>
                                {{ $nursery->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 flex items-center gap-2" style="border-top: 1px solid rgba(74,222,128,0.08);">
                    <span class="text-xs font-semibold" style="color: rgba(134,239,172,0.35);">
                        {{ $nursery->plants->count() }} {{ $nursery->plants->count() === 1 ? 'plant' : 'plants' }} available
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Plants grid ── --}}
        <div class="px-4 md:px-8 pb-16 max-w-7xl mx-auto">

            @if ($nursery->plants->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 gap-3">
                    <svg style="width:40px;height:40px;color:rgba(74,222,128,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                            d="M12 22V12m0 0C12 7 8 4 4 5c0 4 2.5 7 8 7zm0 0c0-5 4-8 8-7-1 4-3 7-8 7"/>
                    </svg>
                    <p class="text-sm" style="color: rgba(134,239,172,0.3);">No plants listed yet.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
                    style="gap: 1px; background: rgba(74,222,128,0.06); border-radius: 16px; overflow: hidden;
                           border: 1px solid rgba(74,222,128,0.12);">

                    @foreach ($nursery->plants as $plant)
                        <a href="{{ route('plant.public', $plant) }}"
                            class="flex flex-col p-3 transition-colors duration-200"
                            style="background: #071a0e; text-decoration: none;"
                            onmouseover="this.style.background='rgba(74,222,128,0.04)'"
                            onmouseout="this.style.background='#071a0e'">

                            @if ($plant->image)
                                <img src="{{ asset('storage/plants/' . $plant->image) }}"
                                    alt="{{ $plant->name }}"
                                    loading="lazy"
                                    class="w-full object-cover rounded-xl mb-3"
                                    style="height:128px;opacity:0.92;">
                            @else
                                <div class="w-full rounded-xl flex items-center justify-center mb-3"
                                    style="height:128px;background:rgba(74,222,128,0.04);border:1px solid rgba(74,222,128,0.08);">
                                    <svg style="width:24px;height:24px;color:rgba(74,222,128,0.25);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                                            d="M12 22V12m0 0C12 7 8 4 4 5c0 4 2.5 7 8 7zm0 0c0-5 4-8 8-7-1 4-3 7-8 7"/>
                                    </svg>
                                </div>
                            @endif

                            <p class="text-sm font-semibold truncate" title="{{ $plant->name }}"
                                style="color: rgba(220,252,231,0.82);">
                                {{ $plant->name }}
                            </p>

                            @if ($plant->scientific_name)
                                <p class="text-[10px] italic truncate mt-0.5" style="color: rgba(134,239,172,0.35);">
                                    {{ $plant->scientific_name }}
                                </p>
                            @endif

                            <div class="flex flex-wrap gap-1.5 mt-1.5 mb-2 min-h-[20px]">
                                @if ($plant->category)
                                    <span class="text-[9px] font-medium rounded-md px-2 py-0.5"
                                        style="background: rgba(74,222,128,0.07); color: rgba(134,239,172,0.6); border: 1px solid rgba(74,222,128,0.12);">
                                        {{ ucfirst($plant->category) }}
                                    </span>
                                @endif
                                @if ($plant->best_season)
                                    <span class="text-[9px] font-medium rounded-md px-2 py-0.5"
                                        style="background: rgba(74,222,128,0.07); color: rgba(134,239,172,0.6); border: 1px solid rgba(74,222,128,0.12);">
                                        {{ ucfirst($plant->best_season) }}
                                    </span>
                                @endif
                                @if ($plant->sunlight_requirement)
                                    <span class="text-[9px] font-medium rounded-md px-2 py-0.5"
                                        style="background: rgba(74,222,128,0.07); color: rgba(134,239,172,0.6); border: 1px solid rgba(74,222,128,0.12);">
                                        ☀ {{ ucfirst($plant->sunlight_requirement) }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-baseline gap-2 mt-auto pt-2">
                                @if ($plant->offer_price && $plant->offer_price < $plant->selling_price)
                                    <span class="text-[11px] font-medium line-through"
                                        style="color: rgba(134,239,172,0.3);">
                                        Rs. {{ number_format($plant->selling_price, 0) }}
                                    </span>
                                    <span class="text-sm font-semibold"
                                        style="color: rgba(134,239,172,0.75);">
                                        Rs. {{ number_format($plant->offer_price, 0) }}
                                    </span>
                                @else
                                    <span class="text-sm font-semibold"
                                        style="color: rgba(134,239,172,0.75);">
                                        Rs. {{ number_format($plant->selling_price, 0) }}
                                    </span>
                                @endif
                            </div>

                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</x-app-layout>
