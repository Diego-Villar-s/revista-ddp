<?php
/**
 * Tabla de rutas del front controller. Los parámetros de ruta se entregan
 * al controlador en $_GET.
 */

use App\Controllers\HomeController;
use App\Controllers\ReportajeController;
use App\Controllers\NoticiaController;
use App\Controllers\BoletinController;
use App\Controllers\PodcastController;
use App\Controllers\VideoController;
use App\Controllers\PaginaController;
use App\Controllers\SitemapController;
use App\Controllers\ReproductorController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ReportajeAdminController;
use App\Controllers\Admin\NoticiaAdminController;
use App\Controllers\Admin\BoletinAdminController;
use App\Controllers\Admin\PodcastAdminController;
use App\Controllers\Admin\VideoAdminController;
use App\Controllers\Admin\EspecialAdminController;
use App\Controllers\Admin\AutorAdminController;
use App\Controllers\Admin\UsuarioAdminController;
use App\Controllers\Admin\PaginaAdminController;
use App\Controllers\Admin\ConfiguracionAdminController;

/* ==================== SITIO PÚBLICO ==================== */
$router->get('/', HomeController::class, 'index');
$router->get('/reportajes', ReportajeController::class, 'index');
$router->get('/reportajes/pagina/{pagina}', ReportajeController::class, 'index');
$router->get('/reportajes/{slug}', ReportajeController::class, 'show');
$router->get('/noticias', NoticiaController::class, 'index');
$router->get('/boletines', BoletinController::class, 'index');
$router->get('/boletines/{numero}', BoletinController::class, 'show');
$router->get('/podcast', PodcastController::class, 'index');
$router->get('/podcast/{slug}', PodcastController::class, 'show');
$router->get('/videos', VideoController::class, 'index');
$router->get('/videos/{slug}', VideoController::class, 'show');
$router->get('/sobre-nosotros', PaginaController::class, 'sobreNosotros');
$router->get('/alianzas', PaginaController::class, 'alianzas');
$router->get('/contacto', PaginaController::class, 'contacto');
$router->post('/contacto', PaginaController::class, 'enviarContacto');
$router->get('/sitemap.xml', SitemapController::class, 'sitemap');
$router->get('/robots.txt', SitemapController::class, 'robots');
$router->get('/reproductor', ReproductorController::class, 'reproducir');

/* ==================== PANEL ADMIN ==================== */
$router->get('/admin/login', AuthController::class, 'login');
$router->post('/admin/login', AuthController::class, 'procesarLogin');
$router->get('/admin/logout', AuthController::class, 'logout');
$router->get('/admin/recuperar', AuthController::class, 'recuperar');
$router->post('/admin/recuperar', AuthController::class, 'procesarRecuperar');
$router->get('/admin/reset/{token}', AuthController::class, 'mostrarReset');
$router->post('/admin/reset/{token}', AuthController::class, 'procesarReset');
$router->get('/admin', DashboardController::class, 'index');
$router->get('/admin/configuracion', ConfiguracionAdminController::class, 'index');
$router->post('/admin/configuracion', ConfiguracionAdminController::class, 'update');

$router->get('/admin/reportajes', ReportajeAdminController::class, 'index');
$router->get('/admin/reportajes/nuevo', ReportajeAdminController::class, 'create');
$router->post('/admin/reportajes/nuevo', ReportajeAdminController::class, 'store');
$router->get('/admin/reportajes/{id}/editar', ReportajeAdminController::class, 'edit');
$router->post('/admin/reportajes/{id}/editar', ReportajeAdminController::class, 'update');
$router->post('/admin/reportajes/{id}/eliminar', ReportajeAdminController::class, 'delete');
$router->post('/admin/reportajes/{id}/toggle-destacado', ReportajeAdminController::class, 'toggleDestacado');
$router->post('/admin/reportajes/{id}/preview', ReportajeAdminController::class, 'preview');
$router->post('/admin/reportajes/fotos/subir', ReportajeAdminController::class, 'subirFoto');
$router->post('/admin/reportajes/fotos/{fotoId}/eliminar', ReportajeAdminController::class, 'eliminarFoto');

