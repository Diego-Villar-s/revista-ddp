<?php
namespace App\Core;

/**
 * Procesamiento de archivos. La BD solo recibe rutas relativas a uploads/.
 */
class MediaProcessor
{
    /** @var string[] */
    private array $errors = [];
    private ?bool $ffmpegAvailable = null;

    public function __construct()
    {
        $this->errors = [];
    }

    /**
     * Valida y optimiza una imagen. Si GD no está disponible, conserva el
     * original para que el panel no pierda una subida válida.
     *
     * @return array{ruta_original:string,ruta_thumb:string,ruta_webp:string}|null
     */
    public function processImage(array $file, string $subfolder, string $customName = ''): ?array
    {
        $this->errors = [];
        if (!$this->validUpload($file)) {
            return null;
        }
        if ((int) $file['size'] > MAX_IMAGE_SIZE) {
            $this->errors[] = 'La imagen supera el tamaño máximo permitido.';
            return null;
        }

        $mime = $this->mime($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($mime, $allowed, true)) {
            $this->errors[] = 'Tipo de archivo de imagen no permitido (' . $mime . ').';
            return null;
        }

        $specialReportaje = $subfolder === 'reportajes';
        $baseDir = UPLOADS_PATH . '/' . trim($subfolder, '/');
        $originalDir = $specialReportaje ? $baseDir . '/original' : $baseDir;
        $thumbDir = $specialReportaje ? $baseDir . '/thumb' : $baseDir;
        $webpDir = $specialReportaje ? $baseDir . '/webp' : $baseDir;
        foreach ([$baseDir, $originalDir, $thumbDir, $webpDir] as $directory) {
            $this->ensureDir($directory);
        }

        $name = $this->uniqueName($customName ?: pathinfo((string) $file['name'], PATHINFO_FILENAME));
        $originalExtension = $this->extensionFromImageMime($mime);
        $originalPath = $originalDir . '/' . $name . '.' . $originalExtension;
        $thumbPath = $thumbDir . '/' . $name . '_thumb.' . $originalExtension;
        $webpPath = $webpDir . '/' . $name . '.webp';
        $prefixOriginal = $specialReportaje ? 'reportajes/original' : trim($subfolder, '/');
        $prefixThumb = $specialReportaje ? 'reportajes/thumb' : trim($subfolder, '/');
        $prefixWebp = $specialReportaje ? 'reportajes/webp' : trim($subfolder, '/');

        $image = $this->loadImage((string) $file['tmp_name'], $mime);
        if (!$image || !function_exists('imagecreatetruecolor')) {
            if (!$this->moveOriginal($file, $originalPath)) {
                $this->errors[] = 'No se pudo guardar la imagen original.';
                return null;
            }
            return [
                'ruta_original' => $prefixOriginal . '/' . $name . '.' . $originalExtension,
                'ruta_thumb' => $prefixOriginal . '/' . $name . '.' . $originalExtension,
                'ruta_webp' => $prefixOriginal . '/' . $name . '.' . $originalExtension,
            ];
        }

        $original = $this->resizeToWidth($image, IMAGE_MAX_WIDTH);
        $thumb = $this->resizeToWidth($image, IMAGE_THUMB_WIDTH);
        if (!$this->saveImage($original, $originalPath, $originalExtension) || !$this->saveImage($thumb, $thumbPath, $originalExtension)) {
            $this->errors[] = 'No se pudieron guardar las versiones optimizadas.';
            return null;
        }

        $webpRelative = $prefixWebp . '/' . $name . '.webp';
        if (function_exists('imagewebp')) {
            $webpImage = $this->resizeToWidth($image, 1280);
            if (imagewebp($webpImage, $webpPath, IMAGE_QUALITY)) {
                $webpRelative = $prefixWebp . '/' . $name . '.webp';
            } else {
                $webpRelative = $prefixThumb . '/' . $name . '_thumb.' . $originalExtension;
            }
            $this->destroyImage($webpImage);
        } else {
            $webpRelative = $prefixThumb . '/' . $name . '_thumb.' . $originalExtension;
        }

        $this->destroyImage($original);
        $this->destroyImage($thumb);
        $this->destroyImage($image);
        return [
            'ruta_original' => $prefixOriginal . '/' . $name . '.' . $originalExtension,
            'ruta_thumb' => $prefixThumb . '/' . $name . '_thumb.' . $originalExtension,
            'ruta_webp' => $webpRelative,
        ];
    }

    public function processAudio(array $file, string $subfolder, string $customName = ''): ?string
    {
        $this->errors = [];
        if (!$this->validUpload($file)) {
            return null;
        }
        if ((int) $file['size'] > MAX_AUDIO_SIZE) {
            $this->errors[] = 'El audio supera el tamaño máximo permitido.';
            return null;
        }
        $mime = $this->mime($file['tmp_name']);
        if (!in_array($mime, ['audio/mpeg', 'audio/mp3', 'audio/ogg', 'audio/wav', 'audio/x-wav', 'audio/webm'], true)) {
            $this->errors[] = 'Tipo de archivo de audio no permitido (' . $mime . ').';
            return null;
        }

        $baseDir = UPLOADS_PATH . '/' . trim($subfolder, '/');
        $this->ensureDir($baseDir);
        $name = $this->uniqueName($customName ?: pathinfo((string) $file['name'], PATHINFO_FILENAME));
        if ($this->hasFfmpeg()) {
            $outputPath = $baseDir . '/' . $name . '.mp3';
            $command = sprintf(
                'ffmpeg -y -i %s -vn -acodec libmp3lame -b:a 128k %s 2>&1',
                escapeshellarg((string) $file['tmp_name']),
                escapeshellarg($outputPath)
            );
            shell_exec($command);
            if (is_file($outputPath)) {
                return trim($subfolder, '/') . '/' . $name . '.mp3';
            }
        }

        $extension = $this->extensionFromMime($mime, 'mp3');
        $destination = $baseDir . '/' . $name . '.' . $extension;
        if ($this->moveOriginal($file, $destination)) {
            return trim($subfolder, '/') . '/' . $name . '.' . $extension;
        }
        $this->errors[] = 'No se pudo guardar el archivo de audio.';
        return null;
    }

    /** @return array{ruta_video:string,poster:?string}|null */
    public function processVideo(array $file, string $subfolder, string $customName = ''): ?array
    {
        $this->errors = [];
        if (!$this->validUpload($file)) {
            return null;
        }
        if ((int) $file['size'] > MAX_VIDEO_SIZE) {
            $this->errors[] = 'El video supera el tamaño máximo permitido.';
            return null;
        }
        $mime = $this->mime($file['tmp_name']);
        if (!in_array($mime, ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'], true)) {
            $this->errors[] = 'Tipo de archivo de video no permitido (' . $mime . ').';
            return null;
        }

        $baseDir = UPLOADS_PATH . '/' . trim($subfolder, '/');
        $posterDir = UPLOADS_PATH . '/videos/poster';
        $this->ensureDir($baseDir);
        $this->ensureDir($posterDir);
        $name = $this->uniqueName($customName ?: pathinfo((string) $file['name'], PATHINFO_FILENAME));

        if ($this->hasFfmpeg()) {
            $outputPath = $baseDir . '/' . $name . '.mp4';
            $posterPath = $posterDir . '/' . $name . '_poster.jpg';
            $command = sprintf(
                'ffmpeg -y -i %s -vf "scale=1280:720:force_original_aspect_ratio=decrease" -c:v libx264 -preset fast -crf 23 -profile:v main -pix_fmt yuv420p -c:a aac -b:a 128k %s 2>&1',
                escapeshellarg((string) $file['tmp_name']),
                escapeshellarg($outputPath)
            );
            shell_exec($command);
            if (is_file($outputPath)) {
                shell_exec(sprintf(
                    'ffmpeg -y -i %s -ss 00:00:01 -vframes 1 -q:v 3 %s 2>&1',
                    escapeshellarg($outputPath),
                    escapeshellarg($posterPath)
                ));
                return [
                    'ruta_video' => trim($subfolder, '/') . '/' . $name . '.mp4',
                    'poster' => is_file($posterPath) ? 'videos/poster/' . $name . '_poster.jpg' : null,
                ];
            }
        }

        $extension = $this->extensionFromMime($mime, 'mp4');
        $destination = $baseDir . '/' . $name . '.' . $extension;
        if ($this->moveOriginal($file, $destination)) {
            return ['ruta_video' => trim($subfolder, '/') . '/' . $name . '.' . $extension, 'poster' => null];
        }
        $this->errors[] = 'No se pudo guardar el archivo de video.';
        return null;
    }

    public function processPdf(array $file, string $subfolder, string $customName = ''): ?string
    {
        $this->errors = [];
        if (!$this->validUpload($file)) {
            return null;
        }
        if ((int) $file['size'] > MAX_PDF_SIZE) {
            $this->errors[] = 'El PDF supera el tamaño máximo permitido.';
            return null;
        }
        if ($this->mime($file['tmp_name']) !== 'application/pdf') {
            $this->errors[] = 'El archivo debe ser un PDF válido.';
            return null;
        }
        $baseDir = UPLOADS_PATH . '/' . trim($subfolder, '/');
        $this->ensureDir($baseDir);
        $name = $this->uniqueName($customName ?: pathinfo((string) $file['name'], PATHINFO_FILENAME));
        $destination = $baseDir . '/' . $name . '.pdf';
        if ($this->moveOriginal($file, $destination)) {
            return trim($subfolder, '/') . '/' . $name . '.pdf';
        }
        $this->errors[] = 'No se pudo guardar el PDF.';
        return null;
    }

    /** @return string[] */
    public function errors(): array
    {
        return $this->errors;
    }

    public function hasFfmpeg(): bool
    {
        if ($this->ffmpegAvailable !== null) {
            return $this->ffmpegAvailable;
        }
        if (!function_exists('shell_exec')) {
            $this->ffmpegAvailable = false;
            return false;
        }
        $output = shell_exec('ffmpeg -version 2>&1');
        $this->ffmpegAvailable = is_string($output) && strpos($output, 'ffmpeg version') !== false;
        return $this->ffmpegAvailable;
    }

    private function validUpload(array $file): bool
    {
        if (!isset($file['tmp_name'], $file['error']) || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file((string) $file['tmp_name'])) {
            $this->errors[] = 'No se recibió un archivo válido.';
            return false;
        }
        return true;
    }

    private function mime(string $path): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return (string) $finfo->file($path);
    }

