<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Boletin;
use App\Models\Pagina;
use App\Models\Podcast;
use App\Models\Reportaje;
use App\Models\Video;

class SitemapController extends Controller
{
    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $base = $this->absoluteBaseUrl();
        $reportajes = (new Reportaje())->getUltimos(500);
        $boletines = (new Boletin())->getAllPublic();
        $podcasts = (new Podcast())->getAllPublic();
        $videos = (new Video())->getAllPublic();
        $paginas = (new Pagina())->getActive();

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $pages = ['', '/reportajes', '/noticias', '/boletines', '/podcast', '/videos', '/sobre-nosotros', '/alianzas', '/contacto'];
        foreach ($pages as $page) {
            $loc = $base . ($page === '' ? '/' : $page);
            echo '  <url><loc>' . $this->xml($loc) . '</loc><changefreq>daily</changefreq><priority>0.8</priority></url>' . "\n";
        }
        foreach ($reportajes as $row) {
            $lastmod = substr((string) ($row['updated_at'] ?? $row['fecha_publicacion'] ?? ''), 0, 10);
            echo '  <url><loc>' . $this->xml($base . '/reportajes/' . rawurlencode((string) $row['slug'])) . '</loc>';
            if ($lastmod !== '') {
                echo '<lastmod>' . $this->xml($lastmod) . '</lastmod>';
            }
            echo '<priority>0.9</priority></url>' . "\n";
        }
        foreach ($boletines as $row) {
            echo '  <url><loc>' . $this->xml($base . '/boletines/' . rawurlencode((string) $row['numero_boletin'])) . '</loc><priority>0.6</priority></url>' . "\n";
        }
        foreach ($podcasts as $row) {
            echo '  <url><loc>' . $this->xml($base . '/podcast/' . rawurlencode((string) $row['slug'])) . '</loc><priority>0.6</priority></url>' . "\n";
        }
        foreach ($videos as $row) {
            echo '  <url><loc>' . $this->xml($base . '/videos/' . rawurlencode((string) $row['slug'])) . '</loc><priority>0.6</priority></url>' . "\n";
        }
        foreach ($paginas as $row) {
            if (in_array($row['slug'], ['contacto', 'sobre-nosotros', 'alianzas'], true)) {
                echo '  <url><loc>' . $this->xml($base . '/' . rawurlencode((string) $row['slug'])) . '</loc><priority>0.5</priority></url>' . "\n";
            }
        }
        echo '</urlset>';
        exit;
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\n";
        echo "Disallow: /admin/\n";
        echo "Allow: /\n\n";
        echo 'Sitemap: ' . $this->absoluteBaseUrl() . "/sitemap.xml\n";
        exit;
    }

    private function absoluteBaseUrl(): string
    {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $https ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $host = preg_replace('/[^A-Za-z0-9.\-:\[\]]/', '', $host) ?: 'localhost';
        return $scheme . '://' . $host . rtrim(BASE_URL, '/');
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
