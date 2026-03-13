<?php
$dir = __DIR__ . '/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$replacements = [
    '📊' => '<i data-lucide="layout-dashboard" class="icon-sm"></i>',
    '🎯' => '<i data-lucide="target" class="icon-sm"></i>',
    '👥' => '<i data-lucide="users" class="icon-sm"></i>',
    '🏢' => '<i data-lucide="building" class="icon-sm"></i>',
    '📏' => '<i data-lucide="ruler" class="icon-sm"></i>',
    '⚙️' => '<i data-lucide="settings" class="icon-sm"></i>',
    '🚪' => '<i data-lucide="log-out" class="icon-sm"></i>',
    '➕' => '<i data-lucide="plus" class="icon-sm"></i>',
    '📝' => '<i data-lucide="file-edit" class="icon-sm"></i>',
    '✅' => '<i data-lucide="check-circle" class="icon-sm"></i>',
    '💰' => '<i data-lucide="credit-card" class="icon-sm"></i>',
    '👁️' => '<i data-lucide="eye" class="icon-sm"></i>',
    '✏️' => '<i data-lucide="edit-3" class="icon-sm"></i>',
    '🗑️' => '<i data-lucide="trash-2" class="icon-sm"></i>',
    '👑' => '<i data-lucide="shield" class="icon-sm"></i>',
    '👤' => '<i data-lucide="user" class="icon-sm"></i>',
    '💶' => '<i data-lucide="euro" class="icon-sm"></i>',
    '⚠️' => '<i data-lucide="alert-triangle" class="icon-sm"></i>',
    '📋' => '<i data-lucide="list" class="icon-sm"></i>',
    '🔐' => '<i data-lucide="lock" class="icon-sm"></i>',
    '❌' => '<i data-lucide="x-circle" class="icon-sm"></i>',
    '💾' => '<i data-lucide="save" class="icon-sm"></i>'
];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}

// Update includes/helpers.php
$helpers = __DIR__ . '/includes/helpers.php';
if (file_exists($helpers)) {
    $content = file_get_contents($helpers);
    $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
    if ($content !== $newContent) {
        file_put_contents($helpers, $newContent);
        echo "Updated: $helpers\n";
    }
}

echo "Done replacing emojis.";
