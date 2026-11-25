{{-- Charte visuelle commune au module Restaurant (alignée sur le hub Check-in) --}}
<style>
:root {
    --rs-brown-900: #3d241a;
    --rs-brown-800: #4e3024;
    --rs-brown-600: #704838;
    --rs-brown-500: #8b4513;
    --rs-brown-400: #a0522d;
    --rs-brown-100: #f9f0e6;
    --rs-brown-50:  #fcf8f3;
    --rs-line:      #f0e3d4;

    --rs-amber:  #b45309;
    --rs-green:  #15803d;
    --rs-blue:   #1d4ed8;
    --rs-red:    #b91c1c;
    --rs-grey:   #704838;

    --rs-shadow-sm: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.05);
    --rs-shadow-md: 0 4px 12px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.05);
    --rs-radius: 14px;
}

.rs-page {
    padding: 26px 26px 48px;
    background: var(--rs-brown-50);
    min-height: 100vh;
    font-family: 'Segoe UI', system-ui, sans-serif;
    color: var(--rs-brown-900);
}

@keyframes rsFadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rs-animate { animation: rsFadeUp .35s cubic-bezier(.4,0,.2,1) both; }

/* ── En-tête ───────────────────────────────── */
.rs-header {
    display: flex; flex-wrap: wrap; gap: 16px;
    justify-content: space-between; align-items: flex-start;
    margin-bottom: 24px;
}
.rs-header-title {
    font-size: 1.7rem; font-weight: 700; margin: 0;
    color: var(--rs-brown-900); display: flex; align-items: center; gap: 12px;
}
.rs-header-title i { color: var(--rs-brown-500); }
.rs-header-sub { margin: 6px 0 0; color: var(--rs-brown-600); font-size: .92rem; }
.rs-header-actions { display: flex; flex-wrap: wrap; gap: 10px; }

