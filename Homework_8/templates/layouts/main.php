<!doctype html>
<html lang="ru" class="<?= (($_COOKIE['theme'] ?? 'light') === 'dark') ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/style.css">
    <title><?=$titleSite?></title>
</head>
<body>
    <header class="site-header">
        <div class="brand">
            <a href="/">📝 Мой Блог</a>
        </div>

        <div class="prefs">
            <!-- Theme checkbox as per homework -->
            <label class="theme-toggle" title="Переключить светлую/тёмную тему. Значение хранится в куке.">
                <input type="checkbox" id="theme-checkbox"
                       <?= (($_COOKIE['theme'] ?? 'light') === 'dark') ? 'checked' : '' ?>>
                <span class="toggle-text">Тёмная тема</span>
            </label>

            <!-- Cookie demo: visit counter + clear button -->
            <span class="stat">
                Визиты: <strong id="visit-count"><?= (int)($_COOKIE['visit_count'] ?? 0) ?></strong>
                <button type="button" class="clear-btn" onclick="location.href='/?page=clear-cookies'" title="Очистить куки (счётчик визитов и сохранённая тема)">очистить</button>
            </span>

            <!-- Session demo: views in this session + clear button -->
            <span class="stat">
                В сессии: <strong><?= (int)($_SESSION['page_views'] ?? 0) ?></strong>
                <button type="button" class="clear-btn" onclick="location.href='/?page=clear-session'" title="Сбросить сессию (в т.ч. идентификатор для лайков)">сбросить сессию</button>
            </span>
        </div>
    </header>

    <?=$menu?>
    <?=$content?>

    <footer class="site-footer">
        <small>Homework 8 • PHP Блог • Cookies, Sessions &amp; Theme demo</small>
    </footer>

    <!-- Instant theme toggle via JS (sets cookie, toggles class, no reload needed) -->
    <script>
    (function () {
        const checkbox = document.getElementById('theme-checkbox');
        if (!checkbox) return;

        function applyTheme(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        // On toggle: update DOM immediately + persist in cookie (for PHP on next requests)
        checkbox.addEventListener('change', function () {
            const isDark = this.checked;
            applyTheme(isDark);

            // Set cookie (1 year)
            const value = isDark ? 'dark' : 'light';
            document.cookie = 'theme=' + value + '; path=/; max-age=' + (60 * 60 * 24 * 365);
        });

        // Also support keyboard and ensure initial state matches (in case of mismatch)
        // If user has no cookie yet, default is light and checkbox unchecked.
    })();
    </script>
</body>
</html>