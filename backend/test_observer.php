<?php
$p = App\Models\Product::first();
$p->stock = 3;
$p->save();
echo "Done";
