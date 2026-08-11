@php($hideChrome = true)
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — LibraTech</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=literata:400,600,700&family=hanken-grotesk:400,500,600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        'primary-deep': '#3525cd',
                        surface: '#fcf8ff',
                        'surface-low': '#f5f2ff',
                        ink: '#1b1b24',
                        muted: '#464555',
                        line: '#c7c4d8',
                        'line-soft': '#e2e8f0',
                        accent2: '#82f5c1',
                    },
                    fontFamily: {
                        display: ['Literata','Georgia','serif'],
                        sans: ['Hanken Grotesk','system-ui','sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        :root{ --radius-card:16px; --radius-input:10px; }
        *{ scrollbar-color:#c7c4d8 transparent }
        @keyframes authFadeUp{ from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .auth-animate{ animation:authFadeUp 600ms ease-out both }
        .auth-d1{ animation-delay:80ms } .auth-d2{ animation-delay:160ms } .auth-d3{ animation-delay:240ms }
        @media(prefers-reduced-motion:reduce){ .auth-animate{animation:none;opacity:1;transform:none} }
    </style>
</head>
<body class="min-h-screen bg-surface text-ink font-sans antialiased">
<div class="flex min-h-screen">
    {{-- Left: quote / editorial panel — Stitch 56d — hidden mobile --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-[#e4e1ee] border-r border-line/60">
        <img src="https://images.unsplash.com/photo-1519682337058-a94d519337bc?auto=format&fit=crop&w=1200&q=80" alt="Rak buku perpustakaan" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-primary-deep/85 via-primary-deep/30 to-transparent"></div>
        <div class="relative flex flex-col justify-end p-10 xl:p-12 w-full">
            <blockquote class="auth-animate font-display text-[28px] xl:text-[30px] leading-[1.25] text-white max-w-[22ch] font-semibold tracking-[-0.02em]">
                “A room without books is like a body without a soul.”
            </blockquote>
            <p class="auth-animate auth-d1 mt-3 text-white/85 text-[15px] font-sans">— Marcus Tullius Cicero</p>
            <p class="auth-animate auth-d2 mt-8 text-white/70 text-xs tracking-[0.12em] uppercase">Literary Modernist · Warm Editorial</p>
        </div>
    </div>

    {{-- Right: form — Flux + editorial --}}
    <div class="flex flex-1 flex-col justify-center px-5 sm:px-8 lg:px-12 xl:px-16 py-10 bg-white lg:bg-surface">
        <div class="mx-auto w-full max-w-[400px]">
            <div class="auth-animate flex items-center gap-2.5 mb-8">
                <span class="w-9 h-9 rounded-[10px] bg-primary text-white grid place-items-center">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1">auto_stories</span>
                </span>
                <span class="font-display font-bold text-[22px] tracking-tight text-ink">LibraTech</span>
                <span class="hidden sm:inline text-[11px] tracking-[0.14em] uppercase text-muted border-l border-line pl-2.5 ml-1">Perpustakaan</span>
            </div>

            <div class="auth-animate auth-d1">
                <h1 class="font-display text-[28px] leading-[1.15] font-semibold tracking-[-0.02em] text-ink">Selamat Datang Kembali</h1>
                <p class="mt-2 text-[14.5px] leading-6 text-muted">Silakan masuk ke akun Anda.</p>
            </div>

            @if($errors->any())
                <div class="auth-animate auth-d1 mt-5 rounded-[10px] border border-red-200 bg-[#ffdad6]/60 px-3.5 py-3 text-sm text-[#93000a]">
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="auth-animate auth-d2 mt-7 space-y-4" x-data="{ show:false }" novalidate>
                @csrf
                <flux:field>
                    <flux:label>Alamat Email</flux:label>
                    <flux:input type="email" name="email" value="{{ old('email') }}" placeholder="anda@contoh.com" autocomplete="email" required :invalid="$errors->has('email')" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <div class="flex items-center justify-between">
                        <flux:label>Kata Sandi</flux:label>
                        <button type="button" class="text-[11px] tracking-[0.06em] uppercase font-semibold text-primary hover:text-primary-deep" @click="show=!show" x-text="show ? 'Sembunyikan' : 'Lihat'"></button>
                    </div>
                    <div class="relative">
                        <flux:input x-bind:type="show ? 'text' : 'password'" name="password" placeholder="••••••••" autocomplete="current-password" required :invalid="$errors->has('password')" />
                    </div>
                    <flux:error name="password" />
                </flux:field>

                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-2 text-sm text-ink cursor-pointer select-none">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 rounded border-line text-primary focus:ring-primary">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm font-semibold text-primary hover:text-primary-deep">Lupa kata sandi?</a>
                </div>

                <flux:button variant="primary" type="submit" class="w-full mt-2 justify-center">
                    Masuk
                </flux:button>

                <div class="rounded-[10px] border border-line-soft bg-surface-low px-3.5 py-3 text-xs leading-5 text-muted">
                    <span class="font-semibold text-ink">Demo:</span> admin@libratech.test / password &middot; member: andi@libratech.test / password
                </div>
            </form>

            <p class="auth-animate auth-d3 mt-8 text-center text-sm text-muted">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-primary-deep">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>
{{-- ponytail: Alpine toggle is minimal; Flux viewable prop (flux:input viewable) is lazier if Flux JS present—swap x-data toggle for viewable to remove Alpine. --}}
</body>
</html>
