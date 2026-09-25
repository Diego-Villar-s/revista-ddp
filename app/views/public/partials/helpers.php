<?php
/**
 * Funciones auxiliares reutilizables en las vistas del sitio público
 */

if (!function_exists('ddpImgUrl')) {
    /**
     * Convierte una ruta interna en URL pública completa.
     * - assets/...  -> PUBLIC_URL + '/' + ruta
     * - /uploads/... o cualquier otra -> BASE_URL + '/uploads/' + ruta
     * - http(s)://  -> tal cual (esquemas absolutos)
     */
    function ddpImgUrl($ruta) {
        if (empty($ruta)) return '';
        if (preg_match('#^https?://#i', $ruta)) return $ruta;
        if (str_starts_with($ruta, 'assets/')) return PUBLIC_URL . '/' . ltrim($ruta, '/');
        if (str_starts_with($ruta, 'uploads/')) return BASE_URL . '/' . ltrim($ruta, '/');
        if (str_starts_with($ruta, '/')) return BASE_URL . $ruta;
        return BASE_URL . '/uploads/' . ltrim($ruta, '/');
    }
}

if (!function_exists('ddpFecha')) {
    /**
     * Formatea una fecha (YYYY-MM-DD) en español: "5 de setiembre de 2026"
     */
    function ddpFecha($fecha) {
        if (empty($fecha)) return '';
        $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','setiembre','octubre','noviembre','diciembre'];
        $ts = strtotime((string) $fecha);
        if ($ts === false) return '';
        return (int) date('j', $ts) . ' de ' . $meses[(int) date('n', $ts) - 1] . ' de ' . date('Y', $ts);
    }
}

if (!function_exists('ddpFechaCorta')) {
    /**
     * Formato corto del sitio de referencia: "Set 09, 2026"
     */
    function ddpFechaCorta($fecha) {
        if (empty($fecha)) return '';
        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Set','Oct','Nov','Dic'];
        $ts = strtotime((string) $fecha);
        if ($ts === false) return '';
        return $meses[(int) date('n', $ts) - 1] . ' ' . date('j', $ts) . ', ' . date('Y', $ts);
    }
}

if (!function_exists('ddpFechaLarga')) {
    /**
     * Fecha con mes completo: "Noviembre 21, 2025"
     */
    function ddpFechaLarga($fecha) {
        if (empty($fecha)) return '';
        $meses = explode(' ', 'Enero Febrero Marzo Abril Mayo Junio Julio Agosto Setiembre Octubre Noviembre Diciembre');
        $ts = strtotime((string) $fecha);
        if ($ts === false) return '';
        return $meses[(int) date('n', $ts) - 1] . ' ' . date('j', $ts) . ', ' . date('Y', $ts);
    }
}

if (!function_exists('ddpFechaDia')) {
    /**
     * Solo día y mes: "28 agosto"
     */
    function ddpFechaDia($fecha) {
        if (empty($fecha)) return '';
        $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','setiembre','octubre','noviembre','diciembre'];
        $ts = strtotime((string) $fecha);
        if ($ts === false) return '';
        return (int) date('j', $ts) . ' ' . $meses[(int) date('n', $ts) - 1];
    }
}

if (!function_exists('ddpImg')) {
    /**
     * Devuelve la URL de la imagen real si existe; si no, un placeholder SVG inline.
     */
    function ddpImg($ruta, $seed = 'ddp', $w = 800, $h = 600) {
        $url = ddpImgUrl($ruta);
        if ($url !== '') return $url;
        return 'data:image/svg+xml;base64,' . base64_encode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="' . $w . '" height="' . $h .
            '" viewBox="0 0 ' . $w . ' ' . $h . '"><rect width="100%" height="100%" fill="#e9ecef"/>' .
            '<text x="50%" y="50%" fill="#6c757d" font-family="Arial, sans-serif" font-size="20" text-anchor="middle" dominant-baseline="middle">Sin imagen</text></svg>'
        );
    }
}