$router->get('/admin/noticias', NoticiaAdminController::class, 'index');
$router->get('/admin/noticias/nuevo', NoticiaAdminController::class, 'create');
$router->post('/admin/noticias/nuevo', NoticiaAdminController::class, 'store');
$router->get('/admin/noticias/{id}/editar', NoticiaAdminController::class, 'edit');
$router->post('/admin/noticias/{id}/editar', NoticiaAdminController::class, 'update');
$router->post('/admin/noticias/{id}/eliminar', NoticiaAdminController::class, 'delete');

$router->get('/admin/boletines', BoletinAdminController::class, 'index');
$router->get('/admin/boletines/nuevo', BoletinAdminController::class, 'create');
$router->post('/admin/boletines/nuevo', BoletinAdminController::class, 'store');
$router->get('/admin/boletines/{id}/editar', BoletinAdminController::class, 'edit');
$router->post('/admin/boletines/{id}/editar', BoletinAdminController::class, 'update');
$router->post('/admin/boletines/{id}/eliminar', BoletinAdminController::class, 'delete');

$router->get('/admin/podcasts', PodcastAdminController::class, 'index');
$router->get('/admin/podcasts/nuevo', PodcastAdminController::class, 'create');
$router->post('/admin/podcasts/nuevo', PodcastAdminController::class, 'store');
$router->get('/admin/podcasts/{id}/editar', PodcastAdminController::class, 'edit');
$router->post('/admin/podcasts/{id}/editar', PodcastAdminController::class, 'update');
$router->post('/admin/podcasts/{id}/eliminar', PodcastAdminController::class, 'delete');

$router->get('/admin/videos', VideoAdminController::class, 'index');
$router->get('/admin/videos/nuevo', VideoAdminController::class, 'create');
$router->post('/admin/videos/nuevo', VideoAdminController::class, 'store');
$router->get('/admin/videos/{id}/editar', VideoAdminController::class, 'edit');
$router->post('/admin/videos/{id}/editar', VideoAdminController::class, 'update');
$router->post('/admin/videos/{id}/eliminar', VideoAdminController::class, 'delete');

$router->get('/admin/especiales', EspecialAdminController::class, 'index');
$router->get('/admin/especiales/nuevo', EspecialAdminController::class, 'create');
$router->post('/admin/especiales/nuevo', EspecialAdminController::class, 'store');
$router->get('/admin/especiales/{id}/editar', EspecialAdminController::class, 'edit');
$router->post('/admin/especiales/{id}/editar', EspecialAdminController::class, 'update');
$router->post('/admin/especiales/{id}/eliminar', EspecialAdminController::class, 'delete');

$router->get('/admin/autores', AutorAdminController::class, 'index');
$router->get('/admin/autores/nuevo', AutorAdminController::class, 'create');
$router->post('/admin/autores/nuevo', AutorAdminController::class, 'store');
$router->get('/admin/autores/{id}/editar', AutorAdminController::class, 'edit');
$router->post('/admin/autores/{id}/editar', AutorAdminController::class, 'update');
$router->post('/admin/autores/{id}/eliminar', AutorAdminController::class, 'delete');

$router->get('/admin/paginas', PaginaAdminController::class, 'index');
$router->get('/admin/paginas/nuevo', PaginaAdminController::class, 'create');
$router->post('/admin/paginas/nuevo', PaginaAdminController::class, 'store');
$router->get('/admin/paginas/{id}/editar', PaginaAdminController::class, 'edit');
$router->post('/admin/paginas/{id}/editar', PaginaAdminController::class, 'update');
$router->post('/admin/paginas/{id}/eliminar', PaginaAdminController::class, 'delete');

$router->get('/admin/usuarios', UsuarioAdminController::class, 'index');
$router->get('/admin/usuarios/nuevo', UsuarioAdminController::class, 'create');
$router->post('/admin/usuarios/nuevo', UsuarioAdminController::class, 'store');
$router->get('/admin/usuarios/{id}/editar', UsuarioAdminController::class, 'edit');
$router->post('/admin/usuarios/{id}/editar', UsuarioAdminController::class, 'update');
$router->post('/admin/usuarios/{id}/eliminar', UsuarioAdminController::class, 'delete');
