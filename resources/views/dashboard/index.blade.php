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
            --bg: #07111f;
            --bg-soft: #0d1a2f;
            --surface: rgba(9, 18, 34, 0.78);
            --surface-strong: rgba(14, 27, 48, 0.96);
            --panel: rgba(255, 255, 255, 0.04);
            --text: #f8fbff;
            --muted: #8ea5c7;
            --border: rgba(255, 255, 255, 0.08);
            --shadow: 0 24px 80px rgba(0, 0, 0, 0.34);
            --shadow-soft: 0 14px 32px rgba(0, 0, 0, 0.22);
            --primary: #ffb347;
            --primary-2: #ffd56a;
            --primary-3: #48d6a8;
            --danger: #ff7d7d;
            --ok: #63e6c0;
            --radius-xl: 30px;
            --radius-lg: 22px;
            --radius-md: 16px;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            min-height: 100%;
        }

        body {
            font-family: 'Instrument Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 15% 10%, rgba(255, 179, 71, 0.26), transparent 22%),
                radial-gradient(circle at 88% 18%, rgba(72, 214, 168, 0.18), transparent 24%),
                radial-gradient(circle at 50% 100%, rgba(99, 230, 192, 0.12), transparent 20%),
                linear-gradient(180deg, #06101d 0%, #0b1730 55%, #07111f 100%);
        }

        .app {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            padding: 28px 16px 42px;
        }

        .app::before,
        .app::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            filter: blur(18px);
            pointer-events: none;
            opacity: 0.55;
        }

        .app::before {
            width: 260px;
            height: 260px;
            background: rgba(255, 179, 71, 0.20);
            top: -90px;
            right: -80px;
        }

        .app::after {
            width: 280px;
            height: 280px;
            background: rgba(72, 214, 168, 0.12);
            left: -80px;
            bottom: 40px;
        }

        .shell {
            position: relative;
            z-index: 1;
            max-width: 1440px;
            margin: 0 auto;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.45fr 0.95fr;
            gap: 18px;
            align-items: stretch;
            margin-bottom: 18px;
        }

        .hero-card,
        .sidebar-card,
        .panel {
            background: linear-gradient(180deg, rgba(13, 26, 47, 0.84), rgba(8, 16, 30, 0.94));
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
        }

        .hero-card {
            padding: 26px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-radius: 999px;
            color: #1c1400;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(2.2rem, 5vw, 4.6rem);
            line-height: 0.98;
            letter-spacing: -0.07em;
        }

        .hero p {
            margin: 14px 0 0;
            max-width: 68ch;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.75;
        }

        .hero-highlights {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            color: var(--text);
            font-size: 0.88rem;
        }

        .hero-metrics {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .hero-metric {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
        }

        .hero-metric small {
            display: block;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.72rem;
            margin-bottom: 8px;
        }

        .hero-metric strong {
            font-size: 1.3rem;
            letter-spacing: -0.04em;
        }

        .sidebar-card {
            padding: 18px;
            display: grid;
            gap: 14px;
        }

        .site-card {
            padding: 18px;
            border-radius: 22px;
            background:
                linear-gradient(135deg, rgba(255, 179, 71, 0.16), rgba(72, 214, 168, 0.08)),
                rgba(255,255,255,0.04);
            border: 1px solid var(--border);
        }

        .site-card h2 {
            margin: 0;
            font-size: 1.05rem;
            letter-spacing: -0.03em;
        }

        .site-card p {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .control-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            align-items: end;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field label {
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .field input {
            width: 100%;
            border: 1px solid rgba(255,255,255,0.10);
            background: rgba(255,255,255,0.06);
            color: var(--text);
            border-radius: 14px;
            padding: 13px 14px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .field input:focus {
            border-color: rgba(255, 213, 106, 0.95);
            box-shadow: 0 0 0 4px rgba(255, 213, 106, 0.14);
        }

        .button-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            border: 0;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 800;
            padding: 13px 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-primary {
            color: #1f1604;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            box-shadow: 0 16px 28px rgba(255, 179, 71, 0.24);
        }

        .btn-secondary {
            color: var(--text);
            background: rgba(255,255,255,0.07);
            border: 1px solid var(--border);
        }

        .btn-ghost {
            color: #bfeedd;
            background: rgba(72, 214, 168, 0.10);
            border: 1px solid rgba(72, 214, 168, 0.18);
        }

        .page-grid {
            display: grid;
            gap: 18px;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 14px;
        }

        .kpi-card {
            grid-column: span 2;
            padding: 18px;
            border-radius: var(--radius-lg);
            background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
            border: 1px solid var(--border);
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .kpi-card::after {
            content: '';
            position: absolute;
            inset: auto -16px -28px auto;
            width: 96px;
            height: 96px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(255, 213, 106, 0.20), transparent 70%);
        }

        .kpi-label {
            margin: 0;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .kpi-value {
            margin: 12px 0 6px;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            font-weight: 900;
            letter-spacing: -0.06em;
        }

        .kpi-unit {
            color: var(--muted);
            font-size: 0.88rem;
        }

        .section-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 18px;
        }

        .panel {
            padding: 22px;
        }

        .panel-title {
            margin: 0 0 16px;
            font-size: 1.15rem;
            letter-spacing: -0.04em;
        }

        .panel-subtitle {
            margin: -8px 0 16px;
            color: var(--muted);
            line-height: 1.7;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 14px;
        }

        .chart-card {
            grid-column: span 6;
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
            border: 1px solid var(--border);
            padding: 18px;
            min-height: 360px;
        }

        .chart-card h3 {
            margin: 0 0 12px;
            font-size: 1rem;
        }

        .chart-shell {
            position: relative;
            height: 290px;
        }

        .chart-shell.tall { height: 320px; }

        .chart-shell canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .battery-card {
            display: grid;
            gap: 14px;
        }

        .battery-meter {
            position: relative;
            height: 18px;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .battery-meter > span {
            display: block;
            height: 100%;
            width: 0%;
            border-radius: inherit;
            background: linear-gradient(90deg, #63e6c0, #ffd56a, #ffb347);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .info-item {
            padding: 14px;
            border-radius: 18px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
        }

        .info-item span {
            display: block;
            color: var(--muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
        }

        .info-item strong {
            font-size: 1.1rem;
        }

        .recommendation-list {
            display: grid;
            gap: 12px;
        }

        .recommendation-item {
            display: flex;
            gap: 12px;
            align-items: start;
            padding: 14px;
            border-radius: 18px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
        }

        .recommendation-icon {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            color: #1b1403;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            flex: 0 0 auto;
        }

        .recommendation-item h4 {
            margin: 0 0 6px;
            font-size: 0.98rem;
        }

        .recommendation-item p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 0.92rem;
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
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.05);
            color: var(--text);
            font-size: 0.82rem;
        }

        .alert-list {
            display: grid;
            gap: 10px;
        }

        .alert-item {
            padding: 14px;
            border-radius: 18px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.04);
        }

        .alert-item.critical { border-left: 4px solid var(--danger); }
        .alert-item.warning { border-left: 4px solid #ffd56a; }
        .alert-item.success { border-left: 4px solid var(--ok); }

        .alert-item strong {
            display: block;
            margin-bottom: 6px;
        }

        .alert-item p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 0.92rem;
        }

        .table-panel {
            overflow: hidden;
            padding: 0;
        }

        .citations-panel {
            margin-top: 4px;
            padding: 22px;
        }

        .citations-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 14px;
        }

        .citation-card {
            grid-column: span 6;
            padding: 16px;
            border-radius: 18px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.04);
        }

        .citation-card h3 {
            margin: 0 0 10px;
            font-size: 0.98rem;
        }

        .citation-list {
            display: grid;
            gap: 10px;
        }

        .citation-list a,
        .citation-list span {
            color: #d8e6ff;
            line-height: 1.55;
            font-size: 0.93rem;
        }

        .citation-list a {
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .citation-note {
            margin-top: 12px;
            color: var(--muted);
            font-size: 0.88rem;
            line-height: 1.65;
        }

        .table-head {
            padding: 22px 22px 0;
        }

        .table-head p {
            color: var(--muted);
            margin: 0;
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
            text-align: left;
            padding: 16px 22px;
            color: var(--muted);
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-bottom: 1px solid var(--border);
        }

        .data-table tbody td {
            padding: 15px 22px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: var(--text);
        }

        .data-table tbody tr:hover {
            background: rgba(255,255,255,0.03);
        }

        .loading {
            display: none;
            margin: 18px 0;
            text-align: center;
            color: var(--muted);
        }

        .loading.active { display: block; }

        .spinner {
            width: 42px;
            height: 42px;
            margin: 0 auto 12px;
            border-radius: 50%;
            border: 3px solid rgba(255, 213, 106, 0.18);
            border-top-color: var(--primary);
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .error-message {
            display: none;
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(255, 125, 125, 0.12);
            border: 1px solid rgba(255, 125, 125, 0.18);
            color: #ffd0d0;
        }

        .error-message.active { display: block; }

        .stack {
            display: grid;
            gap: 18px;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1120px) {
            .hero,
            .section-grid {
                grid-template-columns: 1fr;
            }

            .kpi-card {
                grid-column: span 4;
            }

            .chart-card {
                grid-column: span 12;
            }
        }

        @media (max-width: 760px) {
            .app { padding: 16px 12px 28px; }
            .hero-card, .sidebar-card, .panel { border-radius: 22px; }
            .hero-metrics, .control-grid, .info-grid { grid-template-columns: 1fr; }
            .kpi-card { grid-column: span 12; }
            .button-row { flex-direction: column; }
            .chart-shell { height: 260px; }
            .chart-shell.tall { height: 290px; }
            .table-head, .data-table thead th, .data-table tbody td { padding-left: 16px; padding-right: 16px; }
        }
    </style>
</head>
<body>
    <main class="app">
        <div class="shell">
            <section class="hero">
                <article class="hero-card" data-aos="fade-up">
                    <div class="eyebrow">
                        <i class="fas fa-sun"></i>
                        SOLAR AI DASHBOARD
                    </div>
                    <h1>Agente Inteligente de Optimización Energética para Riohacha</h1>
                    <p>
                        Un tablero para analizar radiación solar histórica, estimar generación fotovoltaica, detectar picos de demanda y entregar recomendaciones accionables para empresas, entidades y comunidades de Riohacha.
                    </p>
                    <div class="hero-highlights">
                        <span class="chip"><i class="fas fa-bolt"></i> Radiación promedio objetivo: 7.0 kWh/m2/día</span>
                        <span class="chip"><i class="fas fa-sack-dollar"></i> Tarifa base: 943 COP/kWh</span>
                        <span class="chip"><i class="fas fa-battery-three-quarters"></i> Respaldo estimado con baterías</span>
                    </div>
                    <div class="hero-metrics">
                        <div class="hero-metric">
                            <small>Potencial solar</small>
                            <strong>Riohacha lidera</strong>
                        </div>
                        <div class="hero-metric">
                            <small>AI insight</small>
                            <strong>Recomendaciones diarias</strong>
                        </div>
                        <div class="hero-metric">
                            <small>Impacto</small>
                            <strong>Menos costo y más estabilidad</strong>
                        </div>
                    </div>
                </article>

                <aside class="sidebar-card" data-aos="fade-up">
                    <div class="site-card">
                        <h2>Site control</h2>
                        <p>Usa Riohacha como base o ajusta el punto para comparar otros escenarios energéticos.</p>
                    </div>
                    <div class="control-grid">
                        <div class="field">
                            <label for="latitude">Latitud</label>
                            <input type="number" id="latitude" step="0.0001" placeholder="11.5444">
                        </div>
                        <div class="field">
                            <label for="longitude">Longitud</label>
                            <input type="number" id="longitude" step="0.0001" placeholder="-72.9070">
                        </div>
                    </div>
                    <div class="button-row">
                        <button type="button" class="btn btn-secondary" onclick="setRiohachaSite()">
                            <i class="fas fa-location-dot"></i>
                            Cargar Riohacha
                        </button>
                        <button type="button" class="btn btn-primary" onclick="loadDashboardData()">
                            <i class="fas fa-arrows-rotate"></i>
                            Actualizar análisis
                        </button>
                    </div>
                    <div class="tag-row">
                        <span class="tag"><i class="fas fa-city"></i> Riohacha, La Guajira</span>
                        <span class="tag"><i class="fas fa-solar-panel"></i> Fotovoltaica</span>
                        <span class="tag"><i class="fas fa-shield-halved"></i> Respaldo inteligente</span>
                    </div>
                </aside>
            </section>

            <div id="loading" class="loading">
                <div class="spinner"></div>
                <p>Analizando radiación, consumo estimado y recomendaciones...</p>
            </div>

            <div id="errorMessage" class="error-message">
                <i class="fas fa-triangle-exclamation"></i>
                <span id="errorText"></span>
            </div>

            <div id="dashboardContent" class="page-grid" style="display:none;">
                <section class="kpi-grid" id="kpiGrid"></section>

                <section class="section-grid">
                    <article class="panel" data-aos="fade-up">
                        <h2 class="panel-title">Estado solar actual</h2>
                        <p class="panel-subtitle">Radiación solar, clima de soporte y generación estimada para los últimos 30 días.</p>
                        <div class="charts-grid">
                            <div class="chart-card">
                                <h3><i class="fas fa-sun"></i> Radiación solar diaria</h3>
                                <div class="chart-shell">
                                    <canvas id="radiationChart"></canvas>
                                </div>
                            </div>
                            <div class="chart-card">
                                <h3><i class="fas fa-bolt"></i> Generación vs demanda</h3>
                                <div class="chart-shell">
                                    <canvas id="energyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </article>

                    <aside class="panel battery-card" data-aos="fade-up">
                        <h2 class="panel-title">Estado de baterías</h2>
                        <p class="panel-subtitle">Estimación operativa basada en la energía disponible, la carga crítica y el potencial solar de hoy.</p>
                        <div class="battery-meter" aria-label="Nivel de batería">
                            <span id="batteryMeterFill"></span>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <span>Carga estimada</span>
                                <strong id="batteryChargeText">--</strong>
                            </div>
                            <div class="info-item">
                                <span>Autonomía</span>
                                <strong id="batteryAutonomyText">--</strong>
                            </div>
                            <div class="info-item">
                                <span>Uso recomendado</span>
                                <strong id="batteryUsageText">--</strong>
                            </div>
                            <div class="info-item">
                                <span>Ventana solar</span>
                                <strong id="solarWindowText">--</strong>
                            </div>
                        </div>
                    </aside>
                </section>

                <section class="section-grid">
                    <article class="panel" data-aos="fade-up">
                        <h2 class="panel-title">Analítica avanzada</h2>
                        <p class="panel-subtitle">Temperatura y humedad ayudan a entender estabilidad térmica, mantenimiento y productividad solar.</p>
                        <div class="charts-grid">
                            <div class="chart-card">
                                <h3><i class="fas fa-temperature-three-quarters"></i> Clima de soporte</h3>
                                <div class="chart-shell tall">
                                    <canvas id="weatherChart"></canvas>
                                </div>
                            </div>
                            <div class="chart-card">
                                <h3><i class="fas fa-chart-line"></i> Potencial de ahorro</h3>
                                <div class="chart-shell tall">
                                    <canvas id="savingsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </article>

                    <aside class="panel" data-aos="fade-up">
                        <h2 class="panel-title">Agente Solar</h2>
                        <p class="panel-subtitle">Recomendaciones automáticas para reducir OpEx, mover cargas y usar mejor paneles y baterías.</p>
                        <div id="recommendationList" class="recommendation-list"></div>
                        <div class="tag-row" id="agentTags"></div>
                    </aside>
                </section>

                <section class="section-grid">
                    <article class="panel" data-aos="fade-up">
                        <h2 class="panel-title">Alertas inteligentes</h2>
                        <p class="panel-subtitle">Señales para baja radiación, riesgo de sobreconsumo y oportunidades de ahorro.</p>
                        <div id="alertList" class="alert-list"></div>
                    </article>

                    <article class="panel" data-aos="fade-up">
                        <h2 class="panel-title">Resumen de impacto</h2>
                        <p class="panel-subtitle">Indicadores de negocio que muestran el valor económico y ambiental de la operación solar.</p>
                        <div class="info-grid">
                            <div class="info-item">
                                <span>Ahorro mensual</span>
                                <strong id="monthlySavingsText">--</strong>
                            </div>
                            <div class="info-item">
                                <span>CO2 evitado</span>
                                <strong id="co2AvoidedText">--</strong>
                            </div>
                            <div class="info-item">
                                <span>Cobertura</span>
                                <strong id="coverageText">--</strong>
                            </div>
                            <div class="info-item">
                                <span>Solar score</span>
                                <strong id="solarScoreText">--</strong>
                            </div>
                        </div>
                    </article>
                </section>

                <section class="panel table-panel" data-aos="fade-up">
                    <div class="table-head">
                        <h2 class="panel-title" style="margin-bottom:8px;">Series histórica</h2>
                        <p>Radiación, generación estimada, ahorro y variables climáticas por día.</p>
                    </div>
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Radiación</th>
                                    <th>Generación</th>
                                    <th>Temp.</th>
                                    <th>Humedad</th>
                                    <th>Ahorro</th>
                                    <th>CO2 evitado</th>
                                </tr>
                            </thead>
                            <tbody id="dataTableBody"></tbody>
                        </table>
                    </div>
                </section>

                <section class="panel citations-panel" data-aos="fade-up">
                    <h2 class="panel-title">Citas y supuestos</h2>
                    <p class="panel-subtitle">
                        Esta vista mezcla datos reales descargados desde internet con parámetros operativos definidos para el modelo.
                        Lo que viene de internet se cita aquí; lo que está “quemado” se marca como supuesto ajustable del sistema.
                    </p>
                    <div class="citations-grid">
                        <article class="citation-card">
                            <h3>Fuentes reales usadas por el tablero</h3>
                            <div class="citation-list">
                                <a href="https://power.larc.nasa.gov/" target="_blank" rel="noopener noreferrer">
                                    NASA POWER API, datos diarios de radiación y variables meteorológicas para la ubicación consultada.
                                </a>
                                <span>Campos usados: `ALLSKY_SFC_SW_DWN`, `CLRSKY_SFC_SW_DWN`, `T2M`, `RH2M`, `WS2M`.</span>
                            </div>
                        </article>
                        <article class="citation-card">
                            <h3>Contexto del reto y cifras de negocio</h3>
                            <div class="citation-list">
                                <span>Documento del reto SOLAR AI DASHBOARD: radiación promedio de referencia en Riohacha, tarifa de 943 COP/kWh, 60 horas/año de interrupciones y pérdidas anuales estimadas de 18.700 millones COP.</span>
                            </div>
                        </article>
                        <article class="citation-card">
                            <h3>Parámetros quemados del modelo</h3>
                            <div class="citation-list">
                                <span>Riohacha como sitio base: lat 11.5444, lon -72.9069.</span>
                                <span>Modelo FV: 20 kWp, batería de 40 kWh, carga crítica 5.5 kW, consumo diario base 58 kWh, performance ratio 0.78, autoconsumo 0.82, factor CO2 0.42 kg/kWh.</span>
                            </div>
                        </article>
                        <article class="citation-card">
                            <h3>Notas de interpretación</h3>
                            <div class="citation-list">
                                <span>Las recomendaciones del Agente Solar son heurísticas del backend por ahora; todavía no usan un modelo de lenguaje.</span>
                                <span>Los valores de generación, ahorro y cobertura son estimaciones para apoyo de decisión, no mediciones eléctricas certificadas.</span>
                            </div>
                        </article>
                    </div>
                    <p class="citation-note">
                        Si quieres, en la siguiente iteración puedo convertir estos supuestos en variables editables desde una pantalla de configuración o guardarlos por empresa/cliente.
                    </p>
                </section>
            </div>
        </div>
    </main>

    <script>
        let charts = {};
        let solarData = [];
        let recommendations = [];
        let alerts = [];
        let statisticsData = {};
        const defaultSite = {
            name: 'Riohacha, La Guajira',
            latitude: 11.5444,
            longitude: -72.9069
        };

        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 650,
                easing: 'ease-out-cubic',
                once: true
            });

            if (!document.getElementById('latitude').value) {
                setRiohachaSite(false);
            }

            loadDashboardData();
        });

        function setRiohachaSite(autoLoad = true) {
            document.getElementById('latitude').value = Number(defaultSite.latitude).toFixed(4);
            document.getElementById('longitude').value = Number(defaultSite.longitude).toFixed(4);

            if (autoLoad) {
                loadDashboardData();
            }
        }

        async function loadDashboardData() {
            const latitude = document.getElementById('latitude').value || defaultSite.latitude;
            const longitude = document.getElementById('longitude').value || defaultSite.longitude;

            showLoading(true);
            hideError();

            try {
                const response = await fetch(`/api/solar-data?latitude=${encodeURIComponent(latitude)}&longitude=${encodeURIComponent(longitude)}`);
                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message || 'No fue posible cargar el tablero solar.');
                }

                solarData = Array.isArray(result.data) ? result.data : [];
                statisticsData = result.statistics || {};
                recommendations = Array.isArray(result.recommendations) ? result.recommendations : [];
                alerts = Array.isArray(result.alerts) ? result.alerts : [];

                renderKPIs(result.site || {});
                renderCharts();
                renderBatteryPanel();
                renderRecommendations();
                renderAlerts();
                renderTable();
                renderImpactSummary();

                showLoading(false);
                document.getElementById('dashboardContent').style.display = 'grid';
            } catch (error) {
                showLoading(false);
                document.getElementById('dashboardContent').style.display = 'none';
                showError(error.message);
            }
        }

        function renderKPIs(site) {
            const container = document.getElementById('kpiGrid');
            container.innerHTML = '';

            const kpis = [
                { label: 'Radiación promedio', value: statisticsData.avg_radiation ?? 0, unit: 'kWh/m2/día', icon: 'fa-sun', tone: 'primary' },
                { label: 'Generación estimada', value: statisticsData.total_generation_kwh ?? 0, unit: 'kWh/mes', icon: 'fa-bolt', tone: 'ok' },
                { label: 'Ahorro estimado', value: statisticsData.estimated_monthly_savings_cop ?? 0, unit: 'COP/mes', icon: 'fa-sack-dollar', tone: 'primary' },
                { label: 'Cobertura', value: statisticsData.coverage_ratio ?? 0, unit: '%', icon: 'fa-chart-pie', tone: 'ok' },
                { label: 'Autonomía', value: statisticsData.battery_autonomy_hours ?? 0, unit: 'horas', icon: 'fa-battery-full', tone: 'primary' },
                { label: 'Solar score', value: statisticsData.solar_score ?? 0, unit: 'puntos', icon: 'fa-solar-panel', tone: 'primary' },
            ];

            kpis.forEach((item, index) => {
                const card = document.createElement('article');
                card.className = 'kpi-card';
                card.setAttribute('data-aos', 'fade-up');
                card.innerHTML = `
                    <p class="kpi-label"><i class="fas ${item.icon}"></i> ${item.label}</p>
                    <div class="kpi-value">${formatNumber(item.value)}</div>
                    <div class="kpi-unit">${item.unit}</div>
                `;
                container.appendChild(card);

                setTimeout(() => {
                    const targetValue = Number(item.value ?? 0);
                    const counter = new CountUp(card.querySelector('.kpi-value'), targetValue, {
                        duration: 1.6,
                        decimalPlaces: targetValue % 1 === 0 ? 0 : 1
                    });
                    counter.start();
                }, index * 80);
            });
        }

        function renderCharts() {
            if (!solarData.length) return;

            const dates = solarData.map(row => row.date);
            const radiation = solarData.map(row => row.radiation);
            const clearSky = solarData.map(row => row.clear_sky_radiation);
            const generation = solarData.map(row => row.estimated_generation_kwh);
            const demand = solarData.map(row => row.estimated_demand_kwh);
            const temperature = solarData.map(row => row.temperature);
            const humidity = solarData.map(row => row.humidity);
            const savings = solarData.map(row => row.estimated_savings_cop);

            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        labels: { color: '#dbe7ff' }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(3, 10, 20, 0.96)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.06)' },
                        ticks: { color: '#90a6c8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#90a6c8', maxTicksLimit: 6 }
                    }
                }
            };

            if (charts.radiation) charts.radiation.destroy();
            charts.radiation = new Chart(document.getElementById('radiationChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Radiación',
                            data: radiation,
                            borderColor: '#ffd56a',
                            backgroundColor: 'rgba(255, 213, 106, 0.18)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3
                        },
                        {
                            label: 'Cielo despejado',
                            data: clearSky,
                            borderColor: '#63e6c0',
                            backgroundColor: 'rgba(99, 230, 192, 0.10)',
                            fill: false,
                            tension: 0.35,
                            pointRadius: 2
                        }
                    ]
                },
                options: baseOptions
            });

            if (charts.energy) charts.energy.destroy();
            charts.energy = new Chart(document.getElementById('energyChart'), {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Generación',
                            data: generation,
                            backgroundColor: '#ffb347',
                            borderRadius: 10,
                            borderSkipped: false
                        },
                        {
                            label: 'Demanda base',
                            data: demand,
                            backgroundColor: '#63e6c0',
                            borderRadius: 10,
                            borderSkipped: false
                        }
                    ]
                },
                options: baseOptions
            });

            if (charts.weather) charts.weather.destroy();
            charts.weather = new Chart(document.getElementById('weatherChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Temperatura',
                            data: temperature,
                            borderColor: '#ff7d7d',
                            backgroundColor: 'rgba(255, 125, 125, 0.14)',
                            fill: true,
                            tension: 0.32
                        },
                        {
                            label: 'Humedad',
                            data: humidity,
                            borderColor: '#7aa8ff',
                            backgroundColor: 'rgba(122, 168, 255, 0.12)',
                            fill: false,
                            tension: 0.32
                        }
                    ]
                },
                options: baseOptions
            });

            if (charts.savings) charts.savings.destroy();
            charts.savings = new Chart(document.getElementById('savingsChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Ahorro COP',
                        data: savings,
                        borderColor: '#63e6c0',
                        backgroundColor: 'rgba(99, 230, 192, 0.18)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 2
                    }]
                },
                options: baseOptions
            });
        }

        function renderBatteryPanel() {
            const fill = document.getElementById('batteryMeterFill');
            const charge = Number(statisticsData.battery_charge_percent ?? 0);
            fill.style.width = `${Math.max(0, Math.min(100, charge))}%`;

            document.getElementById('batteryChargeText').textContent = `${formatNumber(charge)}%`;
            document.getElementById('batteryAutonomyText').textContent = `${formatNumber(statisticsData.battery_autonomy_hours ?? 0)} h`;
            document.getElementById('batteryUsageText').textContent = statisticsData.battery_usage_text || 'Usar cargas medias';
            document.getElementById('solarWindowText').textContent = statisticsData.solar_window || '10:00 - 14:00';
        }

        function renderRecommendations() {
            const container = document.getElementById('recommendationList');
            const tags = document.getElementById('agentTags');
            container.innerHTML = '';
            tags.innerHTML = '';

            recommendations.forEach((item) => {
                const card = document.createElement('article');
                card.className = 'recommendation-item';
                card.innerHTML = `
                    <div class="recommendation-icon"><i class="fas ${item.icon || 'fa-lightbulb'}"></i></div>
                    <div>
                        <h4>${item.title}</h4>
                        <p>${item.message}</p>
                    </div>
                `;
                container.appendChild(card);
            });

            const tagValues = statisticsData.tags || [];
            tagValues.forEach((tag) => {
                const el = document.createElement('span');
                el.className = 'tag';
                el.textContent = tag;
                tags.appendChild(el);
            });
        }

        function renderAlerts() {
            const container = document.getElementById('alertList');
            container.innerHTML = '';

            alerts.forEach((item) => {
                const card = document.createElement('article');
                card.className = `alert-item ${item.level || 'warning'}`;
                card.innerHTML = `
                    <strong>${item.title}</strong>
                    <p>${item.message}</p>
                `;
                container.appendChild(card);
            });
        }

        function renderImpactSummary() {
            document.getElementById('monthlySavingsText').textContent = formatCop(statisticsData.estimated_monthly_savings_cop ?? 0);
            document.getElementById('co2AvoidedText').textContent = `${formatNumber(statisticsData.co2_avoided_kg ?? 0)} kg`;
            document.getElementById('coverageText').textContent = `${formatNumber(statisticsData.coverage_ratio ?? 0)}%`;
            document.getElementById('solarScoreText').textContent = `${formatNumber(statisticsData.solar_score ?? 0)}/100`;
        }

        function renderTable() {
            const tbody = document.getElementById('dataTableBody');
            tbody.innerHTML = '';

            solarData.forEach((row) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.date ?? '--'}</td>
                    <td>${formatNumber(row.radiation ?? 0)} kWh/m2</td>
                    <td>${formatNumber(row.estimated_generation_kwh ?? 0)} kWh</td>
                    <td>${formatNumber(row.temperature ?? 0)} °C</td>
                    <td>${formatNumber(row.humidity ?? 0)}%</td>
                    <td>${formatCop(row.estimated_savings_cop ?? 0)}</td>
                    <td>${formatNumber(row.co2_avoided_kg ?? 0)} kg</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function showLoading(show) {
            document.getElementById('loading').classList.toggle('active', show);
        }

        function showError(message) {
            const element = document.getElementById('errorMessage');
            document.getElementById('errorText').textContent = message;
            element.classList.add('active');
        }

        function hideError() {
            document.getElementById('errorMessage').classList.remove('active');
        }

        function formatNumber(value) {
            const number = Number(value ?? 0);
            return new Intl.NumberFormat('es-CO', {
                maximumFractionDigits: number % 1 === 0 ? 0 : 1
            }).format(number);
        }

        function formatCop(value) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                maximumFractionDigits: 0
            }).format(Number(value ?? 0));
        }
    </script>
</body>
</html>
