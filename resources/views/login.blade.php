<x-app-layout>
    <div class="min-h-screen w-full overflow-y-auto bg-green-950 overflow-x-hidden" style="scrollbar-width: none;">

        {{-- Hero --}}
        <div class="relative h-28 md:h-48 overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('{{ asset("images/Login-bg-image.jpeg") }}');"></div>
            
            {{-- Restored Original Gradient --}}
            <div class="absolute inset-0 bg-gradient-to-b from-green-950/30 to-green-950/85"></div>

            {{-- Top bar --}}
            <div class="absolute top-0 left-0 right-0 z-10 flex items-center px-4 md:px-8 py-3 justify-between">
                <img src="{{ asset('images/FNLTransparent.png') }}" alt="Logo" class="h-12 md:h-16 w-auto object-contain">
                <a href="{{ route('google.redirect') }}"
                    class="flex items-center justify-center gap-3 px-4 py-2 bg-white border-[1.5px] border-green-100 text-green-900 rounded-full shadow-sm hover:border-green-400 hover:shadow-green-400/20 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#4285F4" d="M533.5 278.4c0-17.4-1.6-34-4.8-50H272v95.2h146.9c-6.3 34-25 62.9-53.1 82l85.7 66.6c50-46.1 78-114 78-193.8z" />
                        <path fill="#34A853" d="M272 544.3c71.7 0 132-23.8 176-64.6l-85.7-66.6c-23.8 16-54.2 25.5-90.3 25.5-69.3 0-128-46.9-149-110.1l-87.1 67.4c44 87.7 134.3 148.4 236.1 148.4z" />
                        <path fill="#FBBC05" d="M123 323.1c-10-29.4-10-61.4 0-90.8l-87.1-67.4c-38.3 76.7-38.3 167.8 0 244.5l87.1 67.3z" />
                        <path fill="#EA4335" d="M272 107.6c37.5 0 71 12.9 97.4 34.6l73-73C404 24 344.8 0 272 0 170.2 0 80 60.7 36 148.4l87.1 67.4c21-63.2 79.7-110.1 148.9-110.1z" />
                    </svg>
                    <span class="text-sm font-medium tracking-wide">Sign In</span>
                </a>
            </div>

            {{-- Tagline --}}
            <div class="absolute bottom-0 left-0 right-0 z-10 px-5 md:px-8 mb-1">
                <div class="w-10 h-0.5 bg-gradient-to-r from-green-300 to-transparent rounded-full mt-3"></div>
                <h1 class="text-2xl md:text-3xl font-light italic text-white leading-tight">
                    Where every plant tells a <span class="not-italic font-semibold text-white">story.</span>
                </h1>
            </div>
        </div>

        {{-- Nursery list --}}
        <div class="px-4 md:px-8 pt-6 pb-12 flex flex-col gap-6 max-w-7xl mx-auto">
            <p class="text-xs font-bold tracking-[2px] uppercase text-green-400/50">Featured Nurseries</p>

            @foreach ($nurseries as $nursery)
                @if ($nursery->plants->count())
                    <div class="border border-green-800/60 rounded-2xl overflow-hidden bg-green-950/50 shadow-lg">

                        {{-- Nursery header --}}
                        <button
                            onclick="toggleNursery(this)"
                            class="w-full flex items-center justify-between px-5 py-4 bg-green-900/40 hover:bg-green-900/60 text-left transition-colors duration-200 focus:outline-none">
                            <div>
                                <h2 class="text-base font-bold text-white">{{ $nursery->name }}</h2>
                                <p class="text-xs text-white mt-1">{{ $nursery->plants->count() }} plants available</p>
                            </div>
                            <span class="text-white transform transition-transform duration-300">▼</span>
                        </button>

                        {{-- Collapsible Content Wrapper --}}
                        <div class="nursery-content transition-all">
                            {{-- Plants grid --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-px bg-green-800/40">
                                @foreach ($nursery->plants as $plant)
                                    {{-- Notice the added plant-card class and the loop condition for the hidden class --}}
                                    <div class="plant-card-{{ $nursery->id }} bg-green-950 p-3 flex flex-col group hover:bg-green-900/20 transition-colors duration-200 {{ $loop->iteration > 6 ? 'hidden' : '' }}">

                                        {{-- Image --}}
                                        @if ($plant->image)
                                            <img src="{{ asset('storage/plants/' . $plant->image) }}"
                                                alt="{{ $plant->name }}"
                                                loading="lazy"
                                                class="w-full h-32 sm:h-40 object-cover rounded-xl mb-3 group-hover:opacity-90 transition-opacity">
                                        @else
                                            <div class="w-full h-32 sm:h-40 rounded-xl bg-green-900/40 flex items-center justify-center mb-3">
                                                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                                                        d="M12 22V12m0 0C12 7 8 4 4 5c0 4 2.5 7 8 7zm0 0c0-5 4-8 8-7-1 4-3 7-8 7" />
                                                </svg>
                                            </div>
                                        @endif

                                        {{-- Name --}}
                                        <p class="text-sm font-bold text-white truncate" title="{{ $plant->name }}">{{ $plant->name }}</p>

                                        {{-- Tags --}}
                                        <div class="flex flex-wrap gap-1.5 mb-2.5 min-h-[20px] mt-1">
                                            @if ($plant->category)
                                                <span class="text-[9px] font-medium bg-green-900/60 text-green-300 rounded-md px-2 py-0.5 border border-green-800/50">
                                                    {{ ucfirst($plant->category) }}
                                                </span>
                                            @endif
                                            @if ($plant->best_season)
                                                <span class="text-[9px] font-medium bg-green-900/60 text-green-300 rounded-md px-2 py-0.5 border border-green-800/50">
                                                    {{ ucfirst($plant->best_season) }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Price --}}
                                        <div class="flex items-baseline gap-2 mt-auto pt-2">
                                            @if ($plant->offer_price && $plant->offer_price < $plant->selling_price)
                                                <span class="text-[11px] text-white/70 line-through decoration-white/70 font-medium">
                                                    Rs. {{ number_format($plant->selling_price, 0) }}
                                                </span>
                                                <span class="text-sm font-bold text-white">
                                                    Rs. {{ number_format($plant->offer_price, 0) }}
                                                </span>
                                            @else
                                                <span class="text-sm font-bold text-white">
                                                    Rs. {{ number_format($plant->selling_price, 0) }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                            {{-- Show More Button --}}
                            @if ($nursery->plants->count() > 6)
                                <div class="show-more-container-{{ $nursery->id }} bg-green-900/20 p-4 flex justify-center border-t border-green-800/40">
                                    <button 
                                        onclick="showMorePlants('{{ $nursery->id }}', this)" 
                                        class="text-xs tracking-wide font-semibold text-green-100 hover:text-white uppercase px-6 py-2.5 bg-green-800/60 hover:bg-green-700/80 rounded-full border border-green-600/30 transition-all duration-200">
                                        Show More
                                    </button>
                                </div>
                            @endif
                        </div>

                    </div>
                @endif
            @endforeach
        </div>

    </div>

    <script>
        function toggleNursery(btn) {
            // Target the wrapper div now instead of just the grid
            const content = btn.nextElementSibling;
            const chevron = btn.querySelector('span');
            
            content.classList.toggle('hidden');
            
            if (content.classList.contains('hidden')) {
                chevron.style.transform = 'rotate(-90deg)';
            } else {
                chevron.style.transform = 'rotate(0deg)';
            }
        }

        function showMorePlants(nurseryId, btnElement) {
            // Find all hidden plants for this specific nursery
            const hiddenPlants = document.querySelectorAll(`.plant-card-${nurseryId}.hidden`);
            
            // Unhide up to 10 plants at a time
            const itemsToShow = Math.min(10, hiddenPlants.length);
            for (let i = 0; i < itemsToShow; i++) {
                hiddenPlants[i].classList.remove('hidden');
            }
            
            // If there are no more hidden plants left, hide the "Show More" button entirely
            const remainingHidden = document.querySelectorAll(`.plant-card-${nurseryId}.hidden`);
            if (remainingHidden.length === 0) {
                btnElement.closest(`.show-more-container-${nurseryId}`).style.display = 'none';
            }
        }
    </script>
</x-app-layout>