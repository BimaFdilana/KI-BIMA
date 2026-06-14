<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarService
{
    /**
     * Get all available default avatar SVG files.
     *
     * @return array
     */
    public function getDefaultAvatars(): array
    {
        $files = Storage::disk('public')->files('avatar_user/default');
        return array_filter($files, function ($file) {
            return Str::endsWith($file, '.svg');
        });
    }

    /**
     * Get a random default avatar path.
     *
     * @return string
     */
    public function getRandomDefaultAvatar(): string
    {
        $avatars = $this->getDefaultAvatars();
        if (empty($avatars)) {
            throw new \Exception('No default avatars found');
        }
        return $avatars[array_rand($avatars)];
    }

    /**
     * Generate custom colored avatar from SVG and save it.
     *
     * @param string $avatarName
     * @param string $color
     * @param int $userId
     * @return string
     */
    public function generateCustomAvatar(string $avatarName, string $color, int $userId): string
    {
        // Ensure color is a valid hex color
        if (!preg_match('/^#[a-f0-9]{6}$/i', $color)) {
            $color = '#' . substr(md5($color), 0, 6); // Generate a color if invalid
        }

        // Get the SVG content
        $svgPath = "avatar_user/default/{$avatarName}";
        if (!Storage::disk('public')->exists($svgPath)) {
            throw new \Exception('Avatar template not found');
        }

        $svgContent = Storage::disk('public')->get($svgPath);

        // Replace the black color (#000) with the custom color
        $customSvgContent = str_replace(['#000', 'black'], $color, $svgContent);

        // Save the custom avatar
        $customAvatarPath = "avatar_user/custom/{$userId}_{$avatarName}";
        Storage::disk('public')->put($customAvatarPath, $customSvgContent);

        return $customAvatarPath;
    }

    /**
     * Get the list of available avatar names.
     *
     * @return array
     */
    public function getAvatarNames(): array
    {
        $avatars = $this->getDefaultAvatars();
        return array_map(function ($path) {
            return basename($path);
        }, $avatars);
    }

    /**
     * Generate complementary colors for gradient based on main color.
     *
     * @param string $baseColor
     * @return array
     */
    public function generateGradientColors(string $baseColor): array
    {
        // Convert hex to RGB
        $hex = ltrim($baseColor, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Generate complementary color (lighter variant)
        $lighterR = min(255, $r + 40);
        $lighterG = min(255, $g + 40);
        $lighterB = min(255, $b + 40);

        // Generate darker variant
        $darkerR = max(0, $r - 40);
        $darkerG = max(0, $g - 40);
        $darkerB = max(0, $b - 40);

        // Convert back to hex
        $lighter = sprintf("#%02x%02x%02x", $lighterR, $lighterG, $lighterB);
        $darker = sprintf("#%02x%02x%02x", $darkerR, $darkerG, $darkerB);

        return [
            'base' => $baseColor,
            'lighter' => $lighter,
            'darker' => $darker
        ];
    }
}
