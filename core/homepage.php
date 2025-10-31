<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StructureOne API</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #000;
            color: #fff;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            width: 100%;
        }
        
        header {
            padding: 30px 0;
            border-bottom: 1px solid #222;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo {
            width: 200px;
            height: 200px;
            object-fit: contain;
        }
        
        main {
            flex: 1;
            padding: 60px 0 40px;
        }
        
        .status-badge {
            display: inline-block;
            background: #00cc66;
            color: #000;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        h1 {
            font-size: 48px;
            margin-bottom: 15px;
            color: #fff;
            font-weight: 600;
        }
        
        .subtitle {
            font-size: 18px;
            color: #888;
            margin-bottom: 40px;
        }
        
        .info-section {
            background: #0a0a0a;
            border: 1px solid #222;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .section-header {
            background: #111;
            padding: 15px 20px;
            border-bottom: 1px solid #222;
            font-weight: 600;
            font-size: 14px;
            color: #ccc;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .section-content {
            padding: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            padding: 15px;
            background: #111;
            border: 1px solid #222;
            border-radius: 6px;
        }
        
        .info-item h3 {
            font-size: 12px;
            color: #888;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        
        .info-item p {
            font-size: 16px;
            color: #fff;
            font-weight: 600;
        }
        
        .usage-list {
            list-style: none;
            padding: 0;
        }
        
        .usage-list li {
            padding: 12px 15px;
            background: #111;
            border: 1px solid #222;
            border-radius: 6px;
            margin-bottom: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #ccc;
        }
        
        .usage-list li:last-child {
            margin-bottom: 0;
        }
        
        .usage-list .method {
            color: #00cc66;
            font-weight: 600;
            margin-right: 8px;
        }
        
        .usage-list .route {
            color: #61afef;
        }
        
        .versions-list {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .version-badge {
            background: #111;
            border: 1px solid #333;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            color: #888;
            font-family: 'Courier New', monospace;
        }
        
        footer {
            text-align: center;
            padding: 30px 20px;
            color: #666;
            border-top: 1px solid #222;
            font-size: 14px;
        }
        
        footer a {
            color: #61afef;
            text-decoration: none;
        }
        
        footer a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .logo { width: 50px; height: 50px; }
            h1 { font-size: 32px; }
            .subtitle { font-size: 16px; }
            .info-grid { grid-template-columns: 1fr; }
            main { padding: 40px 0 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo-container">
                <img src="/core/github_save/StructureOne.jpeg" alt="StructureOne" class="logo">
            </div>
        </header>
        
        <main>
            <span class="status-badge">● Système Opérationnel</span>
            <h1>StructureOne API</h1>
            <p class="subtitle">Framework PHP Moderne pour APIs RESTful</p>
            
            <!-- Informations système -->
            <div class="info-section">
                <div class="section-header">Informations Système</div>
                <div class="section-content">
                    <div class="info-grid">
                        <div class="info-item">
                            <h3>Version</h3>
                            <p><?php echo htmlspecialchars($systemVersion ?? '2.1.1+'); ?></p>
                        </div>
                        <div class="info-item">
                            <h3>Mode Debug</h3>
                            <p><?php echo $debugMode ? 'ACTIVÉ' : 'DÉSACTIVÉ'; ?></p>
                        </div>
                        <div class="info-item">
                            <h3>Serveur</h3>
                            <p><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'PHP ' . phpversion()); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Utilisation -->
            <div class="info-section">
                <div class="section-header">Utilisation</div>
                <div class="section-content">
                    <ul class="usage-list">
                        <li><span class="method">GET</span> <span class="route">/api/{route_name}</span> - Route legacy</li>
                        <li><span class="method">GET</span> <span class="route">/api/v{version}/{route_name}</span> - Route versionnée</li>
                    </ul>
                </div>
            </div>
            
            <!-- Exemples -->
            <div class="info-section">
                <div class="section-header">Exemples</div>
                <div class="section-content">
                    <ul class="usage-list">
                        <li><span class="route">/api/test</span></li>
                        <li><span class="route">/api/v1/test</span></li>
                        <li><span class="route">/api/v2/users</span></li>
                    </ul>
                </div>
            </div>
            
            <!-- Versions disponibles -->
            <?php if (!empty($availableVersions)): ?>
            <div class="info-section">
                <div class="section-header">Versions Disponibles</div>
                <div class="section-content">
                    <div class="versions-list">
                        <?php foreach ($availableVersions as $version): ?>
                            <span class="version-badge"><?php echo htmlspecialchars($version); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
        
        <footer>
            <p>StructureOne Framework &copy; 2024 | <a href="https://github.com/onestepcom00/structureone" target="_blank">Documentation</a></p>
        </footer>
    </div>
</body>
</html>
