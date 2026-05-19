<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Climate Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #f5f7fb;
            --surface: rgba(255, 255, 255, 0.82);
            --surface-strong: #ffffff;
            --text: #24324a;
            --muted: #667085;
            --border: rgba(148, 163, 184, 0.22);
            --shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
            --shadow-soft: 0 12px 32px rgba(15, 23, 42, 0.06);
            --primary: #8fa8ff;
            --primary-2: #77d6c2;
            --primary-3: #f7c98f;
            --danger: #ef7d7d;
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            min-height: 100%;
        }

        body {
            font-family: 'Instrument Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(143, 168, 255, 0.35), transparent 28%),
                radial-gradient(circle at 85% 20%, rgba(119, 214, 194, 0.25), transparent 24%),
                radial-gradient(circle at 50% 100%, rgba(247, 201, 143, 0.22), transparent 26%),
                var(--bg);
        }

        .app {
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            padding: 28px 16px 40px;
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
            width: 220px;
            height: 220px;
            background: rgba(143, 168, 255, 0.22);
            top: -40px;
            right: -30px;
        }

        .app::after {
            width: 260px;
            height: 260px;
            background: rgba(119, 214, 194, 0.18);
            left: -100px;
            bottom: 60px;
        }

        .shell {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
        }

        .hero {
            background: linear-gradient(135deg, rgba(255,255,255,0.82), rgba(255,255,255,0.66));
            border: 1px solid rgba(255,255,255,0.7);
            backdrop-filter: blur(18px);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
            padding: 24px;
            margin-bottom: 18px;
        }

        .hero-top {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(143, 168, 255, 0.12);
            color: #5c6dc5;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            margin-bottom: 14px;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3.2rem);
            line-height: 1.02;
            letter-spacing: -0.05em;
        }

        .hero p {
            margin: 12px 0 0;
            max-width: 60ch;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.7;
        }

        .control-panel {
            min-width: min(100%, 480px);
            background: rgba(255,255,255,0.75);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 16px;
            box-shadow: var(--shadow-soft);
        }

        .control-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            align-items: end;
        }

        .action-group {
            display: flex;
            gap: 10px;
            grid-column: span 2;
        }

        .action-group .btn {
            flex: 1;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field label {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--muted);
        }

        .field input {
            width: 100%;
            border: 1px solid rgba(148, 163, 184, 0.28);
            background: rgba(255,255,255,0.9);
            color: var(--text);
            border-radius: 14px;
            padding: 13px 14px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .field input:focus {
            border-color: rgba(143, 168, 255, 0.9);
            box-shadow: 0 0 0 4px rgba(143, 168, 255, 0.14);
        }

        .btn {
            border: 0;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 700;
            padding: 13px 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            color: white;
            background: linear-gradient(135deg, #8fa8ff, #77d6c2);
            box-shadow: 0 14px 26px rgba(143, 168, 255, 0.25);
        }

        .btn-secondary {
            color: var(--text);
            background: rgba(255,255,255,0.88);
            border: 1px solid var(--border);
        }

        .status-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 10px 14px;
            font-size: 0.88rem;
            background: rgba(255,255,255,0.72);
            border: 1px solid var(--border);
            color: var(--muted);
        }

        .loading {
            display: none;
            margin: 20px 0;
            text-align: center;
            color: var(--muted);
        }

        .loading.active {
            display: block;
        }

        .spinner {
            width: 42px;
            height: 42px;
            margin: 0 auto 12px;
            border-radius: 50%;
            border: 3px solid rgba(143, 168, 255, 0.16);
            border-top-color: #8fa8ff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .error-message {
            display: none;
            margin: 18px 0 0;
            padding: 14px 16px;
            border-radius: 16px;
            color: #8a2a2a;
            background: rgba(239, 125, 125, 0.12);
            border: 1px solid rgba(239, 125, 125, 0.22);
        }

        .error-message.active {
            display: block;
        }

        .stack {
            display: grid;
            gap: 18px;
            margin-top: 18px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 16px;
        }

        .stat-card {
            grid-column: span 3;
            background: rgba(255,255,255,0.8);
            border: 1px solid rgba(255,255,255,0.75);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 18px;
            animation: fadeUp 0.55s ease both;
        }

        .stat-card.temp { border-top: 4px solid #f3b46b; }
        .stat-card.humidity { border-top: 4px solid #76b6ff; }
        .stat-card.wind { border-top: 4px solid #76d6c2; }
        .stat-card.rain { border-top: 4px solid #8fa8ff; }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-title {
            margin: 0;
            color: var(--muted);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .stat-icon {
            font-size: 1.2rem;
            color: #7d8fb7;
        }

        .stat-value {
            margin: 14px 0 6px;
            font-size: clamp(1.9rem, 3vw, 2.6rem);
            font-weight: 800;
            letter-spacing: -0.06em;
            color: var(--text);
        }

        .stat-unit {
            color: var(--muted);
            font-size: 0.9rem;
        }

        .meta-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,0.72);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-soft);
        }

        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 999px;
            background: rgba(143, 168, 255, 0.09);
            color: var(--text);
            font-size: 0.88rem;
        }

        .panel {
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
            padding: 22px;
        }

        .panel-title {
            margin: 0 0 16px;
            font-size: 1.2rem;
            letter-spacing: -0.03em;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 16px;
        }

        .chart-card {
            grid-column: span 6;
            background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(250,251,255,0.96));
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 22px;
            padding: 18px;
            min-height: 420px;
            box-shadow: var(--shadow-soft);
        }

        .chart-card h3 {
            margin: 0 0 14px;
            font-size: 1rem;
            color: var(--text);
        }

        .chart-shell {
            position: relative;
            height: 340px;
        }

        .chart-shell.tall {
            height: 390px;
        }

        .chart-shell canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .table-panel {
            overflow: hidden;
            padding: 0;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 0 0 var(--radius-xl) var(--radius-xl);
        }

        .table-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 22px 22px 0;
        }

        .table-head p {
            margin: 0;
            color: var(--muted);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 720px;
        }

        .data-table thead th {
            text-align: left;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            padding: 16px 22px;
            border-bottom: 1px solid var(--border);
        }

        .data-table tbody td {
            padding: 16px 22px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
            color: var(--text);
        }

        .data-table tbody tr:hover {
            background: rgba(143, 168, 255, 0.05);
        }

        .empty-state {
            text-align: center;
            padding: 42px 16px;
            color: var(--muted);
        }

        .empty-state-icon {
            font-size: 2.8rem;
            opacity: 0.5;
            margin-bottom: 12px;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1100px) {
            .stat-card {
                grid-column: span 6;
            }

            .chart-card {
                grid-column: span 12;
            }
        }

        @media (max-width: 720px) {
            .app {
                padding: 16px 12px 28px;
            }

            .hero,
            .panel,
            .control-panel {
                border-radius: 20px;
            }

            .control-grid {
                grid-template-columns: 1fr;
            }

            .action-group {
                grid-column: auto;
                flex-direction: column;
            }

            .stat-card {
                grid-column: span 12;
            }

            .chart-shell {
                height: 300px;
            }

            .chart-shell.tall {
                height: 330px;
            }

            .panel,
            .table-head {
                padding-left: 16px;
                padding-right: 16px;
            }

            .data-table thead th,
            .data-table tbody td {
                padding-left: 16px;
                padding-right: 16px;
            }
        }
    </style>
