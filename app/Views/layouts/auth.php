<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SIMAGANG - Sistem Informasi Magang' ?></title>
    <!-- Tailwind CSS (CDN/Compiled Static Style) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            50: '#f0f4f8',
                            100: '#d9e2ec',
                            500: '#334e68',
                            600: '#243b53',
                            700: '#102a43',
                            800: '#091e36',
                        },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 min-h-screen flex items-center justify-center p-4">
    <?= $this->renderSection('content') ?>
</body>
</html>
