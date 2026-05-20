<?php

namespace App\Services\Tests;

use RuntimeException;
use ZipArchive;

class LatexImportArchiveExtractor
{
    private const BASE_TMP_PATH = 'app/tmp/latex-imports';
    private const MAX_ARCHIVE_SIZE_BYTES = 25 * 1024 * 1024;
    private const MAX_IMAGE_SIZE_BYTES = 2048 * 1024;
    private const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif'];

    /**
     * Extract a LaTeX ZIP archive into private temporary storage.
     */
    public function extract(string $zipPath): array
    {
        $result = [
            'tex_path' => null,
            'root_path' => null,
            'files' => [],
            'images' => [],
            'errors' => [],
            'warnings' => [],
        ];

        if (!is_file($zipPath) || !is_readable($zipPath)) {
            $result['errors'][] = 'ZIP file is missing or not readable.';
            return $result;
        }

        if (filesize($zipPath) > self::MAX_ARCHIVE_SIZE_BYTES) {
            $result['errors'][] = 'ZIP file is larger than the 25 MB limit.';
            return $result;
        }

        $zip = new ZipArchive();
        $opened = $zip->open($zipPath);

        if ($opened !== true) {
            $result['errors'][] = 'Unable to open ZIP archive.';
            return $result;
        }

        try {
            $entries = $this->inspectEntries($zip, $result['errors'], $result['warnings']);

            if (!empty($result['errors'])) {
                return $result;
            }

            $texEntries = array_values(array_filter($entries, static fn (array $entry): bool => $entry['type'] === 'tex'));

            if (count($texEntries) === 0) {
                $result['errors'][] = 'ZIP archive must contain exactly one .tex file.';
                return $result;
            }

            if (count($texEntries) > 1) {
                $result['errors'][] = 'ZIP archive must contain exactly one .tex file.';
                return $result;
            }

            $rootPath = $this->makeExtractionRoot();
            $result['root_path'] = $rootPath;

            foreach ($entries as $entry) {
                $targetPath = $this->joinPaths($rootPath, $entry['path']);
                $this->ensureDirectory(dirname($targetPath));

                $stream = $zip->getStream($entry['zip_name']);

                if ($stream === false) {
                    throw new RuntimeException("Unable to read ZIP entry '{$entry['zip_name']}'.");
                }

                $target = fopen($targetPath, 'wb');

                if ($target === false) {
                    fclose($stream);
                    throw new RuntimeException("Unable to write extracted file '{$entry['path']}'.");
                }

                stream_copy_to_stream($stream, $target);
                fclose($stream);
                fclose($target);

                $file = [
                    'path' => $entry['path'],
                    'extracted_path' => $targetPath,
                    'extension' => $entry['extension'],
                    'size' => $entry['size'],
                ];

                $result['files'][] = $file;

                if ($entry['type'] === 'tex') {
                    $result['tex_path'] = $targetPath;
                }

                if ($entry['type'] === 'image') {
                    $result['images'][$entry['path']] = $file;
                }
            }
        } catch (\Throwable $e) {
            $result['errors'][] = $e->getMessage();

            if (!empty($result['root_path'])) {
                $this->cleanup($result['root_path']);
                $result['root_path'] = null;
                $result['tex_path'] = null;
                $result['files'] = [];
                $result['images'] = [];
            }
        } finally {
            $zip->close();
        }

        return $result;
    }

    /**
     * Delete an extracted temporary directory created by this service.
     */
    public function cleanup(?string $rootPath): bool
    {
        if ($rootPath === null || $rootPath === '') {
            return false;
        }

        $basePath = $this->baseTmpPath();
        $realRoot = realpath($rootPath);
        $realBase = realpath($basePath);

        if ($realRoot === false || $realBase === false || $realRoot === $realBase || !$this->isPathInside($realRoot, $realBase)) {
            return false;
        }

        return $this->deleteDirectory($realRoot);
    }

    private function inspectEntries(ZipArchive $zip, array &$errors, array &$warnings): array
    {
        $entries = [];
        $seenPaths = [];
        $totalSize = 0;

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $stat = $zip->statIndex($index);

            if ($stat === false || !isset($stat['name'])) {
                $errors[] = "Unable to inspect ZIP entry at index {$index}.";
                continue;
            }

            $zipName = $stat['name'];
            $normalizedPath = $this->normalizeArchivePath($zipName);

            if ($normalizedPath === null) {
                $errors[] = "Unsafe ZIP entry path '{$zipName}'.";
                continue;
            }

            if (str_ends_with($zipName, '/')) {
                continue;
            }

            if ($this->isHiddenOrSystemPath($normalizedPath)) {
                $errors[] = "Hidden/system ZIP entry '{$zipName}' is not allowed.";
                continue;
            }

            if ($this->isSymlink($zip, $index)) {
                $errors[] = "Symlink ZIP entry '{$zipName}' is not allowed.";
                continue;
            }

            $extension = strtolower(pathinfo($normalizedPath, PATHINFO_EXTENSION));
            $size = (int) ($stat['size'] ?? 0);
            $totalSize += $size;

            if ($totalSize > self::MAX_ARCHIVE_SIZE_BYTES) {
                $errors[] = 'ZIP archive extracted size is larger than the 25 MB limit.';
                continue;
            }

            if ($extension === 'zip') {
                $errors[] = "Nested ZIP file '{$normalizedPath}' is not allowed.";
                continue;
            }

            $type = null;

            if ($extension === 'tex') {
                $type = 'tex';
            } elseif (in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS, true)) {
                $type = 'image';

                if ($size > self::MAX_IMAGE_SIZE_BYTES) {
                    $errors[] = "Image '{$normalizedPath}' is larger than the 2048 KB limit.";
                    continue;
                }
            } else {
                $warnings[] = "Ignoring unsupported ZIP entry '{$normalizedPath}'.";
                continue;
            }

            if (isset($seenPaths[$normalizedPath])) {
                $errors[] = "Duplicate ZIP entry path '{$normalizedPath}' is not allowed.";
                continue;
            }

            $seenPaths[$normalizedPath] = true;
            $entries[] = [
                'zip_name' => $zipName,
                'path' => $normalizedPath,
                'extension' => $extension,
                'size' => $size,
                'type' => $type,
            ];
        }

        usort($entries, static function (array $a, array $b): int {
            if ($a['type'] === 'tex' && basename($a['path']) === 'test.tex') {
                return -1;
            }

            if ($b['type'] === 'tex' && basename($b['path']) === 'test.tex') {
                return 1;
            }

            return $a['path'] <=> $b['path'];
        });

        return $entries;
    }

    private function normalizeArchivePath(string $path): ?string
    {
        $path = trim($path);

        if ($path === '' || str_contains($path, '\\')) {
            return null;
        }

        if (str_starts_with($path, '/') || preg_match('/^[A-Za-z]:\//', $path) === 1) {
            return null;
        }

        $segments = explode('/', $path);
        $normalizedSegments = [];

        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }

            if ($segment === '..') {
                return null;
            }

            $normalizedSegments[] = $segment;
        }

        if (empty($normalizedSegments)) {
            return null;
        }

        return implode('/', $normalizedSegments);
    }

    private function isHiddenOrSystemPath(string $path): bool
    {
        $segments = explode('/', $path);
        $fileName = strtolower(basename($path));

        if ($fileName === 'thumbs.db' || $fileName === 'desktop.ini') {
            return true;
        }

        foreach ($segments as $segment) {
            if ($segment === '__MACOSX' || str_starts_with($segment, '.')) {
                return true;
            }
        }

        return false;
    }

    private function isSymlink(ZipArchive $zip, int $index): bool
    {
        if (!method_exists($zip, 'getExternalAttributesIndex')) {
            return false;
        }

        $attributes = 0;
        $operatingSystem = 0;

        if (!$zip->getExternalAttributesIndex($index, $operatingSystem, $attributes)) {
            return false;
        }

        if ($operatingSystem !== ZipArchive::OPSYS_UNIX) {
            return false;
        }

        return (($attributes >> 16) & 0170000) === 0120000;
    }

    private function makeExtractionRoot(): string
    {
        $basePath = $this->baseTmpPath();
        $this->ensureDirectory($basePath);

        $rootPath = $this->joinPaths($basePath, date('YmdHis') . '-' . bin2hex(random_bytes(8)));
        $this->ensureDirectory($rootPath);

        return $rootPath;
    }

    private function baseTmpPath(): string
    {
        return storage_path(self::BASE_TMP_PATH);
    }

    private function ensureDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0755, true) && !is_dir($path)) {
            throw new RuntimeException("Unable to create directory '{$path}'.");
        }
    }

    private function joinPaths(string $left, string $right): string
    {
        return rtrim($left, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $right);
    }

    private function isPathInside(string $path, string $basePath): bool
    {
        $basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return str_starts_with($path . DIRECTORY_SEPARATOR, $basePath);
    }

    private function deleteDirectory(string $path): bool
    {
        if (!is_dir($path)) {
            return false;
        }

        $items = scandir($path);

        if ($items === false) {
            return false;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $path . DIRECTORY_SEPARATOR . $item;

            if (is_dir($itemPath) && !is_link($itemPath)) {
                $this->deleteDirectory($itemPath);
                continue;
            }

            unlink($itemPath);
        }

        return rmdir($path);
    }
}