    private function uniqueName(string $name): string
    {
        $name = mb_strtolower(trim($name), 'UTF-8');
        $name = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'u', 'n'], $name);
        $name = preg_replace('/[^a-z0-9-]/', '-', $name) ?? '';
        $name = trim(preg_replace('/-+/', '-', $name) ?? '', '-');
        return substr($name ?: 'archivo', 0, 60) . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(3));
    }

    private function moveOriginal(array $file, string $destination): bool
    {
        return is_uploaded_file((string) $file['tmp_name']) && move_uploaded_file((string) $file['tmp_name'], $destination);
    }

    private function loadImage(string $path, string $mime)
    {
        if (!function_exists('imagecreatefromjpeg')) {
            return null;
        }
        return match ($mime) {
            'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($path) : null,
            'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : null,
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            'image/gif' => function_exists('imagecreatefromgif') ? @imagecreatefromgif($path) : null,
            default => null,
        };
    }

    private function resizeToWidth($image, int $maxWidth)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $newWidth = min($width, $maxWidth);
        $newHeight = max(1, (int) round($newWidth * ($height / $width)));
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        return $resized;
    }

    private function saveImage($image, string $path, string $format = 'jpg'): bool
    {
        return match ($format) {
            'png' => function_exists('imagepng') && imagepng($image, $path, 8),
            'webp' => function_exists('imagewebp') && imagewebp($image, $path, IMAGE_QUALITY),
            'gif' => function_exists('imagegif') && imagegif($image, $path),
            default => function_exists('imagejpeg') && imagejpeg($image, $path, IMAGE_QUALITY),
        };
    }

    private function destroyImage($image): void
    {
        if (is_object($image) && function_exists('imagedestroy')) {
            imagedestroy($image);
        }
    }

    private function extensionFromImageMime(string $mime): string
    {
        return match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };
    }

    private function extensionFromMime(string $mime, string $default): string
    {
        return match ($mime) {
            'audio/mpeg', 'audio/mp3' => 'mp3',
            'audio/ogg' => 'ogg',
            'audio/wav', 'audio/x-wav' => 'wav',
            'audio/webm' => 'weba',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'video/ogg' => 'ogv',
            'video/quicktime' => 'mov',
            default => $default,
        };
    }

    private function ensureDir(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0775, true) && !is_dir($path)) {
            throw new \RuntimeException('No se pudo crear el directorio de subidas: ' . $path);
        }
    }
}