if (!function_exists('ddpIconoSocial')) {
    /**
     * Iconos de redes sociales en SVG (sin dependencias externas)
     */
    function ddpIconoSocial(string $tipo): string {
        switch ($tipo) {
            case 'facebook':
                return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-6.6h2.2l.4-2.7h-2.6V9.9c0-.8.3-1.4 1.5-1.4H16.2V6.1c-.3 0-1.2-.1-2.2-.1-2.2 0-3.8 1.4-3.8 3.9v1.7H8.1v2.7h2.1V21h3.3z"/></svg>';
            case 'instagram':
                return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.9.3 2.4.5.6.2 1 .5 1.5 1 .5.5.8.9 1 1.5.2.5.4 1.2.5 2.4.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 1.9-.5 2.4-.2.6-.5 1-1 1.5-.5.5-.9.8-1.5 1-.5.2-1.2.4-2.4.5-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.9-.3-2.4-.5-.6-.2-1-.5-1.5-1-.5-.5-.8-.9-1-1.5-.2-.5-.4-1.2-.5-2.4-.1-1.3-.1-1.7-.1-4.9s0-3.6.1-4.9c.1-1.2.3-1.9.5-2.4.2-.6.5-1 1-1.5.5-.5.9-.8 1.5-1 .5-.2 1.2-.4 2.4-.5 1.3-.1 1.7-.1 4.9-.1zm0 1.8c-3.1 0-3.5 0-4.7.1-1.1.1-1.7.2-2.1.4-.5.2-.9.4-1.2.8-.4.4-.6.7-.8 1.2-.2.4-.3 1-.4 2.1-.1 1.2-.1 1.6-.1 4.7s0 3.5.1 4.7c.1 1.1.2 1.7.4 2.1.2.5.4.9.8 1.2.4.4.7.6 1.2.8.4.2 1 .3 2.1.4 1.2.1 1.6.1 4.7.1s3.5 0 4.7-.1c1.1-.1 1.7-.2 2.1-.4.5-.2.9-.4 1.2-.8.4-.4.6-.7.8-1.2.2-.4.3-1 .4-2.1.1-1.2.1-1.6.1-4.7s0-3.5-.1-4.7c-.1-1.1-.2-1.7-.4-2.1-.2-.5-.4-.9-.8-1.2-.4-.4-.7-.6-1.2-.8-.4-.2-1-.3-2.1-.4-1.2-.1-1.6-.1-4.7-.1zm0 4.6a5.2 5.2 0 1 1 0 10.4 5.2 5.2 0 0 1 0-10.4zm0 8.6a3.4 3.4 0 1 0 0-6.8 3.4 3.4 0 0 0 0 6.8zm6.6-9.2a1.3 1.3 0 1 1-2.6 0 1.3 1.3 0 0 1 2.6 0z"/></svg>';
            case 'tiktok':
                return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.6 6.8a4.9 4.9 0 0 1-3-2.8 5 5 0 0 1-.2-1.9h-3.3v12.6a2.9 2.9 0 1 1-2.9-2.9c.3 0 .6.1.9.2V8.5a6.3 6.3 0 0 0-.9-.1 6.2 6.2 0 1 0 6.2 6.2V8.4a7.9 7.9 0 0 0 4.6 1.5l.6-.3V6.9c-.2 0-.6 0-1.2-.1z"/></svg>';
            default:
                return '';
        }
    }
}

if (!function_exists('ddpAutor')) {
    /**
     * Devuelve el nombre público de un autor (nickname si aplica)
     */
    function ddpAutor($r) {
        if (!empty($r['es_nickname'] ?? 0) && !empty($r['nickname'])) return $r['nickname'];
        return trim(($r['nombres'] ?? '') . ' ' . ($r['ap_paterno'] ?? ''));
    }
}

if (!function_exists('ddpFormatoDuracion')) {
    /**
     * Convierte segundos en formato mm:ss
     */
    function ddpFormatoDuracion($segundos) {
        if (empty($segundos)) return '';
        return gmdate($segundos >= 3600 ? 'H:i:s' : 'i:s', (int) $segundos);
    }
}

