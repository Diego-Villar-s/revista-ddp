<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Podcast;

class PodcastController extends Controller {
    
    private Podcast $podcastModel;
    
    public function __construct() {
        $this->podcastModel = new Podcast();
    }
    
    /**
     * Listado de podcasts con reproductor embebido
     */
    public function index(): void {
        $podcasts = $this->podcastModel->getAllPublic();
        
        $seo = [
            'title'       => 'Podcast | ' . SITE_NAME,
            'description' => 'Escucha los podcasts de ' . SITE_NAME_FULL,
            'og_type'     => 'website',
            'og_url'      => BASE_URL . '/podcast',
        ];
        
        $this->view('podcasts/index', compact('podcasts', 'seo'));
    }
    
    /**
     * Reproductor individual de podcast
     */
    public function show(): void {
        $slug = $this->param('slug');
        $podcast = $this->podcastModel->findPublicBySlug($slug);
        
        if (!$podcast) {
            $this->render404();
            return;
        }
        
        // Últimos podcasts para sidebar
        $podcasts = $this->podcastModel->getUltimos(5);
        
        $seo = [
            'title'       => $podcast['titulo'] . ' | Podcast | ' . SITE_NAME,
            'description' => $podcast['titulo'] . ' - Podcast de ' . SITE_NAME_FULL,
            'og_type'     => 'article',
            'og_url'      => BASE_URL . '/podcast/' . $podcast['slug'],
            'og_image'    => $podcast['portada'],
        ];
        
        $this->view('podcasts/show', compact('podcast', 'podcasts', 'seo'));
    }
}