<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\MediaPlayer;
use App\Models\Especial;
use App\Models\Podcast;
use App\Models\Video;

/**
 * Endpoint público para el modal de reproducción de multimedia.
 * No requiere sesión: solo entrega filas publicadas y su HTML reproducible.
 */
final class ReproductorController extends Controller
{
    public function reproducir(): void
    {
        require_once APP_PATH . '/views/public/partials/helpers.php';

        $tipoValue = $_GET['tipo'] ?? '';
        $idValue = $_GET['id'] ?? '';
        $tipo = is_scalar($tipoValue) ? strtolower(trim((string) $tipoValue)) : '';
        $id = is_scalar($idValue) ? (int) $idValue : 0;

        if (!in_array($tipo, ['podcast', 'video', 'especial'], true) || $id < 1) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            $this->json(['ok' => false, 'message' => 'Solicitud de reproducción inválida.'], 400);
            return;
        }

        $record = match ($tipo) {
            'podcast' => (new Podcast())->findPublicById($id),
            'video' => (new Video())->findPublicById($id),
            default => (new Especial())->findPublicById($id),
        };

        if (!$record) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            $this->json(['ok' => false, 'message' => 'El contenido no está disponible o no fue publicado.'], 404);
            return;
        }

        $html = match ($tipo) {
            'podcast' => MediaPlayer::podcast($record),
            'video' => MediaPlayer::video($record),
            default => MediaPlayer::especial($record),
        };

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->json([
            'ok' => true,
            'tipo' => $tipo,
            'id' => $id,
            'titulo' => (string) ($record['titulo'] ?? $record['titulo_completo'] ?? ''),
            'html' => $html,
        ]);
    }
}
