<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\Reportaje;
use App\Models\Noticia;
use App\Models\Boletin;
use App\Models\Podcast;
use App\Models\Video;
use App\Models\Autor;
use App\Models\Usuario;
use App\Models\LogActividad;

class DashboardController extends AdminController {
    
    /**
     * Panel principal con conteos y actividad reciente
     */
    public function index(): void {
        $titlePage = 'Panel de Control';
        
        // Conteos por tipo de contenido
        $reportajeModel = new Reportaje();
        $noticiaModel = new Noticia();
        $boletinModel = new Boletin();
        $podcastModel = new Podcast();
        $videoModel = new Video();
        $autorModel = new Autor();
        $logModel = new LogActividad();
        
        $conteos = [
            'reportajes'   => $reportajeModel->countWhere(),
            'borradores'   => $reportajeModel->countWhere("estado = 'borrador'"),
            'publicados'   => $reportajeModel->countWhere("estado = 'publicado'"),
            'archivados'   => $reportajeModel->countWhere("estado = 'archivado'"),
            'destacados'   => $reportajeModel->countWhere("es_destacado = 1"),
            'noticias'     => $noticiaModel->countWhere(),
            'boletines'    => $boletinModel->countWhere(),
            'podcasts'     => $podcastModel->countWhere(),
            'videos'       => $videoModel->countWhere(),
            'autores'      => $autorModel->countWhere(),
            'usuarios'     => (new Usuario())->countWhere(),
        ];
        
        // Últimos movimientos
        $ultimosLogs = $logModel->getUltimos(10);
        
        // Accesos rápidos (tablas populares)
        $ultimosReportajes = $reportajeModel->paginateAdmin([], 1, 5)['items'];
        
        // Progreso del día (para el mini-grafico de la tarjeta de reportajes)
        $reportajesPorEstado = $reportajeModel->countByEstado();
        
        $this->view('dashboard', compact(
            'titlePage',
            'conteos',
            'ultimosLogs',
            'ultimosReportajes',
            'reportajesPorEstado'
        ));
    }
}