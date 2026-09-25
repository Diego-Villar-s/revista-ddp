<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Noticia;

class NoticiaController extends Controller
{
    public function index(): void
    {
        $page = max(1, (int) ($_GET['pagina'] ?? $_GET['page'] ?? 1));
        $model = new Noticia();
        $resultados = $model->paginatePublic($page, ITEMS_PER_PAGE);
        if ($resultados['totalPages'] > 0 && $resultados['page'] > $resultados['totalPages']) {
            $page = max(1, (int) $resultados['totalPages']);
            $resultados = $model->paginatePublic($page, ITEMS_PER_PAGE);
        }
        $seo = [
            'title' => 'Noticias | ' . SITE_NAME,
            'description' => 'Las últimas noticias de ' . SITE_NAME_FULL,
            'og_type' => 'website',
            'og_url' => BASE_URL . '/noticias',
        ];
        $this->view('noticias/index', compact('resultados', 'seo'));
    }
}
