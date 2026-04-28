<!-- resources/views/components/app-layout.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'FloraNepal' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/FloraNepalLogoOnly.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp 0.55s ease forwards;
        }

        .fade-up>* {
            animation: fadeUp 0.55s ease forwards;
            opacity: 0;
        }

        .fade-up>*:nth-child(1) {
            animation-delay: 0.05s;
        }

        .fade-up>*:nth-child(2) {
            animation-delay: 0.15s;
        }

        .fade-up>*:nth-child(3) {
            animation-delay: 0.25s;
        }

        .fade-up>*:nth-child(4) {
            animation-delay: 0.35s;
        }

        .fade-up>*:nth-child(5) {
            animation-delay: 0.45s;
        }

        .fade-up>*:nth-child(6) {
            animation-delay: 0.55s;
        }

        .fade-up>*:nth-child(7) {
            animation-delay: 0.65s;
        }

        .fade-up>*:nth-child(8) {
            animation-delay: 0.75s;
        }

        .fade-up>*:nth-child(9) {
            animation-delay: 0.85s;
        }

        .fade-up>*:nth-child(10) {
            animation-delay: 0.95s;
        }

        .fade-up>*:nth-child(11) {
            animation-delay: 1.05s;
        }
    </style>
</head>

<body class="font-sans h-screen">

    {{ $slot }}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "progressBar": true,
            "closeButton": true,
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    </script>
</body>

</html>