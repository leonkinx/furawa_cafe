<?php
/**
 * PWA Icon Generator for Furawa Cafe Admin
 * 
 * Script ini akan generate icon PWA dalam berbagai ukuran
 * Jalankan: php public/generate-pwa-icons.php
 */

// Icon sizes yang dibutuhkan
$sizes = [72, 96, 128, 144, 152, 192, 384, 512];

// Warna background
$bgColor = [102, 126, 234]; // #667eea

// Buat folder images jika belum ada
$imagesDir = __DIR__ . '/images';
if (!file_exists($imagesDir)) {
    mkdir($imagesDir, 0755, true);
}

echo "🎨 Generating PWA Icons for Furawa Cafe Admin...\n\n";

foreach ($sizes as $size) {
    $filename = "icon-{$size}x{$size}.png";
    $filepath = $imagesDir . '/' . $filename;
    
    // Create image
    $image = imagecreatetruecolor($size, $size);
    
    // Enable alpha blending
    imagealphablending($image, false);
    imagesavealpha($image, true);
    
    // Create gradient background
    for ($y = 0; $y < $size; $y++) {
        $ratio = $y / $size;
        $r = $bgColor[0] + ($ratio * (118 - $bgColor[0])); // 667eea to 764ba2
        $g = $bgColor[1] - ($ratio * ($bgColor[1] - 75));
        $b = $bgColor[2] - ($ratio * ($bgColor[2] - 162));
        
        $color = imagecolorallocate($image, $r, $g, $b);
        imagefilledrectangle($image, 0, $y, $size, $y + 1, $color);
    }
    
    // Add rounded corners
    $radius = $size * 0.15; // 15% radius
    $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
    
    // Top-left corner
    imagefilledellipse($image, $radius, $radius, $radius * 2, $radius * 2, $transparent);
    imagefilledrectangle($image, 0, 0, $radius, $radius, $transparent);
    
    // Top-right corner
    imagefilledellipse($image, $size - $radius, $radius, $radius * 2, $radius * 2, $transparent);
    imagefilledrectangle($image, $size - $radius, 0, $size, $radius, $transparent);
    
    // Bottom-left corner
    imagefilledellipse($image, $radius, $size - $radius, $radius * 2, $radius * 2, $transparent);
    imagefilledrectangle($image, 0, $size - $radius, $radius, $size, $transparent);
    
    // Bottom-right corner
    imagefilledellipse($image, $size - $radius, $size - $radius, $radius * 2, $radius * 2, $transparent);
    imagefilledrectangle($image, $size - $radius, $size - $radius, $size, $size, $transparent);
    
    // Add icon (utensils symbol)
    $white = imagecolorallocate($image, 255, 255, 255);
    $iconSize = $size * 0.5;
    $centerX = $size / 2;
    $centerY = $size / 2;
    
    // Draw fork (left)
    $forkWidth = $iconSize * 0.15;
    $forkHeight = $iconSize * 0.8;
    $forkX = $centerX - $iconSize * 0.2;
    imagefilledrectangle(
        $image,
        $forkX - $forkWidth / 2,
        $centerY - $forkHeight / 2,
        $forkX + $forkWidth / 2,
        $centerY + $forkHeight / 2,
        $white
    );
    
    // Fork prongs
    for ($i = -1; $i <= 1; $i++) {
        $prongX = $forkX + ($i * $forkWidth * 1.5);
        imagefilledrectangle(
            $image,
            $prongX - $forkWidth / 4,
            $centerY - $forkHeight / 2,
            $prongX + $forkWidth / 4,
            $centerY - $forkHeight / 4,
            $white
        );
    }
    
    // Draw spoon (right)
    $spoonWidth = $iconSize * 0.15;
    $spoonHeight = $iconSize * 0.8;
    $spoonX = $centerX + $iconSize * 0.2;
    
    // Spoon handle
    imagefilledrectangle(
        $image,
        $spoonX - $spoonWidth / 2,
        $centerY - $spoonHeight / 2,
        $spoonX + $spoonWidth / 2,
        $centerY + $spoonHeight / 2,
        $white
    );
    
    // Spoon bowl
    imagefilledellipse(
        $image,
        $spoonX,
        $centerY - $spoonHeight / 2,
        $spoonWidth * 2.5,
        $spoonHeight * 0.4,
        $white
    );
    
    // Save image
    imagepng($image, $filepath);
    imagedestroy($image);
    
    echo "✅ Generated: {$filename} ({$size}x{$size})\n";
}

echo "\n🎉 All PWA icons generated successfully!\n";
echo "📁 Location: {$imagesDir}\n\n";
echo "Next steps:\n";
echo "1. Check the generated icons in public/images/\n";
echo "2. Refresh your browser\n";
echo "3. Look for the install button in the address bar\n";
echo "4. Install the PWA!\n";
