<?php

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

$products = Product::whereNull('image')->get();

foreach ($products as $product) {
    // Generate a unique dummy image URL based on product name
    $dummyImageUrl = "https://picsum.photos/seed/" . urlencode($product->name) . "/400/400";
    
    // Download the image
    $imageContents = @file_get_contents($dummyImageUrl);
    
    if ($imageContents) {
        $filename = 'products/' . Str::slug($product->name) . '-' . time() . '.jpg';
        
        Storage::disk('public')->put($filename, $imageContents);
        
        $product->image = $filename;
        $product->save();
        
        echo "Assigned dummy image for: {$product->name}\n";
    } else {
        echo "Failed to download image for: {$product->name}\n";
    }
}

echo "Done assigning dummy images.\n";
