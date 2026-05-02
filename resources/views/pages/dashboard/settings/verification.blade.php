<x-app-layout>
    <div class="flex flex-col items-center justify-center min-h-screen px-4 bg-gray-50">
        <div class="bg-white rounded-2xl shadow-md p-8 max-w-md w-full text-center">

            <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mx-auto mb-5">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Verify your email</h1>
            <p class="text-gray-500 text-sm mb-6">
                We sent a verification link to
                <span class="font-medium text-gray-700">
                    {{ Auth::check() ? Auth::user()->email : 'not authenticated' }}
                </span>
                Click it to activate your FloraNepal account.
            </p>

            @if (session('status') === 'verification-link-sent')
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-5">
                A new verification link has been sent to your email.
            </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl transition duration-200">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 transition">
                    Log out
                </button>
            </form>

        </div>
    </div>
    <script>
        const interval = setInterval(async () => {
            try {
                const res = await fetch('/auth/check-verification', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();

                if (data.verified) {
                    clearInterval(interval);
                    window.location.href = '/dashboard';
                }
            } catch (e) {
                // silently ignore
            }
        }, 3000);
    </script>
</x-app-layout>