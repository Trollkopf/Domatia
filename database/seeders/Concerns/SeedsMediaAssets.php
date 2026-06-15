<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait SeedsMediaAssets
{
    protected function normalizedAssetFiles(string $directory): array
    {
        if (! File::isDirectory($directory)) {
            return [];
        }

        return collect(File::files($directory))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))
            ->sortBy(fn ($file) => $file->getFilename())
            ->values()
            ->all();
    }

    protected function existingPublicAssets(string $relativePath): array
    {
        $directory = storage_path('app/public/' . trim($relativePath, '/'));

        return collect($this->normalizedAssetFiles($directory))
            ->sortBy(fn ($file) => $file->getFilename())
            ->map(fn ($file) => trim($relativePath, '/') . '/' . $file->getFilename())
            ->values()
            ->all();
    }

    protected function publishSeedAssets(string $sourceRelativePath, string $destinationRelativePath): array
    {
        $sourcePath = database_path('seeders/assets/' . trim($sourceRelativePath, '/'));
        $files = collect($this->normalizedAssetFiles($sourcePath));

        if ($files->isEmpty()) {
            return [];
        }

        return $files->map(function ($file) use ($destinationRelativePath) {
            $filename = Str::slug(pathinfo($file->getFilename(), PATHINFO_FILENAME))
                . '.'
                . strtolower($file->getExtension());

            $relativePath = trim($destinationRelativePath, '/') . '/' . $filename;

            Storage::disk('public')->put($relativePath, File::get($file->getPathname()));

            return $relativePath;
        })->all();
    }

    protected function publishResourceAssets(string $sourceRelativePath, string $destinationRelativePath): array
    {
        $sourcePath = resource_path(trim($sourceRelativePath, '/'));
        $files = collect($this->normalizedAssetFiles($sourcePath));

        if ($files->isEmpty()) {
            return [];
        }

        return $files->map(function ($file) use ($destinationRelativePath) {
            $filename = Str::slug(pathinfo($file->getFilename(), PATHINFO_FILENAME))
                . '.'
                . strtolower($file->getExtension());

            $relativePath = trim($destinationRelativePath, '/') . '/' . $filename;

            Storage::disk('public')->put($relativePath, File::get($file->getPathname()));

            return $relativePath;
        })->all();
    }

    protected function cycleSeedImage(array $images, int $index): ?string
    {
        if ($images === []) {
            return null;
        }

        return $images[$index % count($images)];
    }

    protected function mergeSeedImagePools(array ...$pools): array
    {
        return collect($pools)
            ->flatten(1)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
