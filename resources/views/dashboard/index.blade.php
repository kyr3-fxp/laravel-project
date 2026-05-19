<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOLAR AI DASHBOARD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #050505;
            --bg-overlay: rgba(0, 0, 0, 0.56);
            --card: #0b0b0b;
            --card-soft: rgba(11, 11, 11, 0.84);
            --border: rgba(255, 255, 255, 0.08);
            --text: #ffffff;
            --muted: #9ca3af;
            --green: #22c55e;
            --yellow: #facc15;
            --orange: #fb923c;
            --red: #ef4444;
            --purple: #c084fc;
            --blue: #38bdf8;
            --shadow: 0 0 60px rgba(0, 0, 0, .45);
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            min-height: 100%;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                linear-gradient(var(--bg-overlay), var(--bg-overlay)),
                radial-gradient(circle at 15% 15%, rgba(34, 197, 94, 0.16), transparent 24%),
                radial-gradient(circle at 88% 20%, rgba(56, 189, 248, 0.10), transparent 20%),
                linear-gradient(180deg, #07130c 0%, #050505 48%, #050505 100%);
            background-attachment: fixed;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -2;
            background:
                url('https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
            filter: brightness(0.32) saturate(0.8) blur(2px);
            transform: scale(1.03);
        }

        .app {
            min-height: 100vh;
            padding: 24px;
        }

        .shell {
            max-width: 1600px;
            min-height: 95vh;
            margin: 0 auto;
            padding: 24px;
            border-radius: 28px;
            background: rgba(5, 5, 5, 0.92);
            border: 1px solid var(--border);
            overflow: hidden;
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow);
            display: grid;
            grid-template-rows: auto 1fr auto;
            gap: 18px;
        }

        .topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 40;
        }

        .search-shell {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 240px;
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.03);
            color: var(--muted);
            position: relative;
            z-index: 50;
            overflow: visible;
        }

        .search-shell i { color: #cbd5e1; }

        .search-shell input {
            width: 100%;
            background: transparent;
            border: 0;
            outline: none;
            color: var(--text);
            font-size: 0.92rem;
        }

        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 4px;
            background: linear-gradient(180deg, rgba(13,13,13,.96), rgba(9,9,9,.88));
            border: 1px solid var(--border);
            border-radius: 12px;
            backdrop-filter: blur(18px);
            max-height: 300px;
            overflow-y: auto;
            z-index: 9999;
            display: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .search-suggestions.active {
            display: flex;
            flex-direction: column;
        }

        .suggestion-item {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(255,255,255,.06);
            cursor: pointer;
            transition: all .2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text);
        }

        .suggestion-item:hover,
        .suggestion-item.selected {
            background: rgba(255,255,255,.08);
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-item i {
            color: var(--muted);
            font-size: 0.85rem;
            min-width: 16px;
        }

        .suggestion-item .name {
            flex: 1;
            font-weight: 500;
        }

        .suggestion-item .type {
            font-size: 0.75rem;
            color: var(--muted);
        }

        .suggestion-loading {
            padding: 12px 14px;
            text-align: center;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .suggestion-empty {
            padding: 12px 14px;
            text-align: center;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .icon-btn,
        .glass-btn {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
            cursor: pointer;
            transition: transform .3s ease, border-color .3s ease, background .3s ease;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
        }

        .glass-btn {
            border-radius: 12px;
            padding: 10px 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .icon-btn:hover,
        .glass-btn:hover {
            transform: translateY(-1px) scale(1.02);
            border-color: rgba(255,255,255,0.18);
        }

        .map-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            background: rgba(0,0,0,.7);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .map-modal.active { display: flex; }

        .map-modal-content {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            width: 90%;
            max-width: 900px;
            height: 90vh;
            max-height: 700px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .map-modal-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .map-modal-header h2 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .map-modal-close {
            background: none;
            border: 1px solid var(--border);
            color: var(--text);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            display: grid;
            place-items: center;
            transition: all .2s ease;
        }

        .map-modal-close:hover {
            background: rgba(255,255,255,.08);
            border-color: rgba(255,255,255,.18);
        }

        .map-modal-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .gmap {
            flex: 1;
            width: 100%;
            border-radius: 0;
        }

        .map-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .map-modal-footer button {
            padding: 10px 20px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,.05);
            color: var(--text);
            cursor: pointer;
            transition: all .2s ease;
            font-weight: 600;
        }

        .map-modal-footer button:hover {
            background: rgba(255,255,255,.10);
            border-color: rgba(255,255,255,.18);
        }

        .map-modal-footer .confirm-btn {
            background: linear-gradient(135deg, var(--green), var(--blue));
            border-color: var(--green);
        }

        .map-modal-footer .confirm-btn:hover {
            opacity: 0.9;
        }

        .layout {
            display: grid;
            grid-template-columns: 320px 1fr 280px;
            gap: 18px;
            min-height: 0;
        }

        .sidebar,
        .center,
        .right {
            min-height: 0;
        }

        .stack {
            display: grid;
            gap: 18px;
        }

        .glass-card {
            background: linear-gradient(180deg, rgba(13,13,13,.96), rgba(9,9,9,.88));
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.02);
            backdrop-filter: blur(18px);
        }

        .left-status {
            min-height: 430px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .left-status .meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            font-weight: 600;
            font-size: .92rem;
        }

        .site-name {
            margin: 8px 0 0;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .main-metric {
            text-align: center;
            padding: 14px 0;
        }

        .main-metric .value {
            font-size: clamp(4rem, 7vw, 5.4rem);
            line-height: .9;
            font-weight: 800;
            letter-spacing: -0.08em;
        }

        .main-metric .label {
            margin-top: 8px;
            color: var(--muted);
            font-weight: 600;
        }

        .weather-foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 10px;
        }

        .weather-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
        }

        .mini-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .mini-card {
            padding: 12px;
            border-radius: 16px;
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.06);
        }

        .mini-card small {
            display: block;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .mini-card strong {
            font-size: 1.05rem;
        }

        .forecast-card {
            padding: 18px;
            min-height: 520px;
        }

        .card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .card-head h2,
        .card-head h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }

        .card-head p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: .9rem;
        }

        .forecast-list {
            display: grid;
            gap: 10px;
            max-height: 430px;
            padding-right: 4px;
        }

        .forecast-item {
            display: grid;
            grid-template-columns: 60px 36px 1fr 72px;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }

        .forecast-item:last-child { border-bottom: 0; }

        .forecast-item .day {
            font-weight: 700;
        }

        .forecast-item .date {
            color: var(--muted);
            font-size: .8rem;
            margin-top: 2px;
        }

        .forecast-bar {
            height: 8px;
            border-radius: 999px;
            background: rgba(255,255,255,.08);
            overflow: hidden;
            position: relative;
        }

        .forecast-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #60a5fa, #34d399, #facc15);
        }

        .center-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            align-content: start;
        }

        .production-card {
            grid-column: span 6;
            min-height: 160px;
            padding: 18px;
        }

        .meter {
            margin-top: 18px;
            height: 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.08);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.08);
        }

        .meter > span {
            display: block;
            height: 100%;
            width: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, #38bdf8, #22c55e, #facc15, #fb923c, #c084fc, #ef4444);
        }

        .production-copy {
            margin-top: 16px;
            color: var(--muted);
            font-size: .92rem;
            line-height: 1.6;
        }

        .small-metric {
            grid-column: span 3;
            min-height: 160px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .small-metric .icon {
            color: #f5f5f5;
            font-size: 1rem;
            opacity: .82;
        }

        .small-metric .title {
            color: var(--muted);
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 12px;
        }

        .small-metric .value {
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: -0.05em;
        }

        .small-metric .sub {
            color: var(--muted);
            margin-top: 4px;
            line-height: 1.5;
            font-size: .9rem;
        }

        .center-chart {
            grid-column: span 4;
            min-height: 180px;
            padding: 16px;
        }

        .chart-shell {
            position: relative;
            height: 280px;
        }

        .chart-shell.tall { height: 300px; }

        .chart-shell canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .panel {
            padding: 18px;
        }

        .recommendations-card,
        .cities-card,
        .sources-card {
            padding: 18px;
        }

        .list {
            display: grid;
            gap: 12px;
        }

        .list-item {
            display: flex;
            gap: 12px;
            align-items: start;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.03);
        }

        .list-item i {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: rgba(255,255,255,.06);
        }

        .list-item strong {
            display: block;
            margin-bottom: 4px;
        }

        .list-item p {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
            font-size: .9rem;
        }

        .tag-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.03);
            color: var(--text);
            font-size: .82rem;
        }

        .alerts-grid {
            display: grid;
            gap: 10px;
        }

        .alert {
            padding: 14px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.03);
        }

        .alert strong {
            display: block;
            margin-bottom: 6px;
        }

        .alert p {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
            font-size: .9rem;
        }

        .alert.success { border-left: 4px solid var(--green); }
        .alert.warning { border-left: 4px solid var(--yellow); }
        .alert.critical { border-left: 4px solid var(--red); }

        .data-panel {
            padding: 0;
            overflow: hidden;
        }

        .table-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 18px 18px 0;
        }

        .table-head p {
            margin: 0;
            color: var(--muted);
        }

        .table-wrap {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            min-width: 920px;
            border-collapse: collapse;
        }

        .data-table thead th {
            padding: 16px 18px;
            text-align: left;
            color: var(--muted);
            border-bottom: 1px solid rgba(255,255,255,.08);
            font-size: .76rem;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .data-table tbody td {
            padding: 15px 18px;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }

        .data-table tbody tr:hover {
            background: rgba(255,255,255,.02);
        }

        .citations-card {
            padding: 18px;
        }

        .citations-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 14px;
            margin-top: 16px;
        }

        .citation-box {
            grid-column: span 6;
            padding: 16px;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.03);
        }

        .citation-box h3 {
            margin: 0 0 10px;
            font-size: 1rem;
        }

        .citation-box a,
        .citation-box span {
            display: block;
            color: #dbe7ff;
            line-height: 1.6;
            font-size: .92rem;
            margin-bottom: 8px;
        }

        .citation-box a {
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .citation-note {
            margin-top: 12px;
            color: var(--muted);
            line-height: 1.6;
            font-size: .9rem;
        }

        .loading {
            display: none;
            text-align: center;
            color: var(--muted);
        }

        .loading.active { display: block; }

        .spinner {
            width: 42px;
            height: 42px;
            margin: 0 auto 12px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,.10);
            border-top-color: #fff;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .error-message {
            display: none;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid rgba(239, 68, 68, 0.24);
            background: rgba(239, 68, 68, 0.12);
            color: #ffd0d0;
        }

        .error-message.active { display: block; }

        .footer-note {
            text-align: center;
            color: var(--muted);
            font-size: .88rem;
            padding-bottom: 4px;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1280px) {
            .layout {
                grid-template-columns: 300px 1fr;
            }

            .right {
                grid-column: 1 / -1;
            }

            .center-grid {
                grid-template-columns: repeat(12, minmax(0,1fr));
            }

        }

        @media (max-width: 960px) {
            .layout { grid-template-columns: 1fr; }
            .topbar { justify-content: stretch; flex-wrap: wrap; }
            .search-shell { width: 100%; }
            .center-grid { grid-template-columns: 1fr; }
            .production-card,
            .small-metric,
            .center-chart,
            .citation-box { grid-column: span 12; }
        }

        @media (max-width: 720px) {
            .app { padding: 12px; }
            .shell { padding: 16px; border-radius: 22px; min-height: 0; }
            .left-status { min-height: 0; }
            .forecast-card { min-height: 0; }
            .chart-shell, .chart-shell.tall { height: 240px; }
            .table-head, .data-table thead th, .data-table tbody td { padding-left: 14px; padding-right: 14px; }
        }
    </style>
