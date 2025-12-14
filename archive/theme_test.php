<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Test</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            transition: all 0.3s ease;
        }
        body.light {
            background-color: #f7f7f7;
            color: #1a1a1a;
        }
        body.dark {
            background-color: #1e1e1e;
            color: #ffffff;
        }
    </style>
</head>
<body class="light">
    <h1>Theme Test Page</h1>
    <p>This is a test to see if theme switching works.</p>
    <button onclick="toggleTheme()">Toggle Theme</button>
    
    <script>
        function toggleTheme() {
            const body = document.body;
            if (body.classList.contains('light')) {
                body.classList.remove('light');
                body.classList.add('dark');
            } else {
                body.classList.remove('dark');
                body.classList.add('light');
            }
        }
    </script>
</body>
</html>