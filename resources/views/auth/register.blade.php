<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - LibraTech</title>
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
    {{-- Left: editorial panel - Stitch 082 mirrored --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-[#e4e1ee] border-r border-line/60">
        <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=1200&q=80" alt="Perpustakaan modern" class="absolute inset-0 w-full h-full object-cover" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-t from-[#3525cd]/85 via-[#3525cd]/30 to-transparent"></div>
        <div class="relative flex flex-col justify-end p-10 xl:p-12 w-full">
            <h2 class="auth-animate font-display text-[28px] xl:text-[30px] leading-[1.25] text-white font-semibold tracking-[-0.02em]">Temukan Duniamu.</h2>
            <p class="auth-animate auth-d1 mt-2 text-white/85 text-[15px] leading-6 max-w-[32ch]">Gerbang menuju ribuan literatur dan pengetahuan tak terbatas, semuanya dalam genggaman yang tenang.</p>
            <p class="auth-animate auth-d2 mt-8 text-white/70 text-xs tracking-[0.12em] uppercase">Literary Modernist - Warm Editorial</p>
        </div>
    </div>

    {{-- Right: form --}}
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
                <h1 class="font-display text-[28px] leading-[1.15] font-semibold tracking-[-0.02em] text-ink">Bergabung dengan LibraTech</h1>
                <p class="mt-2 text-[14.5px] leading-6 text-muted">Buat akun untuk memulai perjalanan membaca Anda.</p>
            </div>

            @if($errors->any())
                <div class="auth-animate auth-d1 mt-5 rounded-[10px] border border-red-200 bg-[#ffdad6]/60 px-3.5 py-3 text-sm text-error">
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="auth-animate auth-d2 mt-7 space-y-4" novalidate>
                @csrf
                <flux:field>
                    <flux:label class="!text-ink">Nama Lengkap</flux:label>
                    <flux:input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap Anda" autocomplete="name" required :invalid="$errors->has('name')" class="!rounded-[10px]" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label class="!text-ink">Email</flux:label>
                    <flux:input type="email" name="email" value="{{ old('email') }}" placeholder="anda@contoh.com" autocomplete="email" required :invalid="$errors->has('email')" class="!rounded-[10px]" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <div class="flex items-center justify-between">
                        <flux:label class="!text-ink">Kata Sandi</flux:label>
                        <button type="button" class="text-[11px] tracking-[0.06em] uppercase font-semibold text-primary hover:text-primary/80" x-data="{ show:false }" @click="show=!show; $refs.pwd1.type = show ? 'text' : 'password'; $el.textContent = show ? 'Sembunyikan' : 'Lihat'">Lihat</button>
                    </div>
                    <flux:input x-ref="pwd1" type="password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required :invalid="$errors->has('password')" class="!rounded-[10px]" />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <div class="flex items-center justify-between">
                        <flux:label class="!text-ink">Konfirmasi Kata Sandi</flux:label>
                        <button type="button" class="text-[11px] tracking-[0.06em] uppercase font-semibold text-primary hover:text-primary/80" x-data="{ show:false }" @click="show=!show; $refs.pwd2.type = show ? 'text' : 'password'; $el.textContent = show ? 'Sembunyikan' : 'Lihat'">Lihat</button>
                    </div>
                    <flux:input x-ref="pwd2" type="password" name="password_confirmation" placeholder="Ulangi kata sandi" autocomplete="new-password" required class="!rounded-[10px]" />
                </flux:field>

                <flux:button variant="primary" type="submit" class="w-full mt-2 justify-center gap-2 !rounded-[10px] !bg-primary hover:!bg-primary/90">
                    Daftar
                    <flux:icon.arrow-right class="w-4 h-4" />
                </flux:button>
            </form>

            <p class="auth-animate auth-d3 mt-8 text-center text-sm text-muted">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-primary hover:text-primary/80 focus-visible:ring-2 focus-visible:ring-primary rounded">Masuk</a>
            </p>
        </div>
    </div>
</div>
@fluxScripts
@livewireScripts
</body>
</html>
{{-- ponytail: lazier swap is flux:input viewable prop - removes Alpine toggle entirely when Flux JS bundles it. --}}
