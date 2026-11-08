    .app-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 32px;
        height: 64px;
        background: #0F172A;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .nav-logo {
        display: flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 9px;
        background: #3866E6;
    }
    .nav-logo img { display: block; height: 38px; width: auto; }
    .nav-links { display: flex; align-items: center; gap: 8px; }
    .nav-links a {
        color: #94A3B8;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        padding: 7px 14px;
        border-radius: 8px;
        transition: background 0.2s, color 0.2s;
    }
    .nav-links a:hover { background: #1E293B; color: #F8FAFC; }
    .nav-links a.active { background: rgba(61,90,241,0.2); color: #A5B4FC; }
    .nav-right { display: flex; align-items: center; gap: 14px; }
    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #3D5AF1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 800;
        color: #fff;
        border: 0;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        text-decoration: none;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .user-avatar:hover, .user-avatar:focus-visible {
        box-shadow: 0 0 0 4px rgba(61,90,241,0.3);
        transform: translateY(-1px);
    }
    .account-menu {
        position: fixed;
        top: 76px;
        right: 28px;
        z-index: 300;
        width: min(320px, calc(100vw - 32px));
        padding: 18px;
        background: #111A2D;
        border: 1px solid rgba(129,140,248,0.24);
        border-radius: 16px;
        box-shadow: 0 24px 70px rgba(0,0,0,0.45);
        opacity: 0;
        pointer-events: none;
        transform: translateY(-8px);
        transition: opacity 0.2s, transform 0.2s;
    }
    .account-menu.is-open { opacity: 1; pointer-events: auto; transform: translateY(0); }
    .account-menu-summary { padding: 4px 2px 16px; border-bottom: 1px solid rgba(255,255,255,0.08); }
    .account-menu-summary strong { display: block; margin-bottom: 4px; color: #F8FAFC; font-size: 15px; }
    .account-menu-summary span { color: #94A3B8; font-size: 12px; overflow-wrap: anywhere; }
    .account-menu-links { display: grid; gap: 6px; padding-top: 14px; }
    .account-menu-links a, .account-menu-links button {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 11px 12px;
        color: #F8FAFC;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 9px;
        font: inherit;
        font-size: 13px;
        text-align: left;
        text-decoration: none;
        cursor: pointer;
    }
    .account-menu-links a:hover, .account-menu-links button:hover { background: #1E293B; }
    .account-menu-links .account-sign-out {
        justify-content: center;
        margin-top: 6px;
        color: #FCA5A5;
        background: rgba(239,68,68,0.14);
        border-color: rgba(248,113,113,0.35);
        font-weight: 700;
    }
    .account-menu-links .account-sign-out:hover { background: rgba(239,68,68,0.24); }
    @media (max-width: 640px) {
        .app-nav { padding: 0 18px; }
        .nav-logo { padding: 3px 6px; }
        .nav-logo img { height: 32px; }
        .nav-links { gap: 0; }
        .nav-links a { padding: 7px 9px; font-size: 12px; }
        .user-avatar { width: 42px; height: 42px; }
        .account-menu { top: 70px; right: 16px; }
    }
