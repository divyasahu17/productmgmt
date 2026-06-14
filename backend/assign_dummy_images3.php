<?php

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

$products = Product::all();

foreach ($products as $product) {
    // We will use placehold.co to get an image matching the product name
    $text = urlencode($product->name);
    // Use a nice purple background with white text, Montserrat font
    $dummyImageUrl = "https://placehold.co/400x400/6366f1/ffffff?font=Montserrat&text={$text}";
    
    // Download the image
    // Note: placehold.co returns SVGs or PNGs based on extension. We will append .png to ensure we get a PNG
    $dummyImageUrl = "https://placehold.co/400x400/6366f1/ffffff.png?font=Montserrat&text={$text}";
    $imageContents = @file_get_contents($dummyImageUrl);
    
    if ($imageContents) {
        $filename = 'products/' . Str::slug($product->name) . '-' . time() . '.png';
        
        Storage::disk('public')->put($filename, $imageContents);
        
        $product->image = $filename;
        $product->save();
        
        echo "Assigned dummy image for: {$product->name}\n";
    } else {
        echo "Failed to download image for: {$product->name}\n";
    }
}

echo "Done assigning dummy images.\n";
