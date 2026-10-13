<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MinecraftSkinService
{
    /**
     * Validate whether an uploaded file is a standard Minecraft skin PNG.
     * Valid skins are PNG files of exactly 64x64 (modern) or 64x32 (legacy) pixels.
     *
     * @return string|null Error message, or null if valid.
     */
    public function validateSkin(UploadedFile $file): ?string
    {
        // 1. Max size: 2MB (Minecraft skins are typically 2KB - 25KB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            return 'Skin file size cannot exceed 2MB.';
        }

        $realPath = $file->getRealPath();
        if (! $realPath || ! file_exists($realPath)) {
            return 'Failed to read uploaded skin file.';
        }

        // 2. Validate PNG magic bytes header
        $handle = @fopen($realPath, 'rb');
        if (! $handle) {
            return 'Unable to open skin file for inspection.';
        }
        $header = fread($handle, 8);
        fclose($handle);

        if ($header !== "\x89PNG\r\n\x1a\n") {
            return 'The file must be a valid PNG image format.';
        }

        // 3. Inspect image dimensions
        $imageInfo = @getimagesize($realPath);
        if (! $imageInfo) {
            return 'Unable to read image dimensions. Please ensure it is a valid PNG file.';
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'] ?? '';

        if ($mime !== 'image/png') {
            return 'The file MIME type must be image/png.';
        }

        if ($width !== 64 || ($height !== 64 && $height !== 32)) {
            return "Invalid skin dimensions ({$width}x{$height}). Official Minecraft skins must be exactly 64x64 or 64x32 pixels.";
        }

        return null;
    }

    /**
     * Store uploaded skin file and generate a 64x64 composited head avatar.
     *
     * @return array{skin_path: string, avatar_head_path: string}
     */
    public function saveSkinAndExtractHead(UploadedFile $file, int|string $userId): array
    {
        $unique = $userId . '_' . time() . '_' . Str::random(6);
        $skinPath = "skins/skin_{$unique}.png";
        $headPath = "avatars/head_{$unique}.png";

        // Store original skin
        $realPath = $file->getRealPath();
        $skinBytes = file_get_contents($realPath);
        Storage::disk('public')->put($skinPath, $skinBytes);

        // Generate and store composited 64x64 head avatar
        $headPngBytes = $this->extractHeadPng($skinBytes);
        Storage::disk('public')->put($headPath, $headPngBytes);

        return [
            'skin_path' => $skinPath,
            'avatar_head_path' => $headPath,
        ];
    }

    /**
     * Extract 8x8 face + 8x8 helmet overlay and upscale to 64x64 PNG.
     * Works in 100% pure PHP (zero external dependencies/binaries required).
     */
    public function extractHeadPng(string $pngData): string
    {
        // If GD is available, use GD for speed
        if (extension_loaded('gd')) {
            $src = @imagecreatefromstring($pngData);
            if ($src) {
                $head = imagecreatetruecolor(64, 64);
                imagealphablending($head, false);
                imagesavealpha($head, true);
                $transparent = imagecolorallocatealpha($head, 0, 0, 0, 127);
                imagefill($head, 0, 0, $transparent);
                imagealphablending($head, true);

                // Base Face: (8, 8) 8x8 -> (0, 0) 64x64
                imagecopyresampled($head, $src, 0, 0, 8, 8, 64, 64, 8, 8);
                // Outer Hat / Helmet: (40, 8) 8x8 -> (0, 0) 64x64
                imagecopyresampled($head, $src, 0, 0, 40, 8, 64, 64, 8, 8);

                ob_start();
                imagepng($head);
                $gdResult = ob_get_clean();
                imagedestroy($head);
                imagedestroy($src);

                if (! empty($gdResult)) {
                    return $gdResult;
                }
            }
        }

        // Pure PHP PNG scanline decoder & nearest-neighbor 8x scaler
        return $this->purePhpExtractHead($pngData);
    }

    /**
     * Pure PHP PNG Scanline parser & 8x composite renderer.
     */
    protected function purePhpExtractHead(string $data): string
    {
        $offset = 8;
        $length = strlen($data);
        $idat = '';
        $width = 64;
        $height = 64;
        $colorType = 6;

        while ($offset < $length) {
            $chunkLen = unpack('N', substr($data, $offset, 4))[1];
            $chunkType = substr($data, $offset + 4, 4);
            $chunkData = substr($data, $offset + 8, $chunkLen);
            $offset += 12 + $chunkLen;

            if ($chunkType === 'IHDR') {
                $ihdr = unpack('Nwidth/Nheight/Cdepth/Ccolor/Ccomp/Cfilter/Cinterlace', $chunkData);
                $width = $ihdr['width'];
                $height = $ihdr['height'];
                $colorType = $ihdr['color'];
            } elseif ($chunkType === 'IDAT') {
                $idat .= $chunkData;
            } elseif ($chunkType === 'IEND') {
                break;
            }
        }

        $raw = @gzuncompress($idat);
        if ($raw === false) {
            return $data; // Fallback to raw data if uncompression fails
        }

        $bpp = ($colorType === 6) ? 4 : (($colorType === 2) ? 3 : 4);
        $pixels = [];
        $rawOffset = 0;

        for ($y = 0; $y < $height; $y++) {
            $filter = ord($raw[$rawOffset++]);
            $pixels[$y] = [];

            for ($x = 0; $x < $width; $x++) {
                $r = ord($raw[$rawOffset++]);
                $g = ord($raw[$rawOffset++]);
                $b = ord($raw[$rawOffset++]);
                $a = ($bpp === 4) ? ord($raw[$rawOffset++]) : 255;

                if ($filter === 1 && $x > 0) { // Sub
                    $r = ($r + $pixels[$y][$x - 1][0]) & 0xFF;
                    $g = ($g + $pixels[$y][$x - 1][1]) & 0xFF;
                    $b = ($b + $pixels[$y][$x - 1][2]) & 0xFF;
                    if ($bpp === 4) $a = ($a + $pixels[$y][$x - 1][3]) & 0xFF;
                } elseif ($filter === 2 && $y > 0) { // Up
                    $r = ($r + $pixels[$y - 1][$x][0]) & 0xFF;
                    $g = ($g + $pixels[$y - 1][$x][1]) & 0xFF;
                    $b = ($b + $pixels[$y - 1][$x][2]) & 0xFF;
                    if ($bpp === 4) $a = ($a + $pixels[$y - 1][$x][3]) & 0xFF;
                } elseif ($filter === 3) { // Average
                    $leftR = ($x > 0) ? $pixels[$y][$x - 1][0] : 0;
                    $upR = ($y > 0) ? $pixels[$y - 1][$x][0] : 0;
                    $r = ($r + (int)(($leftR + $upR) / 2)) & 0xFF;

                    $leftG = ($x > 0) ? $pixels[$y][$x - 1][1] : 0;
                    $upG = ($y > 0) ? $pixels[$y - 1][$x][1] : 0;
                    $g = ($g + (int)(($leftG + $upG) / 2)) & 0xFF;

                    $leftB = ($x > 0) ? $pixels[$y][$x - 1][2] : 0;
                    $upB = ($y > 0) ? $pixels[$y - 1][$x][2] : 0;
                    $b = ($b + (int)(($leftB + $upB) / 2)) & 0xFF;

                    if ($bpp === 4) {
                        $leftA = ($x > 0) ? $pixels[$y][$x - 1][3] : 0;
                        $upA = ($y > 0) ? $pixels[$y - 1][$x][3] : 0;
                        $a = ($a + (int)(($leftA + $upA) / 2)) & 0xFF;
                    }
                } elseif ($filter === 4) { // Paeth
                    $paeth = function($a, $b, $c) {
                        $p = $a + $b - $c;
                        $pa = abs($p - $a);
                        $pb = abs($p - $b);
                        $pc = abs($p - $c);
                        if ($pa <= $pb && $pa <= $pc) return $a;
                        if ($pb <= $pc) return $b;
                        return $c;
                    };

                    $aR = ($x > 0) ? $pixels[$y][$x - 1][0] : 0;
                    $bR = ($y > 0) ? $pixels[$y - 1][$x][0] : 0;
                    $cR = ($x > 0 && $y > 0) ? $pixels[$y - 1][$x - 1][0] : 0;
                    $r = ($r + $paeth($aR, $bR, $cR)) & 0xFF;

                    $aG = ($x > 0) ? $pixels[$y][$x - 1][1] : 0;
                    $bG = ($y > 0) ? $pixels[$y - 1][$x][1] : 0;
                    $cG = ($x > 0 && $y > 0) ? $pixels[$y - 1][$x - 1][1] : 0;
                    $g = ($g + $paeth($aG, $bG, $cG)) & 0xFF;

                    $aB = ($x > 0) ? $pixels[$y][$x - 1][2] : 0;
                    $bB = ($y > 0) ? $pixels[$y - 1][$x][2] : 0;
                    $cB = ($x > 0 && $y > 0) ? $pixels[$y - 1][$x - 1][2] : 0;
                    $b = ($b + $paeth($aB, $bB, $cB)) & 0xFF;

                    if ($bpp === 4) {
                        $aA = ($x > 0) ? $pixels[$y][$x - 1][3] : 0;
                        $bA = ($y > 0) ? $pixels[$y - 1][$x][3] : 0;
                        $cA = ($x > 0 && $y > 0) ? $pixels[$y - 1][$x - 1][3] : 0;
                        $a = ($a + $paeth($aA, $bA, $cA)) & 0xFF;
                    }
                }

                $pixels[$y][$x] = [$r, $g, $b, $a];
            }
        }

        // Composite 8x8 Head (Face + Hat)
        $head8x8 = [];
        for ($hy = 0; $hy < 8; $hy++) {
            $head8x8[$hy] = [];
            for ($hx = 0; $hx < 8; $hx++) {
                $face = $pixels[8 + $hy][8 + $hx] ?? [0, 0, 0, 0];
                $hat = $pixels[8 + $hy][40 + $hx] ?? [0, 0, 0, 0];

                if ($hat[3] > 0) {
                    $alpha = $hat[3] / 255.0;
                    $r = (int)($hat[0] * $alpha + $face[0] * (1.0 - $alpha));
                    $g = (int)($hat[1] * $alpha + $face[1] * (1.0 - $alpha));
                    $b = (int)($hat[2] * $alpha + $face[2] * (1.0 - $alpha));
                    $a = (int)(255 * ($alpha + ($face[3] / 255.0) * (1.0 - $alpha)));
                    $head8x8[$hy][$hx] = [$r, $g, $b, $a];
                } else {
                    $head8x8[$hy][$hx] = $face;
                }
            }
        }

        // Upscale 8x8 to 64x64 PNG scanlines
        $outScanlines = '';
        for ($y = 0; $y < 64; $y++) {
            $outScanlines .= chr(0); // Filter 0 (None)
            $sourceY = (int)($y / 8);
            for ($x = 0; $x < 64; $x++) {
                $sourceX = (int)($x / 8);
                $p = $head8x8[$sourceY][$sourceX];
                $outScanlines .= chr($p[0]) . chr($p[1]) . chr($p[2]) . chr($p[3]);
            }
        }

        $packChunk = function(string $type, string $chunkData): string {
            return pack('N', strlen($chunkData)) . $type . $chunkData . pack('N', crc32($type . $chunkData));
        };

        return "\x89PNG\r\n\x1a\n"
            . $packChunk('IHDR', pack('NNCCCCC', 64, 64, 8, 6, 0, 0, 0))
            . $packChunk('IDAT', gzcompress($outScanlines, 9))
            . $packChunk('IEND', '');
    }

    /**
     * Delete existing skin and head files from public disk.
     */
    public function deleteOldSkin(?string $skinPath, ?string $headPath): void
    {
        if ($skinPath && Storage::disk('public')->exists($skinPath)) {
            Storage::disk('public')->delete($skinPath);
        }

        if ($headPath && Storage::disk('public')->exists($headPath)) {
            Storage::disk('public')->delete($headPath);
        }
    }
}
