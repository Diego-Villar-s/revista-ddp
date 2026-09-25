<?php
namespace App\Core;

/**
 * Renderiza reproductores públicos de podcasts, videos y especiales.
 *
 * Se mantiene en un solo lugar para que el detalle y el modal de reproducción
 * produzcan exactamente el mismo HTML según el tipo guardado en la BD.
 */
final class MediaPlayer
{
    public static function podcast(array $podcast): string
    {
        $titulo = self::escape((string) ($podcast['titulo'] ?? 'Podcast'));
        $embed = trim((string) ($podcast['url_embed'] ?? ''));

        if (($podcast['tipo'] ?? '') === 'embed' && $embed !== '') {
            return '<div class="ddp-player ddp-player-embed">'
                . '<div class="ddp-repro-frame">'
                . '<iframe src="' . self::escape($embed) . '" title="' . $titulo . '" allow="autoplay; clipboard-write; encrypted-media; fullscreen" allowfullscreen></iframe>'
                . '</div></div>';
        }

        $archivo = trim((string) ($podcast['archivo_audio'] ?? ''));
        if ($archivo !== '') {
            return '<div class="ddp-player ddp-player-audio">'
                . '<audio controls autoplay preload="metadata">'
                . '<source src="' . self::escape(self::assetUrl($archivo)) . '">'
                . 'Tu navegador no soporta audio HTML5.'
                . '</audio></div>';
        }

        return '<p class="ddp-repro-error">Este podcast todavía no tiene un reproductor disponible.</p>';
    }

    public static function video(array $video): string
    {
        $titulo = self::escape((string) ($video['titulo'] ?? 'Video'));
        $embed = trim((string) ($video['url_embed'] ?? ''));

        if (($video['tipo'] ?? '') === 'embed' && $embed !== '') {
            return '<div class="ddp-player ddp-player-embed">'
                . '<div class="ddp-repro-frame">'
                . '<iframe src="' . self::escape($embed) . '" title="' . $titulo . '" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>'
                . '</div></div>';
        }

        $archivo = trim((string) ($video['archivo_video'] ?? ''));
        if ($archivo !== '') {
            $poster = trim((string) ($video['poster'] ?? ''));
            $posterAttr = $poster !== '' ? ' poster="' . self::escape(self::assetUrl($poster)) . '"' : '';
            return '<div class="ddp-player ddp-player-video">'
                . '<video controls autoplay playsinline preload="metadata"' . $posterAttr . '>'
                . '<source src="' . self::escape(self::assetUrl($archivo)) . '">'
                . 'Tu navegador no soporta video HTML5.'
                . '</video></div>';
        }

        return '<p class="ddp-repro-error">Este video todavía no tiene un reproductor disponible.</p>';
    }

    public static function especial(array $especial): string
    {
        $titulo = self::escape((string) ($especial['titulo_completo'] ?? 'Especial'));
        if (!function_exists('ddpVideoEmbedUrl')) {
            require_once APP_PATH . '/views/public/partials/helpers.php';
        }
        $embed = ddpVideoEmbedUrl((string) ($especial['url_video'] ?? ''));

        if ($embed !== '') {
            return '<div class="ddp-player ddp-player-embed">'
                . '<div class="ddp-repro-frame">'
                . '<iframe src="' . self::escape($embed) . '" title="' . $titulo . '" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>'
                . '</div></div>';
        }

        return '<p class="ddp-repro-error">Este especial todavía no tiene un video válido.</p>';
    }

    private static function assetUrl(string $path): string
    {
        if (!function_exists('ddpImgUrl')) {
            require_once APP_PATH . '/views/public/partials/helpers.php';
        }
        return ddpImgUrl($path);
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
