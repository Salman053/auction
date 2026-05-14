<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$jobs = Illuminate\Support\Facades\DB::table('jobs')->select('id', 'queue', 'payload')->get();
foreach ($jobs as $j) {
    $p = json_decode($j->payload);
    echo $j->id . ' | ' . $j->queue . ' | ' . $p->displayName . "\n";
}
