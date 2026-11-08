@extends('layouts.app', ['activeNav' => 'subscription'])

@section('title', 'Subscription – SecureThreatShield')
@section('description', 'Manage your SecureThreatShield subscription plan and billing.')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-5 border-b border-gray-700/50">
        <div>
            <span class="text-[11px] font-mono uppercase tracking-wider text-[#00BBFF]">Workspace Billing</span>
            <h1 class="text-xl font-semibold text-white tracking-tight mt-1">Subscription Plans</h1>
            <p class="text-xs text-slate-400 mt-1">Manage your application gate capacity and authentication infrastructure tier.</p>
        </div>

        @if (($subscription ?? null)?->stripe_customer_id)
            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" id="btnManageBilling" onclick="manageBilling(this)"
                    class="px-3.5 py-2 rounded-md text-xs font-medium text-slate-200 bg-[#1C2236] hover:text-white hover:border-[#00BBFF]/60 border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                    Manage Billing
                </button>
                @if (($currentPlan ?? 'free') !== 'free')
                    <button type="button" id="btnCancelSubscription" onclick="manageBilling(this)"
                        class="px-3.5 py-2 rounded-md text-xs font-medium text-red-300 bg-[#1C2236] hover:bg-red-500/10 border border-red-500/30 transition-colors focus:outline-none focus:ring-2 focus:ring-red-400 cursor-pointer">
                        Cancel Subscription
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- Flash Notification Banner --}}
    @if (session('flash_success'))
        <div id="flash" class="mb-6 px-4 py-3 rounded-md border text-xs font-medium bg-emerald-500/10 border-emerald-500/30 text-emerald-300">
            {{ session('flash_success') }}
        </div>
    @elseif (session('flash_error'))
        <div id="flash" class="mb-6 px-4 py-3 rounded-md border text-xs font-medium bg-red-500/10 border-red-500/30 text-red-300">
            {{ session('flash_error') }}
        </div>
    @else
        <div id="flash" hidden class="mb-6 px-4 py-3 rounded-md border text-xs font-medium"></div>
    @endif

    {{-- Pricing / Plan Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($plans as $key => $plan)
            @php
                $isCurrent  = $currentPlan === $key;
                $isFree     = $key === 'free';
                $limitLabel = $plan['limit'] ? $plan['limit'] . ' connected applications' : 'Unlimited connected applications';
                $priceValue = $isFree ? '$0' : explode(' ', $plan['price_label'])[0];
            @endphp

            <div class="bg-[#1C2236] border {{ $isCurrent ? 'border-[#00BBFF]/60' : 'border-gray-700/50' }} rounded-lg p-6 flex flex-col justify-between">
                <div>
                    {{-- Plan Header & Current Plan Badge --}}
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <h2 class="text-sm font-semibold text-white">{{ $plan['label'] }}</h2>
                        @if ($isCurrent)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono text-[#00BBFF] bg-[#00BBFF]/10 border border-[#00BBFF]/30">
                                Current Plan
                            </span>
                        @endif
                    </div>

                    {{-- Price Display --}}
                    <div class="flex items-baseline gap-1 mb-1">
                        <span class="text-2xl font-mono font-semibold text-white">{{ $priceValue }}</span>
                        <span class="text-xs text-slate-400">/ month</span>
                    </div>
                    <p class="text-xs font-mono text-slate-400 pb-5 mb-5 border-b border-gray-700/50">
                        {{ $limitLabel }}
                    </p>

                    {{-- Feature List --}}
                    <ul class="space-y-2.5 mb-6 text-xs text-slate-400">
                        <li class="flex items-start gap-2">
                            <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                            <span>{{ $limitLabel }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                            <span>RFC-6238 TOTP Authenticator</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                            <span>Out-of-band Push Approvals</span>
                        </li>
                        @if (!$isFree)
                            <li class="flex items-start gap-2">
                                <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                                <span>Priority developer support</span>
                            </li>
                        @endif
                        @if (in_array($key, ['ultra', 'ultra_plus']))
                            <li class="flex items-start gap-2">
                                <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                                <span>Advanced login telemetry</span>
                            </li>
                        @endif
                        @if ($key === 'ultra_plus')
                            <li class="flex items-start gap-2">
                                <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                                <span>API access &amp; event webhooks</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="font-mono text-[#00BBFF] shrink-0">✓</span>
                                <span>Dedicated account engineer</span>
                            </li>
                        @endif
                    </ul>
                </div>

                {{-- Card Action Footer --}}
                <div class="pt-4 border-t border-gray-700/50 space-y-2">
                    @if ($isCurrent)
                        <button type="button" disabled
                            class="w-full py-2 px-3 rounded-md text-xs font-mono font-medium text-[#00BBFF] bg-[#0E1326] border border-[#00BBFF]/30 cursor-default">
                            Current Plan
                        </button>
                        @if (!$isFree && ($subscription ?? null)?->stripe_customer_id)
                            <button type="button" onclick="manageBilling(this)"
                                class="w-full py-2 px-3 rounded-md text-xs font-medium text-red-300 bg-[#0E1326] hover:bg-red-500/10 border border-red-500/30 transition-colors focus:outline-none focus:ring-2 focus:ring-red-400 cursor-pointer">
                                Cancel Subscription
                            </button>
                        @endif
                    @elseif ($isFree)
                        <button type="button" disabled
                            class="w-full py-2 px-3 rounded-md text-xs font-medium text-slate-500 bg-[#0E1326]/60 border border-gray-700/40 cursor-default">
                            Included Tier
                        </button>
                    @else
                        <button type="button"
                                id="btn-{{ $key }}"
                                onclick="subscribe('{{ $key }}', this)"
                                class="w-full py-2 px-3 rounded-md text-xs font-medium text-white bg-[#4063D7] hover:bg-[#4063D7]/90 border border-gray-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#00BBFF] cursor-pointer">
                            Upgrade to {{ $plan['label'] }}
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const flashEl = document.getElementById('flash');

function setFlash(type, msg) {
    flashEl.hidden = false;
    const styles = {
        success: 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300',
        error:   'bg-red-500/10 border-red-500/30 text-red-300',
        loading: 'bg-[#4063D7]/15 border-[#4063D7]/40 text-[#00BBFF]',
    };
    flashEl.className = `mb-6 px-4 py-3 rounded-md border text-xs font-medium ${styles[type] || styles.loading}`;
    flashEl.textContent = msg;
}

async function subscribe(plan, btn) {
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Redirecting to Stripe…';
    setFlash('loading', 'Opening Stripe Checkout…');

    try {
        const res = await fetch('/web/subscription/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ plan }),
        });
        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.error || 'Could not start checkout.');
        }

        if (data.checkout_url) {
            window.location.href = data.checkout_url;
        }
    } catch (e) {
        setFlash('error', e.message || 'Something went wrong. Please try again.');
        btn.disabled = false;
        btn.textContent = originalText;
    }
}

async function manageBilling(btn) {
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Opening portal…';
    setFlash('loading', 'Opening the Stripe billing portal…');

    try {
        const res = await fetch('/web/subscription/portal', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
        });
        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.error || 'Could not open the billing portal.');
        }

        if (data.portal_url) {
            window.location.href = data.portal_url;
        }
    } catch (e) {
        setFlash('error', e.message || 'Something went wrong. Please try again.');
        btn.disabled = false;
        btn.textContent = originalText;
    }
}
</script>
@endpush
