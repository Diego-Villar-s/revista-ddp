<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Boletin;
use App\Models\Especial;
use App\Models\Noticia;
use App\Models\Podcast;
use App\Models\Reportaje;

class HomeController extends Controller
{
    public function index(): void
    {
        $reportajeModel = new Reportaje();
        $noticiaModel = new Noticia();
        $boletinModel = new Boletin();
        $podcastModel = new Podcast();
        $especialModel = new Especial();

        $reportajePrincipal = $reportajeModel->getDestacadoPrincipal();
        $ultimosReportajes = $reportajeModel->getUltimos(3, $reportajePrincipal ? (int) $reportajePrincipal['id'] : null);
        $noticiasRecientes = $noticiaModel->getUltimas(3);
        $especiales = $especialModel->getActive();
        $ultimoBoletin = $boletinModel->getUltimo();
        $ultimosPodcasts = $podcastModel->getUltimos(4);

        $seo = [
            'title' => SITE_NAME . ' | ' . SITE_NAME_FULL,
            'description' => SITE_DESCRIPTION,
            'og_type' => 'website',
            'og_url' => BASE_URL . '/',
            'og_image' => PUBLIC_URL . '/assets/img/logo.png',
        ];

        $this->view('home', compact(
            'reportajePrincipal',
            'ultimosReportajes',
            'noticiasRecientes',
            'especiales',
            'ultimoBoletin',
            'ultimosPodcasts',
            'seo'
        ));
    }
}
