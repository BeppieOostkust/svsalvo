<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixStoragePermissions extends Command
{
    protected $signature = 'storage:fix-permissions';
    protected $description = 'Fix storage permissions and create necessary directories for shared hosting';

    public function handle()
    {
        $this->info('Fixing storage permissions and creating directories...');

        // Create storage directories if they don't exist
        $directories = [
            storage_path('app/public'),
            storage_path('app/public/profile-images'),
            public_path('storage'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->info("Created directory: {$directory}");
            } else {
                $this->info("Directory exists: {$directory}");
            }
        }

        // Check if storage link exists
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (File::exists($link)) {
            if (is_link($link)) {
                $this->info('Storage symlink already exists');
            } else {
                $this->warn('Storage directory exists but is not a symlink');
                // On shared hosting, sometimes we need to remove and recreate
                File::deleteDirectory($link);
                $this->createStorageLink($link, $target);
            }
        } else {
            $this->createStorageLink($link, $target);
        }

        // Set permissions (if possible on shared hosting)
        try {
            chmod(storage_path('app/public'), 0755);
            chmod(storage_path('app/public/profile-images'), 0755);
            $this->info('Permissions set to 755');
        } catch (\Exception $e) {
            $this->warn('Could not set permissions (common on shared hosting): ' . $e->getMessage());
        }

        $this->info('Storage fix complete!');
        
        // Test file upload capability
        $testFile = storage_path('app/public/test.txt');
        try {
            File::put($testFile, 'test');
            if (File::exists($testFile)) {
                File::delete($testFile);
                $this->info('✅ File upload test successful');
            } else {
                $this->error('❌ File upload test failed - file not created');
            }
        } catch (\Exception $e) {
            $this->error('❌ File upload test failed: ' . $e->getMessage());
        }
    }

    private function createStorageLink($link, $target)
    {
        try {
            // Try creating symlink first
            if (symlink($target, $link)) {
                $this->info('✅ Storage symlink created successfully');
                return;
            }
        } catch (\Exception $e) {
            $this->warn('Symlink creation failed: ' . $e->getMessage());
        }

        // Fallback: copy directory structure (for shared hosting)
        try {
            if (!File::exists($link)) {
                File::makeDirectory($link, 0755, true);
            }
            
            // Create profile-images directory in public/storage
            $profileImagesDir = $link . '/profile-images';
            if (!File::exists($profileImagesDir)) {
                File::makeDirectory($profileImagesDir, 0755, true);
                $this->info('✅ Created public/storage/profile-images directory');
            }
            
            $this->info('✅ Storage directory structure created (fallback method)');
        } catch (\Exception $e) {
            $this->error('❌ Storage setup failed: ' . $e->getMessage());
        }
    }
}
