<?php
// Add the Uzbek UI layer to every full HTML page without changing JSON/AJAX responses.
if (PHP_SAPI !== 'cli') {
    ob_start(static function (string $html): string {
        if (stripos($html, '<html') === false || stripos($html, '</body>') === false) {
            return $html;
        }

        if (stripos($html, 'application/json') !== false) {
            return $html;
        }

        $script = '<script src="/uzbek.js?v=2"></script>';
        return preg_replace('~</body>~i', $script . '</body>', $html, 1) ?: $html;
    });
}
?>
