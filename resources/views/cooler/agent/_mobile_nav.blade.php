{{--
    Agent Portal Mobile Navigation
    Include at the end of @section('content') in every agent portal view.
    • Hides the sidebar hamburger button on mobile (no dark overlay)
    • Shows a fixed bottom tab bar for navigation
--}}
<style>
/* ── Agent portal: hide sidebar toggle on mobile ─────────────── */
@media (max-width: 1023px) {
    .small-view-button { display: none !important; }
    /* Space so content isn't hidden under the bottom nav */
    #scrollable-container { padding-bottom: 72px !important; }
}

/* ── Bottom navigation bar ────────────────────────────────────── */
#agent-mobile-nav {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1049;
    background: linear-gradient(to right, var(--theme-dark), var(--theme-main));
    box-shadow: 0 -2px 16px rgba(0,0,0,0.18);
    padding: 6px 0 max(6px, env(safe-area-inset-bottom, 6px));
}
@media (max-width: 1023px) {
    #agent-mobile-nav { display: flex; }
}
#agent-mobile-nav a {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    color: rgba(255,255,255,0.65);
    text-decoration: none !important;
    font-size: 10px;
    font-weight: 600;
    padding: 6px 4px 4px;
    letter-spacing: 0.02em;
    transition: color 0.12s;
    -webkit-tap-highlight-color: transparent;
}
#agent-mobile-nav a.nav-active,
#agent-mobile-nav a:hover {
    color: #ffffff;
}
#agent-mobile-nav a.nav-active i {
    transform: scale(1.12);
}
#agent-mobile-nav a i {
    font-size: 20px;
    display: block;
    transition: transform 0.12s;
}
</style>

<nav id="agent-mobile-nav" aria-label="Agent Portal Navigation">
    <a href="{{ route('cooler.agent.dashboard') }}"
       class="{{ request()->routeIs('cooler.agent.dashboard') ? 'nav-active' : '' }}">
        <i class="fa fa-home"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('cooler.agent.customers') }}"
       class="{{ request()->routeIs('cooler.agent.customers*') ? 'nav-active' : '' }}">
        <i class="fa fa-users"></i>
        <span>Customers</span>
    </a>
    <a href="{{ route('cooler.agent.retrievals') }}"
       class="{{ request()->routeIs('cooler.agent.retrievals*') ? 'nav-active' : '' }}">
        <i class="fa fa-truck"></i>
        <span>Retrievals</span>
    </a>
    <a href="{{ route('cooler.agent.orders.create') }}"
       class="{{ request()->routeIs('cooler.agent.orders*') ? 'nav-active' : '' }}">
        <i class="fa fa-shopping-cart"></i>
        <span>New Order</span>
    </a>
</nav>
