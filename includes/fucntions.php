<?php
declare(strict_types=1);

function h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_out(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function require_post_json(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '[]', true);
    return is_array($data) ? $data : [];
}

function upload_php_err_message(int $code): string
{
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return 'Файл слишком большой (лимит сервера или формы).';
        case UPLOAD_ERR_PARTIAL:
            return 'Файл загружен не полностью.';
        case UPLOAD_ERR_NO_FILE:
            return 'Файл не выбран.';
        case UPLOAD_ERR_NO_TMP_DIR:
        case UPLOAD_ERR_CANT_WRITE:
        case UPLOAD_ERR_EXTENSION:
            return 'Ошибка сервера при загрузке (права на tmp или отключены загрузки в PHP).';
        default:
            return 'Не удалось загрузить файл (код ' . $code . ').';
    }
}


function upload_image_or_throw(array $file, string $subdir, array $config): string
{
    if (empty($file['tmp_name'])) {
        throw new RuntimeException('Файл не получен.');
    }
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('Выберите файл.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException(upload_php_err_message((int) $file['error']));
    }

    $max = (int) ($config['upload']['max_bytes'] ?? 5000000);
    if (($file['size'] ?? 0) > $max) {
        throw new RuntimeException('Файл больше ' . round($max / 1024 / 1024, 1) . ' МБ.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']) ?: '';
    $allowedCfg = $config['upload']['allowed_mime'] ?? [];
    $allowed = array_unique(array_merge(
        ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp', 'image/gif'],
        is_array($allowedCfg) ? $allowedCfg : []
    ));

    if (!in_array($mime, $allowed, true)) {
        $imgInfo = @getimagesize($file['tmp_name']);
        if ($imgInfo && isset($imgInfo['mime']) && in_array($imgInfo['mime'], $allowed, true)) {
            $mime = $imgInfo['mime'];
        }
    }

    if (!in_array($mime, $allowed, true)) {
        throw new RuntimeException(
            'Недопустимый тип файла (' . ($mime ?: 'не определён') . '). Используйте JPEG, PNG, WebP или GIF.'
        );
    }

    if (@getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('Файл не похож на изображение.');
    }

    $extMap = [
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
    $ext = $extMap[$mime] ?? null;
    if ($ext === null) {
        throw new RuntimeException('Не удалось определить расширение для типа ' . $mime . '.');
    }

    $base = dirname(__DIR__) . '/uploads/' . trim($subdir, '/');
    if (!is_dir($base) && !@mkdir($base, 0755, true) && !is_dir($base)) {
        throw new RuntimeException(
            'Не удалось создать папку uploads/' . trim($subdir, '/') . '. Проверьте права на каталог сайта.'
        );
    }

    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = $base . '/' . $name;
    if (!is_writable($base)) {
        throw new RuntimeException('Папка uploads недоступна для записи. Выставьте права на каталог uploads/.');
    }
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Не удалось сохранить файл. Проверьте права на uploads/ и open_basedir в PHP.');
    }

    return 'uploads/' . trim($subdir, '/') . '/' . $name;
}

function upload_image(array $file, string $subdir, array $config): ?string
{
    try {
        if (empty($file['tmp_name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        return upload_image_or_throw($file, $subdir, $config);
    } catch (Throwable $e) {
        return null;
    }
}

function upload_pdf(array $file, string $subdir, array $config): ?string
{
    if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $max = (int) ($config['upload']['max_bytes'] ?? 5000000);
    if ($file['size'] > $max) {
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if ($mime !== 'application/pdf') {
        return null;
    }
    $base = dirname(__DIR__) . '/uploads/' . trim($subdir, '/');
    if (!is_dir($base)) {
        mkdir($base, 0755, true);
    }
    $name = bin2hex(random_bytes(16)) . '.pdf';
    $dest = $base . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }
    return 'uploads/' . trim($subdir, '/') . '/' . $name;
}

function base_path(): string
{
    return dirname(__DIR__);
}

function public_upload_path(?string $relative, ?array $config = null): string
{
    if ($relative === null || $relative === '') {
        return '';
    }
    $path = ltrim(str_replace('\\', '/', $relative), '/');

    $prefix = '';
    if ($config !== null) {
        $manual = trim((string) ($config['app']['public_path'] ?? ''), '/');
        if ($manual !== '') {
            $prefix = '/' . $manual;
        } else {
            $bu = trim((string) ($config['app']['base_url'] ?? ''));
            if ($bu !== '') {
                $pathPart = parse_url($bu, PHP_URL_PATH);
                if (is_string($pathPart)) {
                    $pathPart = trim($pathPart, '/');
                    if ($pathPart !== '') {
                        $prefix = '/' . $pathPart;
                    }
                }
            }
        }
    }

    return ($prefix === '' ? '' : $prefix) . '/' . $path;
}

function public_url(string $path, array $config): string
{
    $base = rtrim((string) ($config['app']['base_url'] ?? ''), '/');
    $path = ltrim($path, '/');
    return $base . '/' . $path;
}
