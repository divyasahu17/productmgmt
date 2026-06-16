<?php
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

Product::chunk(50, function ($products) {
    foreach ($products as $p) {
        $url = 'https://ui-avatars.com/api/?name=' . urlencode($p->name) . '&background=random&size=200';
        $content = @file_get_contents($url);
        if ($content) {
            $filename = 'products/' . uniqid() . '.png';
            Storage::disk('public')->put($filename, $content);
            $p->image = $filename;
            $p->price = (int)ceil($p->price * 0.7);
            $p->save();
            echo 'Saved ' . $p->name . "\n";
        }
    }
});
echo "Done!\n";
