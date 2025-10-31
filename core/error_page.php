<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - StructureOne</title>
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
            gap: 15px;
        }
        
        .logo {
            width: 200px;
            height: 200px;
            object-fit: contain;
        }
        
        .logo-text h1 {
            font-size: 24px;
            font-weight: 600;
            color: #fff;
        }
        
        .logo-text p {
            font-size: 12px;
            color: #888;
        }
        
        main {
            flex: 1;
            padding: 40px 0;
        }
        
        .error-badge {
            display: inline-block;
            background: #ff4444;
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        h2 {
            font-size: 36px;
            margin-bottom: 15px;
            color: #fff;
        }
        
        .error-message {
            font-size: 18px;
            color: #ccc;
            margin-bottom: 30px;
            padding: 20px;
            background: #111;
            border-left: 4px solid #ff4444;
            border-radius: 4px;
        }
        
        .error-details {
            background: #0a0a0a;
            border: 1px solid #222;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .details-header {
            background: #111;
            padding: 15px 20px;
            border-bottom: 1px solid #222;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .details-header h3 {
            font-size: 16px;
            color: #fff;
        }
        
        .details-content {
            padding: 20px;
        }
        
        .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #1a1a1a;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            width: 150px;
            color: #888;
            font-weight: 600;
            flex-shrink: 0;
        }
        
        .detail-value {
            flex: 1;
            color: #fff;
            word-break: break-all;
        }
        
        .code-block {
            background: #0d0d0d;
            border: 1px solid #222;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
            overflow-x: auto;
        }
        
        .code-block pre {
            color: #e06c75;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            white-space: pre-wrap;
        }
        
        .stack-trace {
            margin-top: 20px;
        }
        
        .stack-item {
            background: #0d0d0d;
            border: 1px solid #222;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .stack-number {
            display: inline-block;
            background: #222;
            color: #888;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-right: 10px;
        }
        
        .stack-file {
            color: #61afef;
        }
        
        .stack-line {
            color: #888;
        }
        
        .suggestions {
            background: #0a0a0a;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .suggestions h3 {
            color: #61afef;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .suggestions ul {
            list-style: none;
        }
        
        .suggestions li {
            padding: 10px 0;
            padding-left: 25px;
            position: relative;
            color: #ccc;
        }
        
        .suggestions li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #61afef;
        }
        
        footer {
            padding: 20px 0;
            border-top: 1px solid #222;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-error {
            background: #ff4444;
            color: #fff;
        }
        
        .badge-info {
            background: #222;
            color: #888;
        }
        
        @media (max-width: 768px) {
            .logo { width: 40px; height: 40px; }
            h2 { font-size: 28px; }
            .error-message { font-size: 16px; }
            .container { padding: 15px; }
            .detail-label { width: 100px; font-size: 13px; }
            .detail-value { font-size: 14px; }
        }
        
        @media (max-width: 480px) {
            .logo { width: 35px; height: 35px; }
            h2 { font-size: 24px; }
            .error-badge { font-size: 12px; padding: 6px 12px; }
            .detail-row { flex-direction: column; }
            .detail-label { width: 100%; margin-bottom: 5px; }
            .code-block { font-size: 12px; padding: 10px; }
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
            <span class="error-badge"><?php echo htmlspecialchars($errorType ?? 'EXCEPTION'); ?></span>
            <h2><?php echo htmlspecialchars($errorTitle ?? 'Une erreur est survenue'); ?></h2>
            
            <div class="error-message">
                <?php echo htmlspecialchars($errorMessage ?? 'Internal Server Error'); ?>
            </div>
            
            <?php if (!empty($errorFile) && !empty($errorLine)): ?>
            <div class="error-details">
                <div class="details-header">
                    <h3>📍 Localisation de l'erreur</h3>
                    <span class="badge badge-error">Ligne <?php echo $errorLine; ?></span>
                </div>
                <div class="details-content">
                    <div class="detail-row">
                        <div class="detail-label">Fichier :</div>
                        <div class="detail-value"><?php echo htmlspecialchars($errorFile); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Ligne :</div>
                        <div class="detail-value"><?php echo $errorLine; ?></div>
                    </div>
                    <?php if (!empty($errorType)): ?>
                    <div class="detail-row">
                        <div class="detail-label">Type :</div>
                        <div class="detail-value"><?php echo htmlspecialchars($errorType); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($errorCode)): ?>
            <div class="error-details">
                <div class="details-header">
                    <h3>💻 Extrait du code</h3>
                </div>
                <div class="details-content">
                    <div class="code-block">
                        <pre><?php echo htmlspecialchars($errorCode); ?></pre>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($errorTrace) && is_array($errorTrace)): ?>
            <div class="error-details">
                <div class="details-header">
                    <h3>📚 Stack Trace</h3>
                    <span class="badge badge-info"><?php echo count($errorTrace); ?> appels</span>
                </div>
                <div class="details-content">
                    <div class="stack-trace">
                        <?php foreach (array_slice($errorTrace, 0, 5) as $index => $trace): ?>
                        <div class="stack-item">
                            <span class="stack-number">#<?php echo $index; ?></span>
                            <span class="stack-file">
                                <?php echo htmlspecialchars($trace['file'] ?? 'unknown'); ?>
                            </span>
                            <span class="stack-line">
                                <?php if (isset($trace['line'])): ?>
                                    : ligne <?php echo $trace['line']; ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="suggestions">
                <h3>💡 Suggestions pour résoudre cette erreur</h3>
                <ul>
                    <li>Vérifiez la syntaxe de votre code dans le fichier indiqué</li>
                    <li>Assurez-vous que toutes les variables utilisées sont définies</li>
                    <li>Consultez les logs PHP pour plus de détails</li>
                    <li>Vérifiez que DEBUG_MODE est activé dans votre fichier .env</li>
                    <li>Consultez la documentation StructureOne pour plus d'aide</li>
                </ul>
            </div>
        </main>
        
        <footer>
            <p>StructureOne v2.1.1+ | Mode Debug Activé | © 2024</p>
        </footer>
    </div>
</body>
</html>