if (!function_exists('ddpFormatoBytes')) {
    /**
     * Convierte bytes en una representación legible (KB/MB)
     */
    function ddpFormatoBytes($bytes) {
        if (empty($bytes)) return '';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }
}

if (!function_exists('ddpLeetVariant')) {
    /**
     * Variante leet de una palabra, solo como fallback para localizar el
     * resaltado cuando el admin escribe la palabra normal en el título leet.
     */
    function ddpLeetVariant(string $text): string {
        return strtr($text, [
            'a' => '4', 'A' => '4',
            'e' => '3', 'E' => '3',
            'i' => '1', 'I' => '1',
            'o' => '0', 'O' => '0',
        ]);
    }
}

if (!function_exists('ddpResaltarLeet')) {
    /**
     * Devuelve el título leet en mayúsculas y resalta todas las coincidencias
     * de la palabra indicada sin permitir HTML del administrador.
     */
    function ddpResaltarLeet(string $titulo, string $palabraResaltada): string {
        $titulo = trim($titulo);
        $needle = trim($palabraResaltada);
        $escape = static fn(string $value): string => htmlspecialchars(
            mb_strtoupper($value, 'UTF-8'),
            ENT_QUOTES,
            'UTF-8'
        );

        if ($needle === '' || $titulo === '') {
            return $escape($titulo);
        }

        $position = mb_stripos($titulo, $needle, 0, 'UTF-8');
        if ($position === false) {
            $needle = ddpLeetVariant($needle);
            $position = $needle !== '' ? mb_stripos($titulo, $needle, 0, 'UTF-8') : false;
        }
        if ($position === false) {
            return $escape($titulo);
        }

        $result = '';
        $cursor = 0;
        $length = mb_strlen($needle, 'UTF-8');
        while ($position !== false) {
            $result .= $escape(mb_substr($titulo, $cursor, $position - $cursor, 'UTF-8'));
            $match = mb_substr($titulo, $position, $length, 'UTF-8');
            $result .= '<mark class="ddp-especial-highlight">' . $escape($match) . '</mark>';
            $cursor = $position + $length;
            $position = $length > 0
                ? mb_stripos($titulo, $needle, $cursor, 'UTF-8')
                : false;
        }
        $result .= $escape(mb_substr($titulo, $cursor, null, 'UTF-8'));
        return $result;
    }
}

if (!function_exists('ddpVideoEmbedUrl')) {
    /**
     * Detecta URLs de YouTube/Vimeo y devuelve únicamente la URL de embed.
     */
    function ddpVideoEmbedUrl(string $url): string {
        $url = trim($url);
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }

        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $host = preg_replace('/^www\\./', '', $host) ?? $host;
        $path = trim((string) ($parts['path'] ?? ''), '/');
        $query = [];
        parse_str((string) ($parts['query'] ?? ''), $query);

        if ($host === 'youtu.be' || str_ends_with($host, '.youtu.be')) {
            $id = explode('/', $path)[0] ?? '';
            return preg_match('/^[A-Za-z0-9_-]{6,}$/', $id)
                ? 'https://www.youtube.com/embed/' . rawurlencode($id)
                : '';
        }

        if ($host === 'youtube.com' || $host === 'm.youtube.com' || $host === 'youtube-nocookie.com') {
            $id = (string) ($query['v'] ?? '');
            if ($id === '' && preg_match('#^(?:embed|v|shorts|live)/([A-Za-z0-9_-]{6,})#i', $path, $matches)) {
                $id = $matches[1];
            }
            if ($id === '' || !preg_match('/^[A-Za-z0-9_-]{6,}$/', $id)) {
                return '';
            }
            return 'https://www.youtube.com/embed/' . rawurlencode($id);
        }

        if ($host === 'vimeo.com' || $host === 'player.vimeo.com') {
            if (preg_match('#(?:video/)?([0-9]+)(?:/.*)?$#', $path, $matches)) {
                return 'https://player.vimeo.com/video/' . rawurlencode($matches[1]);
            }
        }

        return '';
    }
}