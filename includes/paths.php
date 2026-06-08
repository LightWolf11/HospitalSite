<?php
declare(strict_types=1);


function public_href(string $path, string $navBase): string
{
    if ($navBase === '') {
        return $path;
    }
    if (strpos($path, 'pages/') === 0) {
        return substr($path, 6);
    }
    return '../' . $path;
}
