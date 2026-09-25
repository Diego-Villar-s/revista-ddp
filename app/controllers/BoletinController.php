<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Boletin;

class BoletinController extends Controller
{
    public function index(): void
    {
        $page = max(1, (int) ($_GET['pagina'] ?? $_GET['page'] ?? 1));
        $model = new Boletin();
        $resultados = $model->paginatePublic($page, ITEMS_PER_PAGE);
        if ($resultados['totalPages'] > 0 && $resultados['page'] > $resultados['totalPages']) {
            $page = max(1, (int) $resultados['totalPages']);
            $resultados = $model->paginatePublic($page, ITEMS_PER_PAGE);
        }
        $seo = [
            'title' => 'Boletines NTEP | ' . SITE_NAME,
            'description' => 'Boletines de noticias sociales y territoriales de ' . SITE_NAME_FULL,
            'og_type' => 'website',
            'og_url' => BASE_URL . '/boletines',
        ];
        $this->view('boletines/index', compact('resultados', 'seo'));
    }

    public function show(): void
    {
        $numero = trim((string) ($_GET['numero'] ?? ''));
        $boletin = (new Boletin())->getByNumero($numero);
        if (!$boletin) {
            $this->render404();
            return;
        }
        $seo = [
            'title' => 'Boletín ' . $boletin['numero_boletin'] . ' | ' . SITE_NAME,
            'description' => mb_substr(strip_tags((string) ($boletin['resumen'] ?? '')), 0, 155),
            'og_type' => 'article',
            'og_url' => BASE_URL . '/boletines/' . rawurlencode((string) $boletin['numero_boletin']),
            'og_image' => $boletin['foto_portada'],
        ];
        $this->view('boletines/show', compact('boletin', 'seo'));
    }
}
