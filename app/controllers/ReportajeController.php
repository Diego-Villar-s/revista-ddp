<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Reportaje;

class ReportajeController extends Controller
{
    private Reportaje $model;

    public function __construct()
    {
        $this->model = new Reportaje();
    }

    public function index(): void
    {
        $requestedPage = (int) ($_GET['pagina'] ?? $_GET['page'] ?? 1);
        $requestedPage = max(1, $requestedPage);
        $resultados = $this->model->getPublicadosPaginated($requestedPage, REPORT_ITEMS_PER_PAGE);

        if ($resultados['totalPages'] > 0 && $resultados['page'] > $resultados['totalPages']) {
            $requestedPage = max(1, (int) $resultados['totalPages']);
            $resultados = $this->model->getPublicadosPaginated($requestedPage, REPORT_ITEMS_PER_PAGE);
        }

        $periodo = (string) ($_GET['archivo'] ?? '');
        if ($periodo !== '' && preg_match('/^\d{4}-\d{2}$/', $periodo)) {
            $resultados['items'] = $this->model->getByPeriodo($periodo);
            $resultados['total'] = count($resultados['items']);
            $resultados['totalPages'] = 1;
            $resultados['page'] = 1;
        }

        $archivos = $this->model->getArchivos();
        $seo = [
            'title' => 'Reportajes | ' . SITE_NAME,
            'description' => 'Todos los reportajes de ' . SITE_NAME_FULL,
            'og_type' => 'website',
            'og_url' => BASE_URL . '/reportajes',
        ];
        $this->view('reportajes/index', compact('resultados', 'archivos', 'seo', 'periodo'));
    }

    public function show(): void
    {
        $slug = trim((string) ($_GET['slug'] ?? ''));
        $reportaje = $this->model->findPublicBySlug($slug);
        if (!$reportaje) {
            $this->render404();
            return;
        }

        $fotos = $this->model->getFotos((int) $reportaje['id']);
        $ultimos = $this->model->getUltimos(3, (int) $reportaje['id']);
        $archivos = $this->model->getArchivos();
        $seo = [
            'title' => $reportaje['meta_titulo'] ?: $reportaje['titulo'],
            'description' => $reportaje['meta_descripcion'] ?: mb_substr(strip_tags((string) $reportaje['resumen_corto']), 0, 155),
            'og_type' => 'article',
            'og_url' => BASE_URL . '/reportajes/' . rawurlencode((string) $reportaje['slug']),
            'og_image' => $reportaje['foto_principal'],
        ];
        $this->view('reportajes/show', compact('reportaje', 'fotos', 'ultimos', 'archivos', 'seo'));
    }
}
