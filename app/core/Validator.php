<?php
namespace App\Core;

/**
 * Clase Validator - Validación y sanitización de entradas
 */
class Validator {
    
    private array $errors = [];
    private array $data = [];
    
    /**
     * Valida un array de datos contra reglas definidas
     * Reglas: 'requerido', 'email', 'min:5', 'max:10', 'fecha', 'url', 'slug', 'enum:a,b,c', 'int', 'alfanumerico'
     */
    public function validate(array $data, array $rules): bool {
        $this->data = $data;
        $this->errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';
            if (!is_scalar($value)) {
                $value = '';
            }
            $fieldRules = explode('|', $fieldRules);
            
            foreach ($fieldRules as $rule) {
                $param = null;
                if (strpos($rule, ':') !== false) {
                    [$rule, $param] = explode(':', $rule, 2);
                }
                
                $method = 'validate' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    $this->$method($field, $value, $param);
                }
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Devuelve los errores de validación
     */
    public function errors(): array {
        return $this->errors;
    }
    
    /**
     * Devuelve los datos validados (sanitizados)
     */
    public function data(): array {
        return $this->data;
    }
    
    /**
     * Obtiene todos los errores como texto plano (para mostrar en una línea)
     */
    public function errorsAsString(): string {
        return implode(' | ', array_values($this->errors));
    }
    
    /* ======= Reglas de validación ======= */
    
    private function validateRequerido(string $field, $value): void {
        if (trim((string) $value) === '') {
            $this->errors[$field] = 'El campo ' . $this->label($field) . ' es obligatorio.';
        }
    }
    
    private function validateEmail(string $field, $value): void {
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Ingrese un correo electrónico válido.';
        }
    }
    
    private function validateMin(string $field, $value, ?string $param): void {
        $min = (int) $param;
        if (strlen((string) $value) < $min && (string) $value !== '') {
            $this->errors[$field] = 'El campo ' . $this->label($field) . ' debe tener al menos ' . $min . ' caracteres.';
        }
    }
    
    private function validateMax(string $field, $value, ?string $param): void {
        $max = (int) $param;
        if (strlen((string) $value) > $max) {
            $this->errors[$field] = 'El campo ' . $this->label($field) . ' no debe exceder ' . $max . ' caracteres.';
        }
    }
    
    private function validateFecha(string $field, $value): void {
        if ($value !== '' && !strtotime($value)) {
            $this->errors[$field] = 'Ingrese una fecha válida.';
        }
    }
    
    private function validateUrl(string $field, $value): void {
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errors[$field] = 'Ingrese una URL válida.';
        }
    }
    
    private function validateSlug(string $field, $value): void {
        if ($value !== '' && !preg_match('/^[a-z0-9-]+$/', $value)) {
            $this->errors[$field] = 'El slug solo puede contener letras minúsculas, números y guiones.';
        }
    }
    
    private function validateEnum(string $field, $value, ?string $param): void {
        if ($param !== null) {
            $allowed = explode(',', $param);
            if ($value !== '' && !in_array($value, $allowed)) {
                $this->errors[$field] = 'Valor no permitido para ' . $this->label($field) . '.';
            }
        }
    }
    
    private function validateInt(string $field, $value): void {
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$field] = 'El campo ' . $this->label($field) . ' debe ser un número entero.';
        }
    }
    
    private function validateAlfanumerico(string $field, $value): void {
        if ($value !== '' && !preg_match('/^[a-zA-Z0-9\sáéíóúñüÁÉÍÓÚÑÜ.,¿?¡!()-]+$/u', $value)) {
            $this->errors[$field] = 'El campo ' . $this->label($field) . ' solo admite letras y números.';
        }
    }
    
    /**
     * Genera nombre legible para el campo
     */
    private function label(string $field): string {
        return str_replace('_', ' ', $field);
    }
    
    /**
     * Sanea un texto para prevenir XSS
     */
    public static function sanitize(mixed $text): string {
        if (!is_scalar($text)) {
            return '';
        }
        return htmlspecialchars(strip_tags(trim((string) $text)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanea texto permitiendo HTML básico del editor (p, h2, h3, img, blockquote, etc.)
     */
    public static function sanitizeHtml(mixed $html): string {
        if (!is_scalar($html)) {
            return '';
        }
        $allowed = '<p><br><h2><h3><h4><blockquote><strong><b><em><i><ul><ol><li><a><img><figure><figcaption>';
        $cleaned = strip_tags((string) $html, $allowed);
        $cleaned = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/is', '', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:.*?\2/is', '$1=$2#$2', $cleaned) ?? $cleaned;
        return $cleaned;
    }
}