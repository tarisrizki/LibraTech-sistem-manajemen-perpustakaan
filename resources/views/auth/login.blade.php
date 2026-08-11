<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - LibraTech</title>
    @fluxAppearance
    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
    <style>
        :root{ --radius-card:16px; --radius-input:10px; }
        @keyframes authFadeUp{ from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .auth-animate{ animation:authFadeUp 520ms ease-out both }
        .auth-d1{ animation-delay:80ms } .auth-d2{ animation-delay:160ms } .auth-d3{ animation-delay:240ms }
        @media(prefers-reduced-motion:reduce){ .auth-animate{animation:none;opacity:1;transform:none} }
    </style>
</head>
<body class="min-h-screen bg-surface text-ink font-sans antialiased">
<div class="flex min-h-screen">
    {{-- Left: editorial quote panel - Stitch 56d hidden on mobile --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-[#e4e1ee] border-r border-line/60">
        <img src="https://images.unsplash.com/photo-1519682337058-a94d519337bc?auto=format&fit=crop&w=1200&q=80" alt="Rak buku perpustakaan" class="absolute inset-0 w-full h-full object-cover" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-t from-[#3525cd]/85 via-[#3525cd]/30 to-transparent"></div>
        <div class="relative flex flex-col justify-end p-10 xl:p-12 w-full">
            <blockquote class="auth-animate font-display text-[28px] xl:text-[30px] leading-[1.25] text-white max-w-[22ch] font-semibold tracking-[-0.02em]">
                "A room without books is like a body without a soul."
            </blockquote>
            <p class="auth-animate auth-d1 mt-3 text-white/85 text-[15px] font-sans">Marcus Tullius Cicero</p>
            <p class="auth-animate auth-d2 mt-8 text-white/70 text-xs tracking-[0.12em] uppercase">Literary Modernist - Warm Editorial</p>
        </div>
    </div>

    {{-- Right: form - Flux editorial --}}
    <div class="flex flex-1 flex-col justify-center px-5 sm:px-8 lg:px-12 xl:px-16 py-10 bg-white lg:bg-surface">
        <div class="mx-auto w-full max-w-[400px]">
            <div class="auth-animate flex items-center gap-2.5 mb-8">
                <span class="w-9 h-9 rounded-[10px] bg-primary text-white grid place-items-center shadow-sm">
                    <flux:icon.book-open class="w-5 h-5 text-white" />
                </span>
                <span class="font-display font-bold text-[22px] tracking-tight text-ink">LibraTech</span>
                <span class="hidden sm:inline text-[11px] tracking-[0.14em] uppercase text-muted border-l border-line pl-2.5 ml-1">Perpustakaan</span>
            </div>

            <div class="auth-animate auth-d1">
                <h1 class="font-display text-[28px] leading-[1.15] font-semibold tracking-[-0.02em] text-ink">Selamat Datang Kembali</h1>
                <p class="mt-2 text-[14.5px] leading-6 text-muted">Silakan masuk ke akun Anda.</p>
            </div>

            @if($errors->any())
                <div class="auth-animate auth-d1 mt-5 rounded-[10px] border border-red-200 bg-[#ffdad6]/60 px-3.5 py-3 text-sm text-error">
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="auth-animate auth-d2 mt-7 space-y-4" novalidate>
                @csrf
                <flux:field>
                    <flux:label class="!text-ink">Alamat Email</flux:label>
                    <flux:input type="email" name="email" value="{{ old('email') }}" placeholder="anda@contoh.com" autocomplete="email" required :invalid="$errors->has('email')" class="!rounded-[10px]" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <div class="flex items-center justify-between">
                        <flux:label class="!text-ink">Kata Sandi</flux:label>
                        <button type="button" class="text-[11px] tracking-[0.06em] uppercase font-semibold text-primary hover:text-primary/80" x-data="{ show:false }" @click="show=!show; $refs.pwd.type = show ? 'text' : 'password'; $el.textContent = show ? 'Sembunyikan' : 'Lihat'">Lihat</button>
                    </div>
                    <flux:input x-ref="pwd" type="password" name="password" placeholder="••••••••" autocomplete="current-password" required :invalid="$errors->has('password')" class="!rounded-[10px]" />
                    <flux:error name="password" />
                </flux:field>

                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-2 text-sm text-ink cursor-pointer select-none">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 rounded border-line text-primary focus:ring-primary">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm font-semibold text-primary hover:text-primary/80 focus-visible:ring-2 focus-visible:ring-primary rounded">Lupa kata sandi?</a>
                </div>

                <flux:button variant="primary" type="submit" class="w-full mt-2 justify-center !rounded-[10px] !bg-primary hover:!bg-primary/90">
                    Masuk
                </flux:button>

                <div class="flex items-center gap-3 my-1">
                    <span class="h-px flex-1 bg-line"></span>
                    <span class="text-xs tracking-wide text-muted">atau</span>
                    <span class="h-px flex-1 bg-line"></span>
                </div>

                <a href="{{ route('auth.google') }}" class="flex w-full items-center justify-center gap-2.5 rounded-[10px] border border-line bg-white px-4 py-2.5 text-sm font-semibold text-ink hover:bg-surface transition-colors focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-1">
                    <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09A6.97 6.97 0 015.46 12c0-.72.13-1.42.36-2.09V7.07H2.18A11 11 0 001 12c0 1.78.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Masuk dengan Google
                </a>

                <div class="rounded-[10px] border border-line-soft bg-surface px-3.5 py-3 text-xs leading-5 text-muted">
                    <span class="font-semibold text-ink">Demo:</span> admin@libratech.test / password - member: andi@libratech.test / password
                </div>
            </form>

            <p class="auth-animate auth-d3 mt-8 text-center text-sm text-muted">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-primary/80 focus-visible:ring-2 focus-visible:ring-primary rounded">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>
@fluxScripts
@livewireScripts
</body>
</html>
{{-- ponytail: lazier is @extends('layouts.app') with split slot - kept standalone vite shell to allow full-bleed image without header chrome while still using vite tokens. --}}
