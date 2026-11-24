@extends('layouts.app', ['activeNav' => 'profile'])

@section('title', 'Profile Details – SecureThreatShield')
@section('description', 'Manage your personal details, credentials, and account status.')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-5 border-b border-gray-700/50">
        <div>
            <span class="text-[11px] font-mono uppercase tracking-wider text-[#00BBFF]">Account Profile</span>
            <h1 class="text-xl font-semibold text-white tracking-tight mt-1">Profile details</h1>
            <p class="text-xs text-slate-400 mt-1">Manage your personal details, credentials, and account status.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 rounded-md border text-xs font-medium bg-[#1C2236] border-[#00BBFF]/40 text-[#00BBFF]" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if (session('password_status'))
        <div class="mb-6 px-4 py-3 rounded-md border text-xs font-medium bg-[#1C2236] border-[#00BBFF]/40 text-[#00BBFF]" role="alert">
            {{ session('password_status') }}
        </div>
    @endif

    <div class="space-y-6">
        {{-- User Summary Badge --}}
        <section class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-lg bg-[#4063D7] border border-gray-700/50 flex items-center justify-center text-white text-lg font-semibold shrink-0">
                    {{ $user->initials() }}
                </div>
                <div>
                    <h2 class="text-base font-semibold text-white">{{ $user->name }}</h2>
                    <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $user->email }} &bull; SecureThreatShield account</p>
                </div>
            </div>
        </section>

        {{-- Profile Info Form --}}
        <section class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">
            <div class="mb-5">
                <h2 class="text-sm font-semibold text-white">Account Information</h2>
                <p class="text-xs text-slate-400 mt-0.5">Update your display name and email address connected to this account.</p>
            </div>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="block text-xs font-medium text-slate-300 mb-1.5">Display name</label>
                    <input type="text" id="name" name="name"
                        value="{{ old('name', $user->name) }}" required autocomplete="name"
                        class="w-full bg-[#0E1326] border border-gray-700/50 rounded-md px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#4063D7] focus:ring-1 focus:ring-[#4063D7] transition">
                    @error('name')
                        <span class="block text-xs text-slate-300 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-medium text-slate-300 mb-1.5">Email address</label>
                    <input type="email" id="email" name="email"
                        value="{{ old('email', $user->email) }}" required autocomplete="email"
                        class="w-full bg-[#0E1326] border border-gray-700/50 rounded-md px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#4063D7] focus:ring-1 focus:ring-[#4063D7] transition">
                    @error('email')
                        <span class="block text-xs text-slate-300 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium px-4 py-2.5 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                        Save Changes
                    </button>
                </div>
            </form>
        </section>

        {{-- Change Password Form --}}
        <section class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">
            <div class="mb-5">
                <h2 class="text-sm font-semibold text-white">Update Password</h2>
                <p class="text-xs text-slate-400 mt-0.5">Ensure your account is using a long, secure password to stay protected.</p>
            </div>
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-medium text-slate-300 mb-1.5">Current password</label>
                    <input type="password" id="current_password" name="current_password"
                        autocomplete="current-password" required
                        class="w-full bg-[#0E1326] border border-gray-700/50 rounded-md px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#4063D7] focus:ring-1 focus:ring-[#4063D7] transition">
                    @error('current_password')
                        <span class="block text-xs text-slate-300 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-slate-300 mb-1.5">New password</label>
                    <input type="password" id="password" name="password"
                        autocomplete="new-password" required
                        class="w-full bg-[#0E1326] border border-gray-700/50 rounded-md px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#4063D7] focus:ring-1 focus:ring-[#4063D7] transition">
                    @error('password')
                        <span class="block text-xs text-slate-300 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-slate-300 mb-1.5">Confirm new password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        autocomplete="new-password" required
                        class="w-full bg-[#0E1326] border border-gray-700/50 rounded-md px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#4063D7] focus:ring-1 focus:ring-[#4063D7] transition">
                    @error('password_confirmation')
                        <span class="block text-xs text-slate-300 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="bg-[#4063D7] hover:bg-[#4063D7]/90 text-white text-xs font-medium px-4 py-2.5 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                        Update Password
                    </button>
                </div>
            </form>
        </section>

        {{-- Archive Account --}}
        <section class="bg-[#1C2236] border border-gray-700/50 rounded-lg p-6">
            <div class="mb-4">
                <h2 class="text-sm font-semibold text-white">Archive Account</h2>
                <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">
                    Temporarily deactivate your account. Protection and authentication integrations will be paused. You can reactivate your account at any time simply by logging back in.
                </p>
            </div>
            <div>
                <button type="button" id="openArchiveModalBtn"
                    class="bg-[#0E1326] hover:border-[#00BBFF]/60 text-slate-200 text-xs font-medium px-4 py-2.5 rounded-md border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                    Archive Account
                </button>
            </div>
        </section>
    </div>

    {{-- Archive Confirmation Modal --}}
    <div id="archiveModal" aria-hidden="true"
        class="{{ $errors->archive->any() ? '' : 'hidden' }} fixed inset-0 z-50 bg-[#0E1326]/80 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-[#1C2236] border border-gray-700/50 rounded-lg p-6" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <h3 id="modalTitle" class="text-base font-semibold text-white mb-2">Archive your account?</h3>
            <p class="text-xs text-slate-400 leading-relaxed mb-4">
                Please enter your current password to confirm you want to archive your account. You can reactivate it anytime by logging back in.
            </p>
            <form method="POST" action="{{ route('profile.archive') }}" id="archiveForm">
                @csrf
                <div class="mb-4">
                    <label for="archive_password" class="block text-xs font-medium text-slate-300 mb-1.5">Current password</label>
                    <input type="password" id="archive_password" name="password" required placeholder="Enter password to confirm"
                        class="w-full bg-[#0E1326] border border-gray-700/50 rounded-md px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#4063D7] focus:ring-1 focus:ring-[#4063D7] transition">
                    @if ($errors->archive->has('password'))
                        <span class="block text-xs text-slate-300 mt-1">{{ $errors->archive->first('password') }}</span>
                    @endif
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-700/50">
                    <button type="button" id="closeArchiveModalBtn"
                        class="px-4 py-2 rounded-md text-xs font-medium text-slate-300 bg-[#0E1326] hover:text-white border border-gray-700/50 transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-md text-xs font-medium text-slate-200 bg-[#0E1326] hover:border-[#00BBFF]/60 border border-gray-700/50 transition-colors cursor-pointer">
                        Confirm Archive
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (() => {
        const modal = document.getElementById('archiveModal');
        const openBtn = document.getElementById('openArchiveModalBtn');
        const closeBtn = document.getElementById('closeArchiveModalBtn');
        const passwordInput = document.getElementById('archive_password');

        if (!modal || !openBtn || !closeBtn) return;

        const openModal = () => {
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            passwordInput.value = '';
            passwordInput.focus();
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
        };

        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });
    })();
</script>
@endpush