</head>
<body>
    <main class="app">
        <div class="shell">
            <section class="hero" data-aos="fade-up">
                <div class="hero-top">
                    <div>
                        <div class="eyebrow">
                            <i class="fas fa-cloud-sun-rain"></i>
                            NASA POWER climate view
                        </div>
                        <h1>Climate dashboard</h1>
                        <p>Una vista suave y limpia para explorar temperatura, humedad, viento y precipitacion con datos diarios de NASA POWER.</p>
                        <div class="status-row">
                            <span class="chip"><i class="fas fa-sparkles"></i> Responsive</span>
                            <span class="chip"><i class="fas fa-chart-line"></i> Live charts</span>
                            <span class="chip"><i class="fas fa-table"></i> Detailed rows</span>
                        </div>
                    </div>

                    <div class="control-panel">
                        <div class="control-grid">
                            <div class="field">
                                <label for="latitude">Latitude</label>
                                <input type="number" id="latitude" step="0.0001" placeholder="Latitude">
                            </div>
                            <div class="field">
                                <label for="longitude">Longitude</label>
                                <input type="number" id="longitude" step="0.0001" placeholder="Longitude">
                            </div>
                            <div class="action-group">
                                <button type="button" class="btn btn-secondary" onclick="useMyLocation()">
                                    <i class="fas fa-location-crosshairs"></i>
                                    Use my location
                                </button>
                                <button type="button" class="btn btn-primary" onclick="loadWeatherData()">
                                    <i class="fas fa-sync-alt"></i>
                                    Refresh
                                </button>
                            </div>
                        </div>
                        <div id="metadata" class="meta-bar" style="display:none; margin-top: 14px;"></div>
                    </div>
                </div>

                <div id="errorMessage" class="error-message">
                    <i class="fas fa-triangle-exclamation"></i> <span id="errorText"></span>
                </div>
            </section>

            <div id="loading" class="loading">
                <div class="spinner"></div>
                <p>Loading climate data...</p>
            </div>

            <div id="statsContainer" style="display:none;" class="stack">
                <section class="stats-grid" id="statsGrid"></section>

                <section class="panel" data-aos="fade-up">
                    <h2 class="panel-title">Climate analysis</h2>
                    <div class="charts-grid">
                        <article class="chart-card">
                            <h3><i class="fas fa-temperature-half"></i> Temperature (°C)</h3>
                            <div class="chart-shell">
                                <canvas id="temperatureChart"></canvas>
                            </div>
                        </article>
                        <article class="chart-card">
                            <h3><i class="fas fa-droplet"></i> Humidity (%)</h3>
                            <div class="chart-shell">
                                <canvas id="humidityChart"></canvas>
                            </div>
                        </article>
                        <article class="chart-card">
                            <h3><i class="fas fa-wind"></i> Wind speed (m/s)</h3>
                            <div class="chart-shell tall">
                                <canvas id="windChart"></canvas>
                            </div>
                        </article>
                        <article class="chart-card">
                            <h3><i class="fas fa-cloud-rain"></i> Precipitation (mm)</h3>
                            <div class="chart-shell tall">
                                <canvas id="precipitationChart"></canvas>
                            </div>
                        </article>
                    </div>
                </section>

                <section class="panel table-panel" data-aos="fade-up">
                    <div class="table-head">
                        <div>
                            <h2 class="panel-title" style="margin-bottom:8px;">Data detail</h2>
                            <p>Daily values returned by the API.</p>
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Temperature (°C)</th>
                                    <th>Humidity (%)</th>
                                    <th>Wind (m/s)</th>
                                    <th>Precipitation (mm)</th>
                                </tr>
                            </thead>
                            <tbody id="dataTableBody"></tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div id="emptyState" style="display:none;" class="panel">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3>No data yet</h3>
                    <p>Press Refresh to load climate data.</p>
                </div>
            </div>
        </div>
    </main>

    <script>
        let charts = {};
        let weatherData = [];
        let statisticsData = {};

        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 650,
                easing: 'ease-out-cubic',
                once: true
            });
        });

        async function loadWeatherData() {
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;

            if (!latitude || !longitude) {
                showError('Please enter a valid latitude and longitude.');
                return;
            }

            showLoading(true);
            hideError();

            try {
                const response = await fetch(`/api/weather?latitude=${latitude}&longitude=${longitude}`);
                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message || 'Error loading data');
                }

                weatherData = Array.isArray(result.data) ? result.data : [];
                statisticsData = result.statistics || {};

                renderMeta(result.meta);
                renderStatistics();
                renderCharts();
                renderTable();

                showLoading(false);
                document.getElementById('statsContainer').style.display = 'grid';
                document.getElementById('emptyState').style.display = 'none';
            } catch (error) {
                showError(`Error: ${error.message}`);
                showLoading(false);
                document.getElementById('statsContainer').style.display = 'none';
                document.getElementById('emptyState').style.display = 'block';
            }
        }

        async function useMyLocation() {
            if (!navigator.geolocation) {
                showError('Tu navegador no soporta geolocalización.');
                return;
            }

            showLoading(true);
            hideError();

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const latitude = position.coords.latitude.toFixed(6);
                    const longitude = position.coords.longitude.toFixed(6);

                    document.getElementById('latitude').value = latitude;
                    document.getElementById('longitude').value = longitude;

                    await loadWeatherData();
                },
                () => {
                    showLoading(false);
                    showError('No se pudo obtener tu ubicación. Revisa los permisos del navegador.');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        function renderStatistics() {
            const container = document.getElementById('statsGrid');
            container.innerHTML = '';

            const stats = [
                { title: 'Avg Temperature', value: statisticsData.avg_temperature, unit: '°C', icon: 'fas fa-temperature-half', class: 'temp' },
                { title: 'Max Temperature', value: statisticsData.max_temperature, unit: '°C', icon: 'fas fa-arrow-up', class: 'temp' },
                { title: 'Min Temperature', value: statisticsData.min_temperature, unit: '°C', icon: 'fas fa-arrow-down', class: 'temp' },
                { title: 'Avg Humidity', value: statisticsData.avg_humidity, unit: '%', icon: 'fas fa-droplet', class: 'humidity' },
                { title: 'Avg Wind Speed', value: statisticsData.avg_wind_speed, unit: 'm/s', icon: 'fas fa-wind', class: 'wind' },
                { title: 'Total Precipitation', value: statisticsData.total_precipitation, unit: 'mm', icon: 'fas fa-cloud-rain', class: 'rain' }
            ];

            stats.forEach((stat, index) => {
                const card = document.createElement('article');
                card.className = `stat-card ${stat.class}`;
                card.setAttribute('data-aos', 'fade-up');
                card.innerHTML = `
                    <div class="stat-header">
                        <h3 class="stat-title">${stat.title}</h3>
                        <i class="fas ${stat.icon} stat-icon"></i>
                    </div>
                    <div class="stat-value">${Number(stat.value ?? 0).toFixed(Number(stat.value ?? 0) % 1 === 0 ? 0 : 1)}</div>
                    <div class="stat-unit">${stat.unit}</div>
                `;
                container.appendChild(card);

                setTimeout(() => {
                    const targetValue = Number(stat.value ?? 0);
                    const counter = new CountUp(card.querySelector('.stat-value'), targetValue, {
                        duration: 1.8,
                        decimalPlaces: targetValue % 1 === 0 ? 0 : 1
                    });
                    counter.start();
                }, index * 90);
            });
        }

        function renderCharts() {
            if (!weatherData.length) return;

            const dates = weatherData.map(d => d.date);
            const temperatures = weatherData.map(d => d.temperature);
            const humidities = weatherData.map(d => d.humidity);
            const windSpeeds = weatherData.map(d => d.wind_speed);
            const precipitations = weatherData.map(d => d.precipitation);

            const chartConfig = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 12
                    }
                },
                layout: {
                    padding: {
                        top: 8,
                        right: 8,
                        bottom: 18,
                        left: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.14)'
                        },
                        ticks: {
                            color: '#60708a'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#60708a',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 6
                        }
                    }
                }
            };

            if (charts.temperature) charts.temperature.destroy();
            charts.temperature = new Chart(document.getElementById('temperatureChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        data: temperatures,
                        borderColor: '#f3b46b',
                        backgroundColor: 'rgba(243, 180, 107, 0.14)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#f3b46b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: chartConfig
            });

            if (charts.humidity) charts.humidity.destroy();
            charts.humidity = new Chart(document.getElementById('humidityChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        data: humidities,
                        borderColor: '#76b6ff',
                        backgroundColor: 'rgba(118, 182, 255, 0.14)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#76b6ff',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: chartConfig
            });

            const tallBarOptions = {
                ...chartConfig,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.14)'
                        },
                        ticks: {
                            color: '#60708a'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#60708a',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 5
                        }
                    }
                }
            };

            if (charts.wind) charts.wind.destroy();
            charts.wind = new Chart(document.getElementById('windChart'), {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [{
                        data: windSpeeds,
                        backgroundColor: '#77d6c2',
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 26
                    }]
                },
                options: tallBarOptions
            });

            if (charts.precipitation) charts.precipitation.destroy();
            charts.precipitation = new Chart(document.getElementById('precipitationChart'), {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [{
                        data: precipitations,
                        backgroundColor: '#8fa8ff',
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 26
                    }]
                },
                options: tallBarOptions
            });
        }

        function renderTable() {
            const tbody = document.getElementById('dataTableBody');
            tbody.innerHTML = '';

            weatherData.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.date ?? '--'}</td>
                    <td>${row.temperature ?? '--'}°C</td>
                    <td>${row.humidity ?? '--'}%</td>
                    <td>${row.wind_speed ?? '--'} m/s</td>
                    <td>${row.precipitation ?? '--'} mm</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function renderMeta(meta) {
            const container = document.getElementById('metadata');

            if (!meta) {
                container.style.display = 'none';
                container.innerHTML = '';
                return;
            }

            container.innerHTML = `
                <span class="meta-item"><i class="fas fa-location-dot"></i> Lat ${meta.latitude}, Lon ${meta.longitude}</span>
                <span class="meta-item"><i class="fas fa-calendar-days"></i> ${meta.date_range?.start ?? '--'} to ${meta.date_range?.end ?? '--'}</span>
            `;
            container.style.display = 'flex';
        }

        function showLoading(show) {
            document.getElementById('loading').classList.toggle('active', show);
        }

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            document.getElementById('errorText').textContent = message;
            errorDiv.classList.add('active');
        }

        function hideError() {
            document.getElementById('errorMessage').classList.remove('active');
        }

        document.getElementById('latitude')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') loadWeatherData();
        });

        document.getElementById('longitude')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') loadWeatherData();
        });
    </script>
</body>
</html>