.rs-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px; border-radius: 10px; border: 1px solid transparent;
    font-size: .88rem; font-weight: 600; text-decoration: none; cursor: pointer;
    transition: all .2s cubic-bezier(.4,0,.2,1);
}
.rs-btn-primary { background: var(--rs-brown-500); color: #fff; }
.rs-btn-primary:hover { background: var(--rs-brown-600); color: #fff; transform: translateY(-1px); }
.rs-btn-ghost { background: #fff; color: var(--rs-brown-600); border-color: var(--rs-line); }
.rs-btn-ghost:hover { background: var(--rs-brown-100); color: var(--rs-brown-900); }
.rs-btn-sm { padding: 6px 12px; font-size: .78rem; border-radius: 8px; }

/* ── Cartes de statistiques ────────────────── */
.rs-stats {
    display: grid; gap: 16px; margin-bottom: 26px;
    grid-template-columns: repeat(auto-fit, minmax(215px, 1fr));
}
.rs-stat {
    background: #fff; border-radius: var(--rs-radius); padding: 20px;
    box-shadow: var(--rs-shadow-sm); position: relative; overflow: hidden;
    border: 1px solid var(--rs-line); transition: all .2s cubic-bezier(.4,0,.2,1);
}
.rs-stat::before {
    content: ''; position: absolute; inset: 0 auto 0 0; width: 4px;
    background: var(--rs-accent, var(--rs-brown-500));
}
.rs-stat:hover { transform: translateY(-2px); box-shadow: var(--rs-shadow-md); }
.rs-stat--orders  { --rs-accent: var(--rs-blue); }
.rs-stat--open    { --rs-accent: var(--rs-amber); }
.rs-stat--revenue { --rs-accent: var(--rs-green); }
.rs-stat--tables  { --rs-accent: var(--rs-brown-500); }
.rs-stat-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
.rs-stat-label { font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; color: var(--rs-brown-600); font-weight: 600; }
.rs-stat-value { font-size: 1.85rem; font-weight: 700; line-height: 1.15; margin-top: 4px; color: var(--rs-brown-900); }
.rs-stat-value.is-money { font-size: 1.45rem; }
.rs-stat-icon {
    width: 44px; height: 44px; flex: 0 0 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    background: var(--rs-brown-100); color: var(--rs-accent, var(--rs-brown-500)); font-size: 1.05rem;
}
.rs-stat-meta { margin-top: 12px; font-size: .78rem; color: var(--rs-brown-600); }

/* ── Panneaux ──────────────────────────────── */
.rs-grid { display: grid; gap: 20px; }
.rs-grid-2 { grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr); }
@media (max-width: 1100px) { .rs-grid-2 { grid-template-columns: 1fr; } }

.rs-panel {
    background: #fff; border: 1px solid var(--rs-line); border-radius: var(--rs-radius);
    box-shadow: var(--rs-shadow-sm); overflow: hidden; margin-bottom: 20px;
}
.rs-panel-head {
    display: flex; justify-content: space-between; align-items: center; gap: 12px;
    padding: 16px 20px; border-bottom: 1px solid var(--rs-line); background: var(--rs-brown-50);
}
.rs-panel-title { margin: 0; font-size: 1rem; font-weight: 700; display: flex; align-items: center; gap: 9px; color: var(--rs-brown-900); }
.rs-panel-title i { color: var(--rs-brown-500); }
.rs-panel-body { padding: 18px 20px; }
.rs-panel-body.is-flush { padding: 0; }

/* ── Tableaux ──────────────────────────────── */
.rs-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
.rs-table th {
    text-align: left; padding: 12px 20px; font-size: .74rem; text-transform: uppercase;
    letter-spacing: .04em; color: var(--rs-brown-600); background: var(--rs-brown-50);
    border-bottom: 1px solid var(--rs-line); font-weight: 700; white-space: nowrap;
}
.rs-table td { padding: 14px 20px; border-bottom: 1px solid var(--rs-line); vertical-align: middle; }
.rs-table tbody tr:last-child td { border-bottom: none; }
.rs-table tbody tr:hover { background: var(--rs-brown-50); }
.rs-table .rs-num { font-weight: 700; color: var(--rs-brown-800); }
.rs-table .rs-money { font-weight: 700; color: var(--rs-brown-500); white-space: nowrap; }

/* ── Badges de statut ──────────────────────── */
.rs-badge {
    display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px;
    border-radius: 999px; font-size: .74rem; font-weight: 700; white-space: nowrap;
}
.rs-badge--warning   { background: #fef3c7; color: var(--rs-amber); }
.rs-badge--info      { background: #dbeafe; color: var(--rs-blue); }
.rs-badge--primary   { background: var(--rs-brown-100); color: var(--rs-brown-500); }
.rs-badge--success   { background: #dcfce7; color: var(--rs-green); }
.rs-badge--danger    { background: #fee2e2; color: var(--rs-red); }
.rs-badge--secondary { background: #f1ece6; color: var(--rs-grey); }

/* ── Filtres ───────────────────────────────── */
.rs-filters { display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; }
.rs-field { display: flex; flex-direction: column; gap: 5px; min-width: 165px; flex: 1 1 165px; }
.rs-field label { font-size: .76rem; font-weight: 700; color: var(--rs-brown-600); text-transform: uppercase; letter-spacing: .03em; }
.rs-field .form-control, .rs-field .form-select {
    border: 1px solid var(--rs-line); border-radius: 9px; padding: 9px 12px; font-size: .88rem;
}
.rs-field .form-control:focus, .rs-field .form-select:focus {
    border-color: var(--rs-brown-400); box-shadow: 0 0 0 3px rgba(160,82,45,.12);
}

/* ── État vide ─────────────────────────────── */
.rs-empty { text-align: center; padding: 46px 20px; color: var(--rs-brown-600); }
.rs-empty i { font-size: 2.4rem; color: var(--rs-brown-400); opacity: .55; margin-bottom: 14px; display: block; }
.rs-empty h4 { font-size: 1.02rem; font-weight: 700; color: var(--rs-brown-800); margin-bottom: 6px; }
.rs-empty p { font-size: .87rem; margin-bottom: 16px; }

/* ── Ticket de commande (hub) ──────────────── */
.rs-ticket {
    display: flex; gap: 14px; align-items: flex-start; padding: 15px 20px;
    border-bottom: 1px solid var(--rs-line); transition: background .18s;
}
.rs-ticket:last-child { border-bottom: none; }
.rs-ticket:hover { background: var(--rs-brown-50); }
.rs-ticket-time {
    flex: 0 0 62px; text-align: center; padding: 7px 4px; border-radius: 10px;
    background: var(--rs-brown-100); color: var(--rs-brown-600);
}
.rs-ticket-time strong { display: block; font-size: .95rem; color: var(--rs-brown-900); }
.rs-ticket-time span { font-size: .68rem; text-transform: uppercase; letter-spacing: .04em; }
.rs-ticket-body { flex: 1; min-width: 0; }
.rs-ticket-title { display: flex; flex-wrap: wrap; align-items: center; gap: 9px; font-weight: 700; font-size: .93rem; }
.rs-ticket-meta { margin-top: 4px; font-size: .81rem; color: var(--rs-brown-600); }
.rs-ticket-items { margin-top: 7px; font-size: .81rem; color: var(--rs-brown-800); }
.rs-ticket-actions { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 11px; }

/* ── Cartes de la carte (menus) ────────────── */
.rs-menu-grid { display: grid; gap: 18px; grid-template-columns: repeat(auto-fill, minmax(245px, 1fr)); }
.rs-menu-card {
    background: #fff; border: 1px solid var(--rs-line); border-radius: var(--rs-radius);
    overflow: hidden; box-shadow: var(--rs-shadow-sm); display: flex; flex-direction: column;
    transition: all .2s cubic-bezier(.4,0,.2,1);
}
.rs-menu-card:hover { transform: translateY(-3px); box-shadow: var(--rs-shadow-md); }
.rs-menu-card.is-out { opacity: .62; }
.rs-menu-thumb { position: relative; height: 165px; background: var(--rs-brown-100); }
.rs-menu-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.rs-menu-thumb .rs-menu-placeholder {
    width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
    color: var(--rs-brown-400); font-size: 2.1rem;
}
.rs-menu-price {
    position: absolute; right: 10px; bottom: 10px; background: rgba(61,36,26,.9); color: #fff;
    padding: 5px 12px; border-radius: 999px; font-size: .82rem; font-weight: 700;
}
.rs-menu-cat { position: absolute; left: 10px; top: 10px; }
.rs-menu-body { padding: 15px 16px; flex: 1; display: flex; flex-direction: column; }
.rs-menu-name { font-size: .98rem; font-weight: 700; margin: 0 0 5px; }
.rs-menu-desc { font-size: .83rem; color: var(--rs-brown-600); flex: 1; margin-bottom: 13px; }
.rs-menu-actions { display: flex; gap: 7px; flex-wrap: wrap; }

/* ── Divers ────────────────────────────────── */
.rs-quick { display: grid; gap: 14px; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); }
.rs-quick-item {
    display: flex; align-items: center; gap: 13px; padding: 15px 17px; text-decoration: none;
    background: #fff; border: 1px solid var(--rs-line); border-radius: var(--rs-radius);
    box-shadow: var(--rs-shadow-sm); transition: all .2s cubic-bezier(.4,0,.2,1); color: var(--rs-brown-900);
}
.rs-quick-item:hover { transform: translateY(-2px); box-shadow: var(--rs-shadow-md); color: var(--rs-brown-500); }
.rs-quick-icon {
    width: 42px; height: 42px; flex: 0 0 42px; border-radius: 12px; font-size: 1.05rem;
    display: flex; align-items: center; justify-content: center;
    background: var(--rs-brown-100); color: var(--rs-brown-500);
}
.rs-quick-text strong { display: block; font-size: .91rem; }
.rs-quick-text span { font-size: .78rem; color: var(--rs-brown-600); }

.rs-rank { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid var(--rs-line); }
.rs-rank:last-child { border-bottom: none; }
.rs-rank-pos {
    width: 27px; height: 27px; flex: 0 0 27px; border-radius: 50%; font-size: .78rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center; background: var(--rs-brown-100); color: var(--rs-brown-500);
}
.rs-rank-body { flex: 1; min-width: 0; }
.rs-rank-name { font-size: .88rem; font-weight: 600; }
.rs-rank-meta { font-size: .76rem; color: var(--rs-brown-600); }

.rs-pagination { padding: 16px 20px; }
.rs-pagination .pagination { margin: 0; }
.rs-pagination .page-link { color: var(--rs-brown-500); border-color: var(--rs-line); }
.rs-pagination .active .page-link { background: var(--rs-brown-500); border-color: var(--rs-brown-500); color: #fff; }

@media (max-width: 640px) {
    .rs-page { padding: 18px 14px 40px; }
    .rs-header-title { font-size: 1.35rem; }
    .rs-table th, .rs-table td { padding: 11px 13px; }
}
</style>
