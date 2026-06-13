<x-app-layout>

    <div class="min-h-screen w-full overflow-y-auto bg-green-950 overflow-x-hidden" style="scrollbar-width: none;">

        {{-- ── Hero ── --}}
        <div class="relative h-28 md:h-48 overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('{{ asset("images/Login-bg-image.jpeg") }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-green-950/30 to-green-950/85"></div>

            {{-- Top bar --}}
            <div class="absolute top-0 left-0 right-0 z-10 flex items-center px-4 md:px-8 py-3 justify-between">
                <img src="{{ asset('images/FNLTransparent.png') }}" alt="Logo" class="h-12 md:h-16 w-auto object-contain">
                <a href="{{ route('google.redirect', ['client' => 'web']) }}"
                    class="flex items-center justify-center gap-3 px-4 py-2 bg-white border-[1.5px] border-green-100 text-green-900 rounded-full shadow-sm hover:border-green-400 hover:shadow-green-400/20 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#4285F4" d="M533.5 278.4c0-17.4-1.6-34-4.8-50H272v95.2h146.9c-6.3 34-25 62.9-53.1 82l85.7 66.6c50-46.1 78-114 78-193.8z"/>
                        <path fill="#34A853" d="M272 544.3c71.7 0 132-23.8 176-64.6l-85.7-66.6c-23.8 16-54.2 25.5-90.3 25.5-69.3 0-128-46.9-149-110.1l-87.1 67.4c44 87.7 134.3 148.4 236.1 148.4z"/>
                        <path fill="#FBBC05" d="M123 323.1c-10-29.4-10-61.4 0-90.8l-87.1-67.4c-38.3 76.7-38.3 167.8 0 244.5l87.1 67.3z"/>
                        <path fill="#EA4335" d="M272 107.6c37.5 0 71 12.9 97.4 34.6l73-73C404 24 344.8 0 272 0 170.2 0 80 60.7 36 148.4l87.1 67.4c21-63.2 79.7-110.1 148.9-110.1z"/>
                    </svg>
                    <span class="text-sm font-medium tracking-wide">Sign In</span>
                </a>
            </div>

            {{-- Tagline --}}
            <div class="absolute bottom-0 left-0 right-0 z-10 px-5 md:px-8 mb-1">
                <div class="w-10 h-0.5 bg-gradient-to-r from-green-400 to-transparent rounded-full mt-3"></div>
                <h1 class="text-2xl md:text-3xl font-light italic text-green-100 leading-tight">
                    Where every plant tells a <span class="not-italic font-semibold text-green-200">story.</span>
                </h1>
            </div>
        </div>

        {{-- ── Plant Carousel ── --}}
        @php
            $carouselItems = $nurseries->map(function($nursery) {
                $plant = $nursery->plants->first();
                return $plant ? ['plant' => $plant, 'nursery' => $nursery] : null;
            })->filter()->values();
        @endphp

        @if ($carouselItems->count())
        <div class="relative border-t border-b border-green-800/40 overflow-hidden"
            style="height: 196px; background: rgba(0,20,10,0.4);">

            {{-- Edge fades --}}
            <div class="absolute left-0 inset-y-0 w-20 bg-gradient-to-r from-green-950 to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 inset-y-0 w-20 bg-gradient-to-l from-green-950 to-transparent z-10 pointer-events-none"></div>

            {{-- Slide area --}}
            <div class="absolute inset-0 flex items-center justify-center" id="carouselWrapper"
                style="user-select: none; touch-action: pan-y;">

                @foreach ($carouselItems as $i => $item)
                    <div class="carousel-slide absolute flex items-center gap-6 rounded-2xl"
                        data-index="{{ $i }}"
                        style="opacity:0; transform:translateX(100px); will-change:transform,opacity;
                               min-width: min(440px, 88vw); max-width: min(520px, 92vw);
                               padding: 20px 24px;
                               background: rgba(6, 40, 20, 0.75);
                               border: 1px solid rgba(74, 222, 128, 0.12);">

                        @if ($item['plant']->image)
                            <img src="{{ asset('storage/plants/' . $item['plant']->image) }}"
                                alt="{{ $item['plant']->name }}"
                                class="rounded-xl object-cover shrink-0"
                                style="width:116px; height:116px; box-shadow: 0 0 0 1px rgba(74,222,128,0.1);">
                        @else
                            <div class="rounded-xl shrink-0 flex items-center justify-center"
                                style="width:116px; height:116px; background: rgba(74,222,128,0.05); border: 1px solid rgba(74,222,128,0.1);">
                                <svg class="w-9 h-9 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                                        d="M12 22V12m0 0C12 7 8 4 4 5c0 4 2.5 7 8 7zm0 0c0-5 4-8 8-7-1 4-3 7-8 7"/>
                                </svg>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            @if ($item['plant']->category)
                                <span class="inline-block text-[9px] font-semibold tracking-widest uppercase px-2 py-0.5 rounded-md mb-2"
                                    style="background: rgba(74,222,128,0.08); color: rgba(134,239,172,0.7); border: 1px solid rgba(74,222,128,0.15);">
                                    {{ $item['plant']->category }}
                                </span>
                            @endif
                            <p class="text-xl font-semibold truncate leading-snug" style="color: rgba(220,252,231,0.9);">
                                {{ $item['plant']->name }}
                            </p>
                            <p class="text-xs truncate mt-0.5" style="color: rgba(134,239,172,0.45);">
                                {{ $item['nursery']->name }}
                            </p>
                            <p class="text-base font-semibold mt-2.5" style="color: rgba(134,239,172,0.75);">
                                Rs. {{ number_format($item['plant']->offer_price ?? $item['plant']->selling_price, 0) }}
                            </p>
                        </div>

                        {{-- Vertical dots --}}
                        <div class="flex flex-col items-center gap-1.5 shrink-0">
                            @foreach ($carouselItems as $j => $_)
                                <div class="carousel-dot rounded-full transition-all duration-500"
                                    data-dot="{{ $j }}"
                                    style="{{ $j === 0
                                        ? 'width:6px; height:22px; background:rgba(134,239,172,0.6); border-radius:4px;'
                                        : 'width:4px; height:4px; background:rgba(74,222,128,0.2);' }}">
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
        @endif

        {{-- ── Nursery list ── --}}
        <div class="px-4 md:px-8 pt-6 pb-12 flex flex-col gap-6 max-w-7xl mx-auto">

            <p class="text-xs font-bold tracking-[2px] uppercase" style="color: rgba(134,239,172,0.35);">Featured Nurseries</p>

            @foreach ($nurseries as $nursery)
                @if ($nursery->plants->count())
                    <div class="rounded-2xl overflow-hidden shadow-lg"
                        style="border: 1px solid rgba(74,222,128,0.12); background: rgba(6,30,15,0.5);">

                        <button onclick="toggleNursery(this)"
                            class="w-full flex items-center justify-between px-5 py-4 text-left transition-colors duration-200 focus:outline-none"
                            style="background: rgba(74,222,128,0.05);"
                            onmouseover="this.style.background='rgba(74,222,128,0.08)'"
                            onmouseout="this.style.background='rgba(74,222,128,0.05)'">
                            <div>
                                <h2 class="text-base font-semibold" style="color: rgba(220,252,231,0.85);">{{ $nursery->name }}</h2>
                                <p class="text-xs mt-1" style="color: rgba(134,239,172,0.4);">
                                    {{ $nursery->plants->count() }} plants available
                                </p>
                            </div>
                            <span class="transform transition-transform duration-300 text-xs" style="color: rgba(134,239,172,0.4);">▼</span>
                        </button>

                        <div class="nursery-content">
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
                                style="gap: 1px; background: rgba(74,222,128,0.06);">

                                @foreach ($nursery->plants as $plant)
                                    <div class="plant-card-{{ $nursery->id }} flex flex-col p-3 transition-colors duration-200 {{ $loop->iteration > 4 ? 'hidden' : '' }}"
                                        style="background: #071a0e;"
                                        onmouseover="this.style.background='rgba(74,222,128,0.04)'"
                                        onmouseout="this.style.background='#071a0e'">

                                        @if ($plant->image)
                                            <img src="{{ asset('storage/plants/' . $plant->image) }}"
                                                alt="{{ $plant->name }}"
                                                loading="lazy"
                                                class="w-full h-32 sm:h-40 object-cover rounded-xl mb-3"
                                                style="opacity: 0.92;">
                                        @else
                                            <div class="w-full h-32 sm:h-40 rounded-xl flex items-center justify-center mb-3"
                                                style="background: rgba(74,222,128,0.04); border: 1px solid rgba(74,222,128,0.08);">
                                                <svg class="w-6 h-6" style="color: rgba(74,222,128,0.25);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                                                        d="M12 22V12m0 0C12 7 8 4 4 5c0 4 2.5 7 8 7zm0 0c0-5 4-8 8-7-1 4-3 7-8 7"/>
                                                </svg>
                                            </div>
                                        @endif

                                        <p class="text-sm font-semibold truncate" title="{{ $plant->name }}"
                                            style="color: rgba(220,252,231,0.82);">
                                            {{ $plant->name }}
                                        </p>

                                        <div class="flex flex-wrap gap-1.5 mt-1 mb-2 min-h-[20px]">
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

                                    </div>
                                @endforeach
                            </div>

                            @if ($nursery->plants->count() > 4)
                                <div class="p-4 flex justify-center"
                                    style="background: rgba(74,222,128,0.03); border-top: 1px solid rgba(74,222,128,0.08);">
                                    <a href="{{ route('nursery.public', $nursery) }}"
                                        class="text-xs font-semibold tracking-widest uppercase px-6 py-2.5 rounded-full transition-all duration-200"
                                        style="color: rgba(134,239,172,0.65); background: rgba(74,222,128,0.07); border: 1px solid rgba(74,222,128,0.15);"
                                        onmouseover="this.style.background='rgba(74,222,128,0.12)'; this.style.color='rgba(134,239,172,0.9)';"
                                        onmouseout="this.style.background='rgba(74,222,128,0.07)'; this.style.color='rgba(134,239,172,0.65)';">
                                        Show More
                                    </a>
                                </div>
                            @endif
                        </div>

                    </div>
                @endif
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-4 py-5 text-center" style="border-top: 1px solid rgba(74,222,128,0.08);">
            <a href="{{ route('privacy-policy') }}"
               class="text-xs transition-colors duration-200"
               style="color: rgba(134,239,172,0.4);"
               onmouseover="this.style.color='rgba(134,239,172,0.7)'"
               onmouseout="this.style.color='rgba(134,239,172,0.4)'">
                Privacy Policy
            </a>
        </div>

    </div>

    <script>
        (function () {
            const slides  = Array.from(document.querySelectorAll('.carousel-slide'));
            const dots    = Array.from(document.querySelectorAll('.carousel-dot'));
            const wrapper = document.getElementById('carouselWrapper');
            if (!slides.length) return;

            let current  = 0;
            let paused   = false;
            let timer    = null;
            const PAUSE  = 3000;
            const TRANS  = 650;
            const SWIPE_THRESHOLD = 40; // px needed to trigger a swipe

            // Drag/swipe state
            let dragStartX  = null;
            let dragging    = false;
            let dragDelta   = 0;

            function setDot(idx) {
                dots.forEach((d, i) => {
                    d.style.cssText = i === idx
                        ? 'width:6px; height:22px; background:rgba(134,239,172,0.6); border-radius:4px; transition: all 0.5s ease;'
                        : 'width:4px; height:4px; background:rgba(74,222,128,0.2); border-radius:50%; transition: all 0.5s ease;';
                });
            }

            function show(idx, direction = 1) {
                const el = slides[idx];
                el.style.transition = 'none';
                el.style.opacity    = '0';
                el.style.transform  = `translateX(${direction * 100}px)`;
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    el.style.transition = `opacity ${TRANS}ms ease, transform ${TRANS}ms ease`;
                    el.style.opacity    = '1';
                    el.style.transform  = 'translateX(0)';
                }));
                setDot(idx);
            }

            function hide(idx, direction = -1) {
                const el = slides[idx];
                el.style.transition = `opacity ${TRANS}ms ease, transform ${TRANS}ms ease`;
                el.style.opacity    = '0';
                el.style.transform  = `translateX(${direction * 100}px)`;
            }

            function goTo(idx, direction = 1) {
                if (idx === current) return;
                hide(current, -direction);
                setTimeout(() => show(idx, direction), TRANS);
                current = idx;
            }

            function advance() {
                if (paused) return;
                const nxt = (current + 1) % slides.length;
                hide(current, -1);
                setTimeout(() => show(nxt, 1), TRANS);
                current = nxt;
            }

            function startTimer() {
                clearInterval(timer);
                timer = setInterval(advance, PAUSE + TRANS);
            }

            // Initial show
            show(0, 1);
            startTimer();

            // ── Pointer/touch drag ──
            function onDragStart(e) {
                dragStartX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
                dragging   = true;
                paused     = true;
                clearInterval(timer);
            }

            function onDragMove(e) {
                if (!dragging || dragStartX === null) return;
                const x = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
                dragDelta = x - dragStartX;

                // Live drag feedback on current slide
                const el = slides[current];
                el.style.transition = 'none';
                el.style.transform  = `translateX(${dragDelta * 0.3}px)`;
            }

            function onDragEnd(e) {
                if (!dragging) return;
                dragging = false;

                // Snap current slide back
                const el = slides[current];
                el.style.transition = `transform ${TRANS}ms ease`;
                el.style.transform  = 'translateX(0)';

                if (Math.abs(dragDelta) >= SWIPE_THRESHOLD) {
                    if (dragDelta < 0) {
                        // Swiped left → next
                        const nxt = (current + 1) % slides.length;
                        goTo(nxt, 1);
                    } else {
                        // Swiped right → previous
                        const prv = (current - 1 + slides.length) % slides.length;
                        goTo(prv, -1);
                    }
                }

                dragDelta  = 0;
                dragStartX = null;
                paused     = false;
                startTimer();
            }

            // Mouse drag
            wrapper.addEventListener('mousedown',  onDragStart);
            window.addEventListener('mousemove',   onDragMove);
            window.addEventListener('mouseup',     onDragEnd);

            // Touch swipe
            wrapper.addEventListener('touchstart', onDragStart, { passive: true });
            wrapper.addEventListener('touchmove',  onDragMove,  { passive: true });
            wrapper.addEventListener('touchend',   onDragEnd);

            // Prevent image dragging interfering
            wrapper.querySelectorAll('img').forEach(img => img.addEventListener('dragstart', e => e.preventDefault()));
        })();

        function toggleNursery(btn) {
            const content = btn.nextElementSibling;
            const chevron = btn.querySelector('span');
            content.classList.toggle('hidden');
            chevron.style.transform = content.classList.contains('hidden') ? 'rotate(-90deg)' : 'rotate(0deg)';
        }

    </script>

</x-app-layout>