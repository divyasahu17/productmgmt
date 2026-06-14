<?php

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

$products = Product::all();

foreach ($products as $product) {
    // We will use loremflickr to get an image matching the product name
    // Replace spaces with commas or just use the first word as keyword to improve accuracy
    $keyword = urlencode(explode(' ', $product->name)[0] . ',' . explode(' ', $product->category->name ?? 'product')[0]);
    
    // To ensure we get a unique image per product if names are similar, we can add a random number or lock to a specific one
    $dummyImageUrl = "https://loremflickr.com/400/400/{$keyword}?lock={$product->id}";
    
    // Download the image
    $imageContents = @file_get_contents($dummyImageUrl);
    
    if ($imageContents) {
        $filename = 'products/' . Str::slug($product->name) . '-' . time() . '.jpg';
        
        Storage::disk('public')->put($filename, $imageContents);
        
        $product->image = $filename;
        $product->save();
        
        echo "Assigned dummy image for: {$product->name} (Keyword: {$keyword})\n";
    } else {
        echo "Failed to download image for: {$product->name}\n";
    }
}

echo "Done assigning dummy images.\n";
