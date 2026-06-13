<x-app-layout :title="ucfirst($page)">
    <div x-data="adminApp()" class="flex h-screen">

        <!-- Mobile Backdrop Overlay -->
        <div x-show="mobileSidebarOpen" x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="mobileSidebarOpen = false" class="fixed inset-0 bg-black/40 z-30"
            style="display: none;">
        </div>

        <!-- Sidebar -->
        <aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 w-64
                   bg-white border-r-2 text-white flex flex-col
                   transition-all duration-300 overflow-hidden" style="box-shadow: -2px 0px 15px rgba(0,0,0,0.25);">

            <!-- Header: Logo -->
            <div class="relative flex items-center h-14 px-3 my-2 shrink-0 justify-between">
                <img src="{{ asset('images/FloraNepalHorizontal.png') }}" alt="Logo" x-show="mobileSidebarOpen"
                    x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-100"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="w-36 object-contain ml-3">
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-2 space-y-1 mt-2 overflow-y-auto flex flex-col pb-4">
                <h2 x-show="mobileSidebarOpen"
                    class="text-gray-400 text-xs font-semibold uppercase tracking-wider ml-5 mb-1">
                    Admin
                </h2>

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    @click.prevent="navigate('{{ route('admin.dashboard') }}', 'dashboard', 'Dashboard')"
                    :class="isActive('dashboard') ? 'text-black' : 'text-gray-500'"
                    class="group flex items-center gap-3 px-4 py-2 rounded relative transition font-bold">
                    <span :class="isActive('dashboard') ? 'bg-[#16714B]' : 'bg-transparent group-hover:bg-[#16714B]'"
                        class="absolute left-0 top-0 h-full w-1.5 rounded-r transition-all"></span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        :class="isActive('dashboard') ? 'text-[#16714B]' : 'text-gray-500 group-hover:text-[#16714B]'"
                        class="w-7 h-7 shrink-0 transition-colors" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <rect width="7" height="9" x="3" y="3" rx="1" />
                        <rect width="7" height="5" x="14" y="3" rx="1" />
                        <rect width="7" height="9" x="14" y="12" rx="1" />
                        <rect width="7" height="5" x="3" y="16" rx="1" />
                    </svg>
                    <span x-show="mobileSidebarOpen" x-transition
                        :class="isActive('dashboard') ? 'text-black' : 'group-hover:text-black'"
                        class="whitespace-nowrap transition-colors text-m">
                        Dashboard
                    </span>
                </a>

                <!-- Users -->
                <a href="{{ route('admin.users') }}"
                    @click.prevent="navigate('{{ route('admin.users') }}', 'users.index', 'Users')"
                    :class="isActive('users.index') || isActive('users.show') ? 'text-black' : 'text-gray-500'"
                    class="group flex items-center gap-3 px-4 py-2 rounded relative transition font-bold">
                    <span :class="isActive('users.index') || isActive('users.show') ? 'bg-[#16714B]' :
                            'bg-transparent group-hover:bg-[#16714B]'"
                        class="absolute left-0 top-0 h-full w-1.5 rounded-r transition-all"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" :class="isActive('users.index') || isActive('users.show') ? 'text-[#16714B]' :
                            'text-gray-500 group-hover:text-[#16714B]'" class="w-7 h-7 shrink-0 transition-colors"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    <span x-show="mobileSidebarOpen" x-transition
                        :class="isActive('users.index') || isActive('users.show') ? 'text-black' : 'group-hover:text-black'"
                        class="whitespace-nowrap transition-colors text-m">
                        Users
                    </span>
                </a>

                <!-- Nurseries -->
                <a href="{{ route('admin.nurseries') }}"
                    @click.prevent="navigate('{{ route('admin.nurseries') }}', 'nurseries.index', 'Nurseries')"
                    :class="isActive('nurseries.index') || isActive('nurseries.show') ? 'text-black' : 'text-gray-500'"
                    class="group flex items-center gap-3 px-4 py-2 rounded relative transition font-bold">
                    <span :class="isActive('nurseries.index') || isActive('nurseries.show') ? 'bg-[#16714B]' :
                            'bg-transparent group-hover:bg-[#16714B]'"
                        class="absolute left-0 top-0 h-full w-1.5 rounded-r transition-all"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" :class="isActive('nurseries.index') || isActive('nurseries.show') ? 'text-[#16714B]' :
                            'text-gray-500 group-hover:text-[#16714B]'" class="w-7 h-7 shrink-0 transition-colors"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M12 5a3 3 0 1 1 3 3m-3-3a3 3 0 1 0-3 3m3-3v1" />
                        <path d="M9 8a3 3 0 1 0 3 3M9 8h1m5 0a3 3 0 1 1-3 3m3-3h-1m-2 3v-1" />
                        <circle cx="12" cy="8" r="2" />
                        <path d="M12 10v12" />
                        <path d="M12 22c4.2 0 7-1.667 7-5-4.2 0-7 1.667-7 5Z" />
                        <path d="M12 22c-4.2 0-7-1.667-7-5 4.2 0 7 1.667 7 5Z" />
                    </svg>
                    <span x-show="mobileSidebarOpen" x-transition :class="isActive('nurseries.index') || isActive('nurseries.show') ? 'text-black' :
                            'group-hover:text-black'" class="whitespace-nowrap transition-colors text-m">
                        Nurseries
                    </span>
                </a>

                <!-- Plant Options -->
                <a href="{{ route('admin.plant-options') }}"
                    @click.prevent="navigate('{{ route('admin.plant-options') }}', 'plant-options', 'Plant Options')"
                    :class="isActive('plant-options') ? 'text-black' : 'text-gray-500'"
                    class="group flex items-center gap-3 px-4 py-2 rounded relative transition font-bold">
                    <span
                        :class="isActive('plant-options') ? 'bg-[#16714B]' : 'bg-transparent group-hover:bg-[#16714B]'"
                        class="absolute left-0 top-0 h-full w-1.5 rounded-r transition-all"></span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        :class="isActive('plant-options') ? 'text-[#16714B]' : 'text-gray-500 group-hover:text-[#16714B]'"
                        class="w-7 h-7 shrink-0 transition-colors" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M12 5a3 3 0 1 1 3 3m-3-3a3 3 0 1 0-3 3m3-3v1" />
                        <path d="M9 8a3 3 0 1 0 3 3M9 8h1m5 0a3 3 0 1 1-3 3m3-3h-1m-2 3v-1" />
                        <circle cx="12" cy="8" r="2" />
                        <path d="M12 10v12" />
                        <path d="M12 22c4.2 0 7-1.667 7-5-4.2 0-7 1.667-7 5Z" />
                        <path d="M12 22c-4.2 0-7-1.667-7-5 4.2 0 7 1.667 7 5Z" />
                    </svg>
                    <span x-show="mobileSidebarOpen" x-transition
                        :class="isActive('plant-options') ? 'text-black' : 'group-hover:text-black'"
                        class="whitespace-nowrap transition-colors text-m">
                        Plant Options
                    </span>
                </a>

                <!-- Spacer -->
                <div class="flex-1"></div>
                <!-- Logout -->
                <a href="#" onclick="event.preventDefault(); this.querySelector('form').submit();"
                    class="group flex items-center gap-3 px-4 py-2 rounded relative transition font-bold justify-start">
                    <span
                        class="absolute left-0 top-0 h-full w-1.5 rounded-r transition-all group-hover:bg-[#16714B]"></span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-7 h-7 shrink-0 text-gray-500 group-hover:text-[#16714B] transition-all" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        viewBox="0 0 24 24">
                        <path d="m16 17 5-5-5-5" />
                        <path d="M21 12H9" />
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    </svg>
                    <span x-show="mobileSidebarOpen" x-transition
                        class="whitespace-nowrap transition-colors text-m text-gray-500 group-hover:text-black">
                        Logout
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </a>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-auto bg-white">

            <!-- Mobile Top Bar -->
            <div
                class="mobile-topbar flex items-center justify-between px-4 border-b border-gray-200 bg-white sticky top-0 z-20">
                <button @click="mobileSidebarOpen = true" class="text-gray-600 hover:text-gray-900 p-1 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <img src="{{ asset('images/FloraNepalHorizontal.png') }}" alt="Logo" class="w-40 my-2 object-contain">
            </div>

            <!-- Loading indicator -->
            <div id="nav-loading"
                class="hidden fixed top-0 left-0 right-0 h-0.5 bg-[#16714B] z-50 transition-all duration-300"
                style="width: 0%;">
            </div>

            <div id="main-content" class="max-w-8xl mx-auto py-6 px-4">
                @include('pages.admin.' . str_replace('.', '/', $page))
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminApp', () => ({
                mobileSidebarOpen: false,
                currentPage: '{{ $page ?? 'dashboard' }}',
                loading: false,

                isActive(page) {
                    return this.currentPage === page;
                },

                navigate(url, page, title) {
                    if (this.loading) return;
                    this.loading = true;
                    this.mobileSidebarOpen = false;

                    const bar = document.getElementById('nav-loading');
                    bar.classList.remove('hidden');
                    bar.style.width = '60%';

                    fetch(url, {
                        headers: {
                            'X-Dashboard-Navigate': 'true',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                        .then(r => {
                            if (!r.ok) throw new Error('Navigation failed');
                            return r.text();
                        })
                        .then(html => {
                            const content = document.getElementById('main-content');
                            content.innerHTML = html;
                            this.currentPage = page;
                            document.title = title ?? page;
                            window.history.pushState({
                                page,
                                url
                            }, '', url);
                            bar.style.width = '100%';
                            setTimeout(() => {
                                bar.classList.add('hidden');
                                bar.style.width = '0%';
                            }, 300);
                        })
                        .catch(() => {
                            window.location.href = url;
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                }
            }))
        })

        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!document.getElementById('main-content').contains(form)) return;
            e.preventDefault();

            const formData = new FormData(form);
            const action = form.getAttribute('action');

            fetch(action, {
                method: 'POST',
                headers: {
                    'X-Dashboard-Navigate': 'true',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData,
                redirect: 'follow'
            })
                .then(r => {
                    const finalUrl = r.url; // capture final URL after redirect
                    return r.text().then(html => ({ html, finalUrl }));
                })
                .then(({ html, finalUrl }) => {
                    document.getElementById('main-content').innerHTML = html;
                    window.history.pushState({}, '', finalUrl);
                    Alpine.initTree(document.getElementById('main-content'));

                    // Restore topbar
                    const topbar = document.querySelector('.mobile-topbar');
                    if (topbar) topbar.style.display = 'flex';

                    // Update active page from final URL
                    const app = Alpine.$data(document.querySelector('[x-data="adminApp"]'));
                    if (app) {
                        // Extract page from URL e.g. /admin/users/3 -> users.show
                        const segments = new URL(finalUrl).pathname.replace('/admin/', '').split('/');
                        if (segments.length === 1) app.currentPage = segments[0];
                        else if (segments.length >= 2) app.currentPage = segments[0] + '.show';
                    }
                })
                .catch(() => form.submit());
        });

        window.addEventListener('pageshow', function (e) {
            if (e.persisted) {
                window.location.reload();
            }
        });

        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.url) {
                fetch(e.state.url, {
                    headers: {
                        'X-Dashboard-Navigate': 'true'
                    }
                })
                    .then(r => r.text())
                    .then(html => {
                        document.getElementById('main-content').innerHTML = html;
                        const topbar = document.querySelector('.mobile-topbar');
                        if (topbar) topbar.style.display = 'flex';
                    });
            }
        });
    </script>
</x-app-layout>