<?php
/**
 * Script to add auth.php inclusion to all admin PHP files
 * Run this script once to secure all admin pages
 */

// List of files that should NOT be modified
// Login page and authentication related pages don't need the auth include
$exclude_files = [
    'login.php',
    'auth.php',
    'update_auth.php',
    'register.php'
];

// Directory to process
$directory = __DIR__;

// Get all PHP files
$files = glob($directory . '/*.php');

// Count of modified files
$modified_count = 0;

foreach ($files as $file) {
    // Get the basename of the file
    $filename = basename($file);
    
    // Skip excluded files
    if (in_array($filename, $exclude_files)) {
        echo "Skipping {$filename} (excluded)<br>";
        continue;
    }
    
    // Read the file content
    $content = file_get_contents($file);
    
    // Check if auth.php is already included
    if (strpos($content, "require_once 'auth.php'") !== false || 
        strpos($content, "include 'auth.php'") !== false ||
        strpos($content, "require 'auth.php'") !== false ||
        strpos($content, "include_once 'auth.php'") !== false) {
        echo "Skipping {$filename} (already has auth include)<br>";
        continue;
    }
    
    // Add auth include at the top after <?php
    $pattern = '/^<\?php\s+/';
    $replacement = "<?php\nrequire_once 'auth.php';\n\n";
    
    $new_content = preg_replace($pattern, $replacement, $content);
    
    // If no change, the file might not start with <?php, try a different approach
    if ($new_content === $content) {
        $new_content = "<?php\nrequire_once 'auth.php';\n?>\n" . $content;
    }
    
    // Write the modified content back to the file
    if (file_put_contents($file, $new_content)) {
        echo "Added auth include to {$filename}<br>";
        $modified_count++;
    } else {
        echo "Failed to modify {$filename}<br>";
    }
}

echo "<br><strong>Completed: Modified {$modified_count} files</strong>";
?> 