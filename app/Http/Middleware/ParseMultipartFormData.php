<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * PHP only auto-parses multipart/form-data for POST requests.
 * For PUT/PATCH, php://input is available but PHP never populates $_POST or $_FILES.
 * This middleware manually parses the raw multipart body for PUT/PATCH so that
 * both text fields and file uploads work correctly (Postman + Flutter).
 */
class ParseMultipartFormData
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->method(), ['PUT', 'PATCH'])) {
            return $next($request);
        }

        $contentType = $request->header('Content-Type', '');

        if (! str_contains(strtolower($contentType), 'multipart/form-data')) {
            return $next($request);
        }

        if (! preg_match('/boundary=(.+)$/i', $contentType, $matches)) {
            return $next($request);
        }

        $boundary = trim($matches[1]);
        $rawBody  = file_get_contents('php://input');

        if (empty($rawBody)) {
            return $next($request);
        }

        ['fields' => $fields, 'files' => $files] = $this->parseMultipart($rawBody, $boundary);

        if (! empty($fields)) {
            $request->merge($fields);
        }

        foreach ($files as $key => $fileData) {
            $tmp = tempnam(sys_get_temp_dir(), 'upload_');
            file_put_contents($tmp, $fileData['content']);

            $request->files->set($key, new UploadedFile(
                $tmp,
                $fileData['filename'],
                $fileData['mime_type'],
                null,
                true
            ));
        }

        return $next($request);
    }

    private function parseMultipart(string $body, string $boundary): array
    {
        $fields = [];
        $files  = [];

        $parts = explode('--' . $boundary, $body);

        foreach ($parts as $part) {
            $part = ltrim($part, "\r\n");

            if ($part === '' || str_starts_with($part, '--')) {
                continue;
            }

            $sep = strpos($part, "\r\n\r\n");
            if ($sep === false) {
                continue;
            }

            $headers = substr($part, 0, $sep);
            $content = rtrim(substr($part, $sep + 4), "\r\n");

            if (! preg_match('/Content-Disposition:[^\r\n]*name="([^"]+)"/i', $headers, $nameMatch)) {
                continue;
            }

            $name = $nameMatch[1];

            if (preg_match('/Content-Disposition:[^\r\n]*filename="([^"]*)"/i', $headers, $fileMatch)) {
                $filename = $fileMatch[1];

                if ($filename === '') {
                    continue;
                }

                $mime = 'application/octet-stream';
                if (preg_match('/Content-Type:\s*([^\r\n]+)/i', $headers, $mimeMatch)) {
                    $mime = trim($mimeMatch[1]);
                }

                $files[$name] = [
                    'filename'  => $filename,
                    'mime_type' => $mime,
                    'content'   => $content,
                ];
            } else {
                $fields[$name] = $content;
            }
        }

        return ['fields' => $fields, 'files' => $files];
    }
}
