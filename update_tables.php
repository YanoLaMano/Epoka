<?php
$files = [
    __DIR__ . '/views/salaries/index.php',
    __DIR__ . '/views/missions/index.php',
    __DIR__ . '/views/distances/index.php',
    __DIR__ . '/views/dashboard.php',
    __DIR__ . '/views/agences/index.php',
    __DIR__ . '/views/parametres/index.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Remplacer class="table-container" par class="table-responsive"
        $newContent = str_replace('<div class="table-container">', '<div class="table-responsive">', $content);
        
        // Remplacer <table> par <table class="table table-hover">
        $newContent = str_replace('<table>', '<table class="table table-hover align-middle">', $newContent);
        
        if ($content !== $newContent) {
            file_put_contents($file, $newContent);
            echo "Updated: $file\n";
        }
    }
}
echo "Done replacing table classes.";
