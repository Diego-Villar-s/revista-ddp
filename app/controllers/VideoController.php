<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Video;

class VideoController extends Controller {
    
    private Video $videoModel;
    
    public function __construct() {
        $this->videoModel = new Video();
    }
    
    /**
     * Listado de videos con poster y reproductor
     */
    public function index(): void {
        $videos = $this->videoModel->getAllPublic();
        
        $seo = [
            'title'       => 'Videos | ' . SITE_NAME,
            'description' => 'Mira los videos de ' . SITE_NAME_FULL,
            'og_type'     => 'website',
            'og_url'      => BASE_URL . '/videos',
        ];
        
        $this->view('videos/index', compact('videos', 'seo'));
    }
    
    /**
     * Reproductor individual de video
     */
    public function show(): void {
        $slug = $this->param('slug');
        $video = $this->videoModel->findPublicBySlug($slug);
        
        if (!$video) {
            $this->render404();
            return;
        }
        
        // Últimos videos para sidebar
        $videos = $this->videoModel->getUltimos(5);
        
        $seo = [
            'title'       => $video['titulo'] . ' | Videos | ' . SITE_NAME,
            'description' => $video['titulo'] . ' - Video de ' . SITE_NAME_FULL,
            'og_type'     => 'video',
            'og_url'      => BASE_URL . '/videos/' . $video['slug'],
            'og_image'    => $video['poster'] ?? '',
        ];
        
        $this->view('videos/show', compact('video', 'videos', 'seo'));
    }
}