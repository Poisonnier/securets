<aside class="account-menu" id="account-menu" aria-label="Account menu" aria-hidden="true">
    <div class="account-menu-summary">
        <strong>{{ Auth::user()->name }}</strong>
        <span>{{ Auth::user()->email }}</span>
    </div>
    <div class="account-menu-links">
        <a href="{{ route('profile') }}">Profile details <span aria-hidden="true">›</span></a>
        <a href="{{ route('settings') }}">Settings <span aria-hidden="true">›</span></a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="account-sign-out">Sign out</button>
        </form>
    </div>
</aside>
<script>
    (() => {
        const trigger = document.getElementById('profile-trigger');
        const menu = document.getElementById('account-menu');

        const closeMenu = () => {
            menu.classList.remove('is-open');
            menu.setAttribute('aria-hidden', 'true');
            trigger.setAttribute('aria-expanded', 'false');
        };

        trigger.addEventListener('click', () => {
            const isOpen = menu.classList.toggle('is-open');
            menu.setAttribute('aria-hidden', String(!isOpen));
            trigger.setAttribute('aria-expanded', String(isOpen));
        });

        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target) && !trigger.contains(event.target)) closeMenu();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeMenu();
        });
    })();
</script>