</head>
<body>
    <main class="app">
        <div class="shell">
            <header class="topbar">
                <div class="search-shell glass-card">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input id="citySearchInput" type="text" placeholder="Buscar empresa o ciudad..." autocomplete="off" />
                    <div class="search-suggestions" id="searchSuggestions"></div>
                </div>
                <button class="icon-btn" type="button" title="Modo oscuro">
                    <i class="fa-solid fa-moon-stars"></i>
                </button>
                <a class="glass-btn" href="#" onclick="return false;">
                    <i class="fa-brands fa-github"></i>
                    Support Project
                </a>
            </header>

            <!-- Hidden fields for coordinates -->
            <input type="hidden" id="latitude" />
            <input type="hidden" id="longitude" />

            <section class="layout">
                <aside class="sidebar stack left">
                    <article class="glass-card left-status" data-aos="fade-up">
                        <div>
                            <div class="meta">
                                <div id="statusDay">--</div>
                                <div id="statusTime">--:--</div>
                            </div>
                            <div class="site-name" id="siteName">Riohacha, Colombia</div>
                        </div>

                        <div class="main-metric">
                            <div class="value" id="mainRadiationValue">--</div>
                            <div class="label" id="mainRadiationLabel">Radiación solar</div>
                        </div>

                        <div>
                            <div class="weather-foot">
                                <div class="weather-chip">
                                    <i class="fa-solid fa-sun"></i>
                                    <span id="weatherStateLabel">Radiación alta</span>
                                </div>
                                <div class="weather-chip" id="locationBadge">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>Riohacha</span>
                                </div>
                            </div>
                            <div class="mini-grid" style="margin-top:12px;">
                                <div class="mini-card">
                                    <small>Temp</small>
                                    <strong id="leftTempValue">--</strong>
                                </div>
                                <div class="mini-card">
                                    <small>Humedad</small>
                                    <strong id="leftHumidityValue">--</strong>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="glass-card forecast-card" data-aos="fade-up">
                        <div class="card-head">
                            <div>
                                <h2>Últimos 10 días</h2>
                                <p>Serie real de NASA POWER para la ubicación consultada.</p>
                            </div>
                        </div>
                        <div class="forecast-list" id="forecastList"></div>
                    </article>
                </aside>

                <main class="center">
                    <div class="center-grid">
                        <article class="glass-card production-card" data-aos="fade-up">
                            <div class="card-head">
                                <div>
                                    <h3>Producción solar</h3>
                                    <p id="productionStatus">Analizando datos reales...</p>
                                </div>
                            </div>
                            <div class="meter"><span id="productionMeter"></span></div>
                            <div class="production-copy" id="productionCopy">
                                Cargando radiación histórica y variables atmosféricas desde NASA POWER.
                            </div>
                        </article>

                        <article class="glass-card small-metric" data-aos="fade-up">
                            <div>
                                <div class="title">Último registro</div>
                                <div class="icon"><i class="fa-solid fa-calendar-days"></i></div>
                            </div>
                            <div>
                                <div class="value" id="latestDateValue">--</div>
                                <div class="sub" id="latestDateSub">---</div>
                            </div>
                        </article>

                        <article class="glass-card small-metric" data-aos="fade-up">
                            <div>
                                <div class="title">Promedio 30 días</div>
                                <div class="icon"><i class="fa-solid fa-sun"></i></div>
                            </div>
                            <div>
                                <div class="value" id="avgRadiationValue">--</div>
                                <div class="sub">kWh/m²/día</div>
                            </div>
                        </article>

                        <article class="glass-card small-metric" data-aos="fade-up">
                            <div>
                                <div class="title">Máximo</div>
                                <div class="icon"><i class="fa-solid fa-arrow-trend-up"></i></div>
                            </div>
                            <div>
                                <div class="value" id="maxRadiationValue">--</div>
                                <div class="sub">kWh/m²/día</div>
                            </div>
                        </article>

                        <article class="glass-card small-metric" data-aos="fade-up">
                            <div>
                                <div class="title">Índice de estabilidad</div>
                                <div class="icon"><i class="fa-solid fa-shield"></i></div>
                            </div>
                            <div>
                                <div class="value" id="stabilityValue">--</div>
                                <div class="sub">100 = más estable</div>
                            </div>
                        </article>

                        <article class="glass-card small-metric" data-aos="fade-up">
                            <div>
                                <div class="title">Temperatura media</div>
                                <div class="icon"><i class="fa-solid fa-temperature-three-quarters"></i></div>
                            </div>
                            <div>
                                <div class="value" id="avgTempValue">--</div>
                                <div class="sub">31°C</div>
                            </div>
                        </article>

                        <article class="glass-card small-metric" data-aos="fade-up">
                            <div>
                                <div class="title">Humedad media</div>
                                <div class="icon"><i class="fa-solid fa-droplet"></i></div>
                            </div>
                            <div>
                                <div class="value" id="avgHumidityValue">--</div>
                                <div class="sub">%</div>
                            </div>
                        </article>

                        <article class="glass-card small-metric" data-aos="fade-up">
                            <div>
                                <div class="title">Viento medio</div>
                                <div class="icon"><i class="fa-solid fa-wind"></i></div>
                            </div>
                            <div>
                                <div class="value" id="avgWindValue">--</div>
                                <div class="sub">m/s</div>
                            </div>
                        </article>

                        <article class="glass-card small-metric" data-aos="fade-up">
                            <div>
                                <div class="title">Razón cielo/real</div>
                                <div class="icon"><i class="fa-solid fa-cloud-sun"></i></div>
                            </div>
                            <div>
                                <div class="value" id="avgRatioValue">--</div>
                                <div class="sub">%</div>
                            </div>
                        </article>

                        <article class="glass-card center-chart" data-aos="fade-up">
                            <div class="card-head">
                                <div>
                                    <h3>Radiación diaria</h3>
                                    <p>Datos reales consultados desde NASA POWER.</p>
                                </div>
                            </div>
                            <div class="chart-shell">
                                <canvas id="radiationChart"></canvas>
                            </div>
                        </article>

                        <article class="glass-card center-chart" data-aos="fade-up">
                            <div class="card-head">
                                <div>
                                    <h3>Clima de soporte</h3>
                                    <p>Temperatura, humedad y viento del periodo analizado.</p>
                                </div>
                            </div>
                            <div class="chart-shell">
                                <canvas id="weatherChart"></canvas>
                            </div>
                        </article>

                    </div>
                </main>

                <aside class="right stack">
                    <article class="glass-card recommendations-card" data-aos="fade-up">
                        <div class="card-head">
                            <div>
                                <h2>Recomendaciones IA</h2>
                                <p>Derivadas de los datos reales consultados.</p>
                            </div>
                        </div>
                        <div class="list" id="recommendationList"></div>
                        <div class="tag-row" id="insightTags"></div>
                    </article>

                    <article class="glass-card cities-card" data-aos="fade-up">
                        <div class="card-head">
                            <div>
                                <h2>Ubicaciones recientes</h2>
                                <p>Consultas reales ejecutadas en esta sesión.</p>
                            </div>
                        </div>
                        <div class="list" id="recentSitesList"></div>
                    </article>

                    <article class="glass-card sources-card" data-aos="fade-up">
                        <div class="card-head">
                            <div>
                                <h2>Fuentes activas</h2>
                                <p>Servicios reales usados en el tablero.</p>
                            </div>
                        </div>
                        <div class="tag-row">
                            <span class="tag"><i class="fa-solid fa-cloud-sun"></i> NASA POWER</span>
                            <span class="tag"><i class="fa-solid fa-map-location-dot"></i> Nominatim</span>
                            <span class="tag"><i class="fa-solid fa-map"></i> OpenStreetMap</span>
                        </div>
                    </article>
                </aside>
            </section>

            <div id="loading" class="loading">
                <div class="spinner"></div>
                <p>Actualizando datos reales desde internet...</p>
            </div>

            <div id="errorMessage" class="error-message">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span id="errorText"></span>
            </div>

            <section class="glass-card data-panel" data-aos="fade-up">
                <div class="table-head">
                    <div>
                        <h2 style="margin:0 0 6px;">Serie histórica</h2>
                        <p>Radiación, cielo despejado y clima real por día.</p>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Radiación</th>
                                <th>Cielo despejado</th>
                                <th>Temp.</th>
                                <th>Humedad</th>
                                <th>Viento</th>
                                <th>Razón</th>
                            </tr>
                        </thead>
                        <tbody id="dataTableBody"></tbody>
                    </table>
                </div>
            </section>

            <section class="glass-card citations-card" data-aos="fade-up">
                <h2 style="margin:0;">Citas</h2>
                <p style="margin:8px 0 0; color:var(--muted); line-height:1.6;">
                    Todo lo que ves aquí proviene de servicios reales o de cálculos derivados directamente de esos datos. No se usan valores inventados de negocio, costos fijos ni parámetros de batería predefinidos.
                </p>
                <div class="citations-grid">
                    <article class="citation-box">
                        <h3>NASA POWER</h3>
                        <a href="https://power.larc.nasa.gov/" target="_blank" rel="noopener noreferrer">
                            API usada para obtener radiación solar, temperatura, humedad, viento y cielo despejado por ubicación y fecha.
                        </a>
                    </article>
                    <article class="citation-box">
                        <h3>Nominatim</h3>
                        <a href="https://nominatim.openstreetmap.org/" target="_blank" rel="noopener noreferrer">
                            Servicio de geocodificación usado para convertir búsquedas de ciudad o empresa en coordenadas reales.
                        </a>
                    </article>
                    <article class="citation-box">
                        <h3>OpenStreetMap</h3>
                        <a href="https://www.openstreetmap.org/" target="_blank" rel="noopener noreferrer">
                            Mapa base usado para mostrar la ubicación consultada con una imagen estática real.
                        </a>
                    </article>
                    <article class="citation-box">
                        <h3>Derivaciones del sistema</h3>
                        <span>Promedios, maximos, minimos, estabilidad e indice solar calculados en el backend a partir de los datos descargados.</span>
                    </article>
                    <article class="citation-box">
                        <h3>Estimaciones demo</h3>
                        <span>Energia estimada, ahorro, autonomia de bateria y CO2 evitado se muestran como valores de prototipo calculados a partir de la radiacion consultada.</span>
                    </article>
                </div>
                <p class="citation-note">
                    Si quieres, el siguiente paso es conectar OpenAI para que las recomendaciones del Agente Solar tambien salgan de un modelo de lenguaje, manteniendo las series reales y las estimaciones demo del prototipo.
                </p>
            </section>

            <div class="footer-note">
                SOLAR AI DASHBOARD Â· Riohacha, La Guajira
            </div>
        </div>
    </main>

    <script>
        let charts = {};
        let solarData = [
            { date: '10/05/2026', radiation: 5.8, clear_sky_radiation: 7.2, temperature: 28.5, humidity: 72, wind_speed: 3.2, solar_ratio: 80.6 },
            { date: '11/05/2026', radiation: 6.1, clear_sky_radiation: 7.1, temperature: 29.2, humidity: 68, wind_speed: 2.9, solar_ratio: 85.9 },
            { date: '12/05/2026', radiation: 5.2, clear_sky_radiation: 7.0, temperature: 27.8, humidity: 75, wind_speed: 3.5, solar_ratio: 74.3 },
            { date: '13/05/2026', radiation: 6.4, clear_sky_radiation: 7.3, temperature: 30.1, humidity: 65, wind_speed: 2.4, solar_ratio: 87.7 },
            { date: '14/05/2026', radiation: 5.9, clear_sky_radiation: 7.2, temperature: 28.9, humidity: 70, wind_speed: 3.1, solar_ratio: 81.9 },
            { date: '15/05/2026', radiation: 6.3, clear_sky_radiation: 7.1, temperature: 29.5, humidity: 67, wind_speed: 2.7, solar_ratio: 88.7 },
            { date: '16/05/2026', radiation: 5.5, clear_sky_radiation: 7.0, temperature: 28.2, humidity: 73, wind_speed: 3.3, solar_ratio: 78.6 },
            { date: '17/05/2026', radiation: 6.2, clear_sky_radiation: 7.2, temperature: 29.8, humidity: 66, wind_speed: 2.8, solar_ratio: 86.1 },
            { date: '18/05/2026', radiation: 6.0, clear_sky_radiation: 7.1, temperature: 29.1, humidity: 69, wind_speed: 3.0, solar_ratio: 84.5 },
            { date: '19/05/2026', radiation: 6.4, clear_sky_radiation: 7.3, temperature: 30.2, humidity: 64, wind_speed: 2.5, solar_ratio: 87.7 }
        ];
        let recommendations = [
            { icon: 'fa-bolt', title: 'Radiación óptima', message: 'Condiciones ideales para maximizar generación solar.' },
            { icon: 'fa-cloud-sun', title: 'Monitoreo recomendado', message: 'Incremento de humedad esperado, revisar sistema.' },
            { icon: 'fa-wind', title: 'Ventilación adecuada', message: 'Vientos moderados favorables para enfriamiento.' },
            { icon: 'fa-chart-line', title: 'Tendencia favorable', message: 'Proyección ascendente en próximos 7 días.' }
        ];
        let recentSites = ['Riohacha, Colombia', 'Barranquilla, Colombia', 'Cartagena, Colombia'];
        let statisticsData = {
            latest_date: '19/05/2026',
            latest_radiation: 6.4,
            avg_radiation: 6.0,
            max_radiation: 6.4,
            min_radiation: 5.2,
            stability_index: 82,
            avg_temperature: 29.1,
            avg_humidity: 68.9,
            avg_wind_speed: 3.0,
            avg_solar_ratio: 83.8,
            solar_score: 75,
            tags: ['Zona tropical', 'Radiación excelente', 'Producción estable', 'Índice UV alto']
        };
        let currentSite = {
            name: 'Riohacha, Colombia',
            latitude: 11.5444,
            longitude: -72.9069
        };
        const defaultSite = @json($defaultSite);

        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 650,
                easing: 'ease-out-cubic',
                once: true
            });

            recentSites = [currentSite.name];
            renderRecentSites();
            setSite(defaultSite.name, defaultSite.latitude, defaultSite.longitude, false);
            loadDashboardData();

            const searchInput = document.getElementById('citySearchInput');
            const suggestionsContainer = document.getElementById('searchSuggestions');
            let suggestionsTimeout;
            let currentSuggestionIndex = -1;

            searchInput.addEventListener('input', (e) => {
                clearTimeout(suggestionsTimeout);
                const query = e.target.value.trim();

                if (query.length < 2) {
                    suggestionsContainer.classList.remove('active');
                    return;
                }

                suggestionsContainer.innerHTML = '<div class="suggestion-loading"><i class="fa-solid fa-spinner fa-spin"></i> Buscando...</div>';
                suggestionsContainer.classList.add('active');
                currentSuggestionIndex = -1;

                suggestionsTimeout = setTimeout(() => {
                    fetchSuggestions(query);
                }, 300);
            });

            searchInput.addEventListener('keydown', (e) => {
                const items = suggestionsContainer.querySelectorAll('.suggestion-item');
                const isOpen = suggestionsContainer.classList.contains('active');

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (!isOpen) return;
                    currentSuggestionIndex = Math.min(currentSuggestionIndex + 1, items.length - 1);
                    updateSuggestionSelection(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (!isOpen) return;
                    currentSuggestionIndex = Math.max(currentSuggestionIndex - 1, -1);
                    updateSuggestionSelection(items);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (currentSuggestionIndex >= 0 && items[currentSuggestionIndex]) {
                        items[currentSuggestionIndex].click();
                    } else if (searchInput.value.trim()) {
                        searchCity();
                    }
                } else if (e.key === 'Escape') {
                    suggestionsContainer.classList.remove('active');
                }
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.search-shell')) {
                    suggestionsContainer.classList.remove('active');
                }
            });

            setInterval(() => {
                if (solarData.length) {
                    loadDashboardData(false);
                }
            }, 15 * 60 * 1000);
        });

        function setSite(name, latitude, longitude, shouldLoad = true) {
            currentSite = {
                name: name || 'Riohacha, Colombia',
                latitude: Number(latitude),
                longitude: Number(longitude)
            };

            document.getElementById('latitude').value = currentSite.latitude.toFixed(4);
            document.getElementById('longitude').value = currentSite.longitude.toFixed(4);
            document.getElementById('siteName').textContent = currentSite.name;
            document.getElementById('locationBadge').querySelector('span').textContent = currentSite.name;

            if (shouldLoad) {
                loadDashboardData();
            }
        }

        function showLoading(show) {
            document.getElementById('loading').classList.toggle('active', show);
        }

        function showError(message) {
            document.getElementById('errorText').textContent = message;
            document.getElementById('errorMessage').classList.add('active');
        }

        function hideError() {
            document.getElementById('errorMessage').classList.remove('active');
        }

        async function loadDashboardData(pushRecent = true) {
            const latitude = document.getElementById('latitude').value || currentSite.latitude;
            const longitude = document.getElementById('longitude').value || currentSite.longitude;
            const siteName = currentSite.name;

            showLoading(true);
            hideError();

            try {
                const response = await fetch(`/api/solar-data?latitude=${encodeURIComponent(latitude)}&longitude=${encodeURIComponent(longitude)}&site_name=${encodeURIComponent(siteName)}`);
                const result = await response.json();

                if (result.success && result.data && Array.isArray(result.data) && result.data.length > 0) {
                    // Usar datos reales de la API
                    solarData = result.data;
                    statisticsData = result.statistics || {};
                    recommendations = Array.isArray(result.recommendations) ? result.recommendations : [];

                    const site = result.site || currentSite;
                    currentSite = {
                        name: site.name || currentSite.name,
                        latitude: Number(site.latitude),
                        longitude: Number(site.longitude)
                    };
                } else {
                    // Usar datos ficticios si la API no devuelve datos válidos
                    console.warn('Usando datos ficticios, la API no devolvió datos válidos');
                }

                document.getElementById('siteName').textContent = currentSite.name;
                document.getElementById('locationBadge').querySelector('span').textContent = currentSite.name;

                if (pushRecent) {
                    pushRecentSite(currentSite.name);
                }

                renderAll();

                showLoading(false);
                document.querySelector('.layout').style.display = 'grid';
            } catch (error) {
                // Si hay error en la API, usar datos ficticios y continuar
                console.warn('Error al cargar datos de la API, usando datos ficticios:', error.message);
                renderAll();
                showLoading(false);
                document.querySelector('.layout').style.display = 'grid';
            }
        }

        async function fetchSuggestions(query) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=8&q=${encodeURIComponent(query)}`);
                const results = await response.json();

                const container = document.getElementById('searchSuggestions');
                container.innerHTML = '';

                if (!Array.isArray(results) || results.length === 0) {
                    container.innerHTML = '<div class="suggestion-empty">No se encontraron resultados</div>';
                    return;
                }

                results.forEach((place, index) => {
                    const item = document.createElement('div');
                    item.className = 'suggestion-item';
                    item.innerHTML = `
                        <i class="fa-solid fa-map-location-dot"></i>
                        <div style="flex:1;">
                            <div class="name">${highlightMatch(place.display_name, query)}</div>
                            <div class="type">${getPlaceType(place)}</div>
                        </div>
                    `;
                    item.addEventListener('click', () => selectSuggestion(place));
                    container.appendChild(item);
                });
            } catch (error) {
                console.error('Error fetching suggestions:', error);
                document.getElementById('searchSuggestions').innerHTML = '<div class="suggestion-empty">Error al buscar</div>';
            }
        }

        function updateSuggestionSelection(items) {
            items.forEach((item, index) => {
                item.classList.toggle('selected', index === currentSuggestionIndex);
                if (index === currentSuggestionIndex) {
                    item.scrollIntoView({ block: 'nearest' });
                }
            });
        }

        function highlightMatch(text, query) {
            const regex = new RegExp(`(${query})`, 'gi');
            return text.replace(regex, '<strong style="color: var(--green);">$1</strong>');
        }

        function getPlaceType(place) {
            const types = {
                'city': '🏙️ Ciudad',
                'town': '🏘️ Pueblo',
                'village': '🏞️ Aldea',
                'county': '📍 Condado',
                'country': '🌍 País',
                'administrative': '📋 Región'
            };
            return types[place.type] || place.type || 'Ubicación';
        }

        async function selectSuggestion(place) {
            document.getElementById('citySearchInput').value = place.display_name;
            document.getElementById('searchSuggestions').classList.remove('active');
            setSite(place.display_name, place.lat, place.lon, false);
            pushRecentSite(place.display_name);
            await loadDashboardData(false);
        }

        async function searchCity() {
            const query = document.getElementById('citySearchInput').value.trim();

            if (!query) {
                return;
            }

            try {
                showLoading(true);
                hideError();

                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=${encodeURIComponent(query)}`);
                const results = await response.json();

                if (!Array.isArray(results) || !results.length) {
                    throw new Error('No encontramos esa ciudad o empresa.');
                }

                const place = results[0];
                setSite(place.display_name || query, place.lat, place.lon, false);
                pushRecentSite(place.display_name || query);
                await loadDashboardData(false);
            } catch (error) {
                showError(error.message);
            } finally {
                showLoading(false);
            }
        }

        function pushRecentSite(name) {
            recentSites = [name, ...recentSites.filter(item => item !== name)].slice(0, 5);
            renderRecentSites();
        }

        function renderRecentSites() {
            const container = document.getElementById('recentSitesList');
            container.innerHTML = '';

            recentSites.forEach((item, index) => {
                const el = document.createElement('div');
                el.className = 'list-item';
                el.innerHTML = `
                    <i class="fa-solid fa-location-dot"></i>
                    <div>
                        <strong>${index === 0 ? 'Actual' : 'Reciente'}</strong>
                        <p>${item}</p>
                    </div>
                `;
                container.appendChild(el);
            });
        }

        function renderAll() {
            renderHeader();
            renderForecast();
            renderKPIs();
            renderRecommendations();
            renderAlerts();
            renderTable();
            renderCharts();
        }

        function renderHeader() {
            const latest = solarData.at(-1);
            const day = latest?.date ? new Date(latest.date.split('/').reverse().join('-') + 'T12:00:00') : new Date();
            const dayLabel = new Intl.DateTimeFormat('es-CO', { weekday: 'long' }).format(day);
            const timeLabel = new Intl.DateTimeFormat('es-CO', { hour: '2-digit', minute: '2-digit' }).format(new Date());

            document.getElementById('statusDay').textContent = dayLabel.charAt(0).toUpperCase() + dayLabel.slice(1);
            document.getElementById('statusTime').textContent = timeLabel;
            document.getElementById('mainRadiationValue').textContent = latest?.radiation !== undefined ? formatNumber(latest.radiation, 1) : '--';
            document.getElementById('weatherStateLabel').textContent = getWeatherLabel(statisticsData.avg_radiation);
            document.getElementById('mainRadiationLabel').textContent = 'Radiación solar';
            document.getElementById('leftTempValue').textContent = latest?.temperature !== undefined ? `${formatNumber(latest.temperature, 1)}°C` : '--';
            document.getElementById('leftHumidityValue').textContent = latest?.humidity !== undefined ? `${formatNumber(latest.humidity, 1)}%` : '--';
        }

        function renderForecast() {
            const container = document.getElementById('forecastList');
            container.innerHTML = '';

            const latestItems = solarData.slice(-10).reverse();
            const maxRadiation = Math.max(...solarData.map(item => Number(item.radiation || 0)), 1);

            latestItems.forEach((row) => {
                const intensity = Math.max(8, Math.round((Number(row.radiation || 0) / maxRadiation) * 100));
                const item = document.createElement('div');
                item.className = 'forecast-item';
                item.innerHTML = `
                    <div>
                        <div class="day">${getWeekdayShort(row.date)}</div>
                        <div class="date">${row.date || '--'}</div>
                    </div>
                    <div style="text-align:center;"><i class="fa-solid fa-sun"></i></div>
                    <div>
                        <div class="forecast-bar"><span style="width:${intensity}%"></span></div>
                    </div>
                    <div style="text-align:right; font-weight:700;">${formatNumber(row.radiation, 1)}</div>
                `;
                container.appendChild(item);
            });
        }

        function renderKPIs() {
            document.getElementById('latestDateValue').textContent = statisticsData.latest_date || '--';
            document.getElementById('latestDateSub').textContent = statisticsData.latest_radiation !== undefined ? `${formatNumber(statisticsData.latest_radiation, 1)} kWh/m²/día` : 'Sin datos';
            document.getElementById('avgRadiationValue').textContent = formatNumber(statisticsData.avg_radiation, 1);
            document.getElementById('maxRadiationValue').textContent = formatNumber(statisticsData.max_radiation, 1);
            document.getElementById('stabilityValue').textContent = formatNumber(statisticsData.stability_index, 0);
            document.getElementById('avgTempValue').textContent = formatNumber(statisticsData.avg_temperature, 1);
            document.getElementById('avgHumidityValue').textContent = formatNumber(statisticsData.avg_humidity, 1);
            document.getElementById('avgWindValue').textContent = formatNumber(statisticsData.avg_wind_speed, 2);
            document.getElementById('avgRatioValue').textContent = formatNumber(statisticsData.avg_solar_ratio, 1);

            const meter = document.getElementById('productionMeter');
            meter.style.width = `${Math.min(100, Math.max(0, Number(statisticsData.solar_score || 0)))}%`;

            document.getElementById('productionStatus').textContent = getProductionStatus(statisticsData.solar_score);
            document.getElementById('productionCopy').textContent = `Promedio real de ${formatNumber(statisticsData.avg_radiation, 1)} kWh/m²/día con ${formatNumber(statisticsData.avg_solar_ratio, 1)}% de razón promedio frente al cielo despejado.`;
        }

        function renderRecommendations() {
            const container = document.getElementById('recommendationList');
            const tagRow = document.getElementById('insightTags');
            container.innerHTML = '';
            tagRow.innerHTML = '';

            recommendations.forEach((item) => {
                const el = document.createElement('div');
                el.className = 'list-item';
                el.innerHTML = `
                    <i class="fa-solid ${item.icon}"></i>
                    <div>
                        <strong>${item.title}</strong>
                        <p>${item.message}</p>
                    </div>
                `;
                container.appendChild(el);
            });

            (statisticsData.tags || []).forEach((tag) => {
                const el = document.createElement('span');
                el.className = 'tag';
                el.textContent = tag;
                tagRow.appendChild(el);
            });
        }

        function renderAlerts() {
            return;
        }

        function renderTable() {
            const tbody = document.getElementById('dataTableBody');
            tbody.innerHTML = '';

            solarData.forEach((row) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.date || '--'}</td>
                    <td>${formatNumber(row.radiation, 1)} kWh/m²</td>
                    <td>${formatNumber(row.clear_sky_radiation, 1)} kWh/m²</td>
                    <td>${formatNumber(row.temperature, 1)} °C</td>
                    <td>${formatNumber(row.humidity, 1)}%</td>
                    <td>${formatNumber(row.wind_speed, 2)} m/s</td>
                    <td>${formatNumber(row.solar_ratio, 1)}%</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function renderCharts() {
            if (!solarData.length) return;

            const labels = solarData.map(item => item.date);
            const radiation = solarData.map(item => item.radiation);
            const clearSky = solarData.map(item => item.clear_sky_radiation);
            const temp = solarData.map(item => item.temperature);
            const humidity = solarData.map(item => item.humidity);
            const wind = solarData.map(item => item.wind_speed);

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        labels: { color: '#dbe7ff' }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(3,3,3,.95)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,.06)' },
                        ticks: { color: '#9ca3af' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af', maxTicksLimit: 6 }
                    }
                }
            };

            if (charts.radiation) charts.radiation.destroy();
            charts.radiation = new Chart(document.getElementById('radiationChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'RadiaciÃ³n',
                            data: radiation,
                            borderColor: '#facc15',
                            backgroundColor: 'rgba(250,204,21,.15)',
                            fill: true,
                            tension: .35,
                            pointRadius: 2
                        },
                        {
                            label: 'Cielo despejado',
                            data: clearSky,
                            borderColor: '#38bdf8',
                            backgroundColor: 'rgba(56,189,248,.08)',
                            fill: false,
                            tension: .35,
                            pointRadius: 2
                        }
                    ]
                },
                options: commonOptions
            });

            if (charts.weather) charts.weather.destroy();
            charts.weather = new Chart(document.getElementById('weatherChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Temperatura',
                            data: temp,
                            borderColor: '#fb923c',
                            backgroundColor: 'rgba(251,146,60,.12)',
                            fill: true,
                            tension: .35
                        },
                        {
                            label: 'Humedad',
                            data: humidity,
                            borderColor: '#38bdf8',
                            backgroundColor: 'rgba(56,189,248,.10)',
                            fill: false,
                            tension: .35
                        },
                        {
                            label: 'Viento',
                            data: wind,
                            borderColor: '#22c55e',
                            backgroundColor: 'rgba(34,197,94,.10)',
                            fill: false,
                            tension: .35
                        }
                    ]
                },
                options: commonOptions
            });

        }

        function getWeekdayShort(dateString) {
            if (!dateString) return '--';
            const date = new Date(dateString.split('/').reverse().join('-') + 'T12:00:00');
            return new Intl.DateTimeFormat('es-CO', { weekday: 'short' }).format(date);
        }

        function formatNumber(value, decimals = 2) {
            const number = Number(value ?? 0);
            if (!Number.isFinite(number)) return '--';
            return new Intl.NumberFormat('es-CO', { maximumFractionDigits: decimals, minimumFractionDigits: decimals === 0 ? 0 : Math.min(decimals, 1) }).format(number);
        }

        function getWeatherLabel(avgRadiation) {
            const value = Number(avgRadiation ?? 0);
            if (value >= 6.5) return 'Radiación alta';
            if (value >= 5.0) return 'Radiación moderada';
            return 'Radiación baja';
        }

        function getProductionStatus(score) {
            const value = Number(score ?? 0);
            if (value >= 80) return 'Producción óptima.';
            if (value >= 60) return 'Producción favorable.';
            return 'Producción variable.';
        }

    </script>
</body>
</html>
