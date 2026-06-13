<div class="fade-up flex flex justify-between items-start mb-6 gap-4 px-4">
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
            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'Plant limit reached!', message: 'Upgrade to premium to add more plants.' } }));
        @else
            navigate('{{ route('plants.create') }}', 'nurseries.plants.create', 'Add Plant')
        @endif
    " class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm mt-2">
        Add Plant
    </a>
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