<div x-data="{ limitModal: false }">

    {{-- Plant limit modal --}}
    <div x-show="limitModal" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         style="background: rgba(0,0,0,0.5);">
        <div x-show="limitModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Plant limit reached</h3>
            <p class="text-sm text-gray-500 mb-6">
                Free accounts are limited to <strong>5 plants</strong>. Upgrade to Premium to add unlimited plants.
            </p>
            <div class="flex gap-3">
                <button @click="limitModal = false"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <a href="{{ route('subscription') }}"
                   @click.prevent="navigate('{{ route('subscription') }}', 'payment.subscription', 'Subscription')"
                   class="flex-1 py-2.5 rounded-xl bg-green-700 text-white text-sm font-semibold hover:bg-green-800 transition text-center">
                    Upgrade
                </a>
            </div>
        </div>
    </div>

    <div class="fade-up flex justify-between items-start mb-6 gap-4 px-4">
        <div>
            <h1 class="text-2xl font-bold">
                {{ $nursery->name }} 🌱
            </h1>
            <p class="text-gray-600 text-sm">
                {{ $nursery->location ?? 'No location provided' }}
            </p>
        </div>
        <a href="{{ route('plants.create') }}" @click.prevent="
            @if (Auth::user()->subscription_type === 'general' && $nursery->plants()->count() >= 5)
                limitModal = true
            @else
                navigate('{{ route('plants.create') }}', 'nurseries.plants.create', 'Add Plant')
            @endif
        " class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm mt-2">
            Add Plant
        </a>
    </div>

</div>

{{-- Nursery Description --}}
<div class="fade-up delay-1 mb-6 text-gray-700 px-4">
    {{ $nursery->description ?? 'No description available.' }}
</div>

<h2 class="fade-up delay-3 text-xl font-semibold mb-4 px-4">Available Plants</h2>

@if ($nursery->plants->count())
    <div class="flex flex-col gap-2 px-4">
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Plants</p>
        @foreach ($nursery->plants as $plant)
            <div class="bg-white border border-gray-200 rounded-xl flex items-center p-3 gap-3 hover:-translate-y-1 transition-all duration-300"
                onclick="window.location='{{ route('plants.show', $plant) }}'">
                @if ($plant->image)
                    <img src="{{ asset('storage/plants/' . $plant->image) }}" alt="{{ $plant->name }}"
                        class="w-12 h-12 object-cover rounded-lg flex-shrink-0">
                @else
                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $plant->name }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst($plant->category ?? 'Uncategorized') }}</p>
                </div>
                <p class="text-sm font-semibold text-green-800 flex-shrink-0">Rs.
                    {{ number_format($plant->offer_price, 0) }}
                </p>
            </div>
        @endforeach
    </div>
@else
    <div class="fade-up delay-3 text-gray-500 mt-10 text-center">
        No plants available in this nursery 🌵
    </div>
@endif