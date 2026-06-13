@php
    use Illuminate\Support\Facades\Auth;
@endphp

<div x-data="{ editing: false }" x-init="$watch('editing', val => {
    document.querySelector('.mobile-topbar').style.display = val ? 'none' : 'flex'
})">

    {{-- View mode --}}
    <div x-show="!editing">

        {{-- Header --}}
        <div class="flex flex-col px-4 py-5 bg-gray-50">
            <div class="w-16 h-16 rounded-full bg-green-800 flex items-center justify-center text-white text-2xl font-bold shadow mb-3">
                {{ strtoupper(substr($nursery->name, 0, 1)) }}
            </div>
            <h2 class="text-xl font-bold text-gray-900">{{ $nursery->name }}</h2>
            <p class="text-sm text-gray-400 mt-0.5">{{ $nursery->user->name ?? 'No owner' }} ·
                {{ $nursery->user->email ?? '' }}
            </p>
            <div class="flex gap-2 mt-2">
                <span @class([
                    'text-xs px-3 py-1 rounded-full font-medium',
                    'bg-green-100 text-green-800' => $nursery->is_active,
                    'bg-red-100 text-red-600' => !$nursery->is_active,
                ])>
                    {{ $nursery->is_active ? 'Active' : 'Inactive' }}
                </span>
                <span class="text-xs px-3 py-1 rounded-full font-medium bg-green-100 text-green-800">
                    {{ $nursery->plants->count() }} plants
                </span>
            </div>
        </div>{{-- END Header --}}

        {{-- Info --}}
        <div class="px-4 py-5 flex flex-col gap-3">

            <div class="bg-gray-50 rounded-xl p-4 flex flex-col gap-3">
                <div class="flex justify-between">
                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wide">Location</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $nursery->location ?? 'Not set' }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-100 pt-3">
                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wide">Contact Phone</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $nursery->contact_phone ?? 'Not set' }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-100 pt-3">
                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wide">Contact Email</span>
                    <span class="text-sm font-semibold text-gray-900 truncate ml-4">{{ $nursery->contact_email ?? 'Not set' }}</span>
                </div>
                @if ($nursery->description)
                    <div class="flex flex-col border-t border-gray-100 pt-3 gap-1">
                        <span class="text-xs text-gray-400 font-medium uppercase tracking-wide">Description</span>
                        <span class="text-sm text-gray-700 leading-relaxed">{{ $nursery->description }}</span>
                    </div>
                @endif
            </div>

            {{-- Certificates + Verify --}}
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Certificates</p>
                    @if ($nursery->user)
                        @if ($nursery->user->verification_status === 'verified')
                            <span class="text-xs px-3 py-1 rounded-full font-medium bg-green-100 text-green-800">Verified</span>
                        @else
                            <form action="{{ route('admin.nurseries.verify', $nursery) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="text-xs px-3 py-1 rounded-full font-semibold bg-green-700 text-white hover:bg-green-800 transition">
                                    Verify Owner
                                </button>
                            </form>
                        @endif
                    @else
                        <span class="text-xs px-3 py-1 rounded-full font-medium bg-gray-200 text-gray-400">No owner</span>
                    @endif
                </div>

                @php $certOwnerId = $nursery->cert_owner_id; @endphp

                <div class="flex gap-4">
                    {{-- Registration Certificate --}}
                    <div class="flex flex-col items-center gap-1 flex-1">
                        <p class="text-xs text-gray-500 mb-1">Registration</p>
                        @if ($nursery->reg_cer && $certOwnerId)
                            @php $regUrl = route('admin.file.view', [$certOwnerId, $nursery->reg_cer]); @endphp
                            @if (str_ends_with(strtolower($nursery->reg_cer), '.pdf'))
                                <a href="{{ $regUrl }}" target="_blank"
                                   class="w-28 h-28 bg-red-50 border border-red-200 rounded-lg flex flex-col items-center justify-center gap-1 hover:bg-red-100 transition">
                                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs text-red-500 font-medium">View PDF</span>
                                </a>
                            @else
                                <a href="{{ $regUrl }}" target="_blank">
                                    <img src="{{ $regUrl }}" alt="Registration Certificate"
                                         class="w-28 h-28 object-cover rounded-lg hover:opacity-80 transition">
                                </a>
                            @endif
                        @else
                            <div class="w-28 h-28 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-400">No file</div>
                        @endif
                    </div>

                    {{-- PAN Certificate --}}
                    <div class="flex flex-col items-center gap-1 flex-1">
                        <p class="text-xs text-gray-500 mb-1">PAN</p>
                        @if ($nursery->pan_cer && $certOwnerId)
                            @php $panUrl = route('admin.file.view', [$certOwnerId, $nursery->pan_cer]); @endphp
                            @if (str_ends_with(strtolower($nursery->pan_cer), '.pdf'))
                                <a href="{{ $panUrl }}" target="_blank"
                                   class="w-28 h-28 bg-red-50 border border-red-200 rounded-lg flex flex-col items-center justify-center gap-1 hover:bg-red-100 transition">
                                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs text-red-500 font-medium">View PDF</span>
                                </a>
                            @else
                                <a href="{{ $panUrl }}" target="_blank">
                                    <img src="{{ $panUrl }}" alt="PAN Certificate"
                                         class="w-28 h-28 object-cover rounded-lg hover:opacity-80 transition">
                                </a>
                            @endif
                        @else
                            <div class="w-28 h-28 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-400">No file</div>
                        @endif
                    </div>
                </div>
            </div>{{-- END Certificates --}}

            {{-- Plants --}}
            @if ($nursery->plants->count())
                <div class="flex flex-col gap-2">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Plants</p>
                    @foreach ($nursery->plants as $plant)
                        <button
                            @click="navigate('{{ route('admin.nurseries.plants.show', [$nursery, $plant]) }}', 'nurseries.plants.show', '{{ $plant->name }}')"
                            class="w-full bg-white border border-gray-200 rounded-xl flex items-center p-3 gap-3 text-left">
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
                        </button>
                    @endforeach
                </div>
            @endif
            {{-- END Plants --}}

        </div>{{-- END Info --}}

        {{-- Delete / Edit --}}
        <div class="flex gap-3 px-4 mb-4">
            <form action="{{ route('admin.nurseries.destroy', $nursery) }}" method="POST" class="w-1/2"
                onsubmit="return confirm('Are you sure you want to delete this nursery?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-50 text-red-600 font-semibold text-sm py-3 rounded-xl">
                    Delete nursery
                </button>
            </form>
            <button type="button" @click="editing = true"
                class="w-1/2 bg-green-800 text-white text-sm font-semibold py-3 rounded-xl">
                Edit
            </button>
        </div>{{-- END Delete/Edit --}}

    </div>{{-- END View mode --}}

    {{-- Edit mode --}}
    <div x-show="editing">
        <form action="{{ route('admin.nurseries.update', $nursery) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="px-4 py-5 flex flex-col gap-4">

                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $nursery->name) }}"
                        class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-green-700 outline-none">
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1">Location</label>
                    <input type="text" name="location" value="{{ old('location', $nursery->location) }}"
                        class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-green-700 outline-none">
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $nursery->contact_phone) }}"
                        class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-green-700 outline-none">
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $nursery->contact_email) }}"
                        class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-green-700 outline-none">
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full border-2 border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-green-700 outline-none resize-none">{{ old('description', $nursery->description) }}</textarea>
                </div>

                {{-- Registration Certificate --}}
                <div x-data="{ sizeError: false, cleared: false }">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1">Registration Certificate</label>
                    <label
                        class="relative block w-full h-32 bg-gray-100 rounded-xl flex flex-col items-center justify-center gap-2 cursor-pointer overflow-hidden border-2 border-gray-200"
                        :class="cleared ? 'opacity-40 pointer-events-none' : ''">
                        @if ($nursery->reg_cer && $nursery->cert_owner_id)
                            <img src="{{ route('admin.file.view', [$nursery->cert_owner_id, $nursery->reg_cer]) }}"
                                class="absolute inset-0 w-full h-full object-cover opacity-60">
                        @endif
                        <svg class="w-6 h-6 text-gray-600 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" />
                        </svg>
                        <span class="text-xs text-gray-600 relative z-10">Tap to change</span>
                        <input type="file" name="reg_cer" class="hidden" accept="image/*,application/pdf">
                    </label>
                    <input type="hidden" name="clear_reg_cer" :value="cleared ? '1' : '0'">
                    <p x-show="sizeError" class="text-red-500 text-xs mt-1">File must be under 2MB.</p>
                    <button type="button" @click="cleared = !cleared"
                        :class="cleared ? 'bg-gray-100 text-gray-400' : 'bg-red-50 text-red-600'"
                        class="mt-2 w-full text-xs font-semibold py-2 rounded-xl">
                        <span x-text="cleared ? 'Undo clear' : 'Clear certificate'"></span>
                    </button>
                </div>{{-- END Registration Certificate --}}

                {{-- PAN Certificate --}}
                <div x-data="{ sizeError: false, cleared: false }">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide block mb-1">PAN Certificate</label>
                    <label
                        class="relative block w-full h-32 bg-gray-100 rounded-xl flex flex-col items-center justify-center gap-2 cursor-pointer overflow-hidden border-2 border-gray-200"
                        :class="cleared ? 'opacity-40 pointer-events-none' : ''">
                        @if ($nursery->pan_cer && $nursery->cert_owner_id)
                            <img src="{{ route('admin.file.view', [$nursery->cert_owner_id, $nursery->pan_cer]) }}"
                                alt="PAN Certificate" class="absolute inset-0 w-full h-full object-cover opacity-60">
                        @endif
                        <svg class="w-6 h-6 text-gray-600 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" />
                        </svg>
                        <span class="text-xs text-gray-600 relative z-10">Tap to change</span>
                        <input type="file" name="pan_cer" class="hidden" accept="image/*,application/pdf">
                    </label>
                    <input type="hidden" name="clear_pan_cer" :value="cleared ? '1' : '0'">
                    <p x-show="sizeError" class="text-red-500 text-xs mt-1">File must be under 2MB.</p>
                    <button type="button" @click="cleared = !cleared"
                        :class="cleared ? 'bg-gray-100 text-gray-400' : 'bg-red-50 text-red-600'"
                        class="mt-2 w-full text-xs font-semibold py-2 rounded-xl">
                        <span x-text="cleared ? 'Undo clear' : 'Clear certificate'"></span>
                    </button>
                </div>{{-- END PAN Certificate --}}

                <div class="flex items-center py-3 gap-3">
                    <button type="button" @click="editing = false"
                        class="w-1/2 bg-gray-100 text-gray-600 text-sm font-semibold py-3 rounded-xl">
                        Cancel
                    </button>
                    <button type="submit" class="w-1/2 bg-green-800 text-white text-sm font-semibold py-3 rounded-xl">
                        Save
                    </button>
                </div>

            </div>
        </form>
    </div>{{-- END Edit mode --}}

</div>{{-- END x-data --}}

<script>
    document.addEventListener('alpine:init', () => {
        const topbar = document.querySelector('.mobile-topbar');
        if (topbar) topbar.style.display = 'flex';
    });
</script>