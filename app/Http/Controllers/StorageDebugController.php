<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class StorageDebugController extends Controller
{
    public function debug()
    {
        $debug = [
            'PHP Upload Settings' => [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'max_file_uploads' => ini_get('max_file_uploads'),
                'max_execution_time' => ini_get('max_execution_time'),
                'memory_limit' => ini_get('memory_limit'),
            ],
            'Laravel Storage' => [
                'default_disk' => config('filesystems.default'),
                'public_disk_root' => config('filesystems.disks.public.root'),
                'public_disk_url' => config('filesystems.disks.public.url'),
                'app_url' => config('app.url'),
            ],
            'Directory Status' => [
                'storage/app/public exists' => File::exists(storage_path('app/public')) ? '✅ Yes' : '❌ No',
                'storage/app/public writable' => is_writable(storage_path('app/public')) ? '✅ Yes' : '❌ No',
                'public/storage exists' => File::exists(public_path('storage')) ? '✅ Yes' : '❌ No',
                'public/storage is symlink' => is_link(public_path('storage')) ? '✅ Yes' : '❌ No',
                'profile-images dir exists' => File::exists(storage_path('app/public/profile-images')) ? '✅ Yes' : '❌ No',
                'profile-images writable' => is_writable(storage_path('app/public/profile-images')) ? '✅ Yes' : '❌ No',
            ],
            'Storage Test' => []
        ];

        // Test file creation
        try {
            $testPath = 'test-' . time() . '.txt';
            Storage::disk('public')->put($testPath, 'test content');
            
            if (Storage::disk('public')->exists($testPath)) {
                $debug['Storage Test']['File creation'] = '✅ Success';
                $debug['Storage Test']['File URL'] = Storage::disk('public')->url($testPath);
                
                // Clean up
                Storage::disk('public')->delete($testPath);
            } else {
                $debug['Storage Test']['File creation'] = '❌ Failed - file not found after creation';
            }
        } catch (\Exception $e) {
            $debug['Storage Test']['File creation'] = '❌ Failed: ' . $e->getMessage();
        }

        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    }
}
