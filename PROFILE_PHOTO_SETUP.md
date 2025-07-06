# Shared Hosting Profile Photo Setup Guide

This guide helps fix profile photo upload issues on shared hosting environments.

## Common Issues on Shared Hosting:
1. **Symlink creation fails** - Many shared hosts don't allow symlinks
2. **Permission issues** - Restricted file permissions
3. **PHP upload limits** - Small default upload limits
4. **Storage path issues** - Different directory structures

## Solutions Implemented:

### 1. Enhanced File Upload Component
- Added support for JPG files (in addition to JPEG, PNG)
- Increased max size to 1MB
- Added image optimization and resizing
- Auto-crop to 1:1 aspect ratio
- Resize to 300x300 pixels
- Convert to WebP format for better compression

### 2. Storage Fallback System
- Primary: Uses Laravel storage symlink
- Fallback: Direct file serving route for shared hosting
- Automatic detection and switching

### 3. Commands Added
```bash
# Fix storage permissions and directories
php artisan storage:fix-permissions

# Debug storage configuration
# Visit: your-domain.com/debug-storage
```

### 4. Environment Configuration
Add these to your `.env` file on shared hosting:

```env
# File upload settings (adjust based on your hosting limits)
FILESYSTEM_DISK=public

# If you have issues with symlinks, you can try:
# APP_URL=https://your-domain.com
```

### 5. Server Configuration
If you have access to PHP settings, increase these limits:

```ini
upload_max_filesize = 2M
post_max_size = 2M
max_file_uploads = 20
max_execution_time = 60
memory_limit = 128M
```

### 6. Manual Storage Setup (if automated setup fails)
1. Create directory: `public/storage/profile-images/`
2. Set permissions to 755
3. Copy files from `storage/app/public/` to `public/storage/`

## Troubleshooting:

### Profile photos not showing:
1. Check `/debug-storage` endpoint for issues
2. Verify file permissions (755 for directories, 644 for files)
3. Ensure `public/storage/profile-images/` exists
4. Check if files are being uploaded to correct location

### Upload fails:
1. Check PHP upload limits in debug endpoint
2. Try smaller images (under 500KB)
3. Use only JPEG/PNG formats
4. Check server error logs

### Files upload but don't display:
1. Check if storage symlink works
2. Files may be uploading but URL generation fails
3. Try the fallback route: `/storage-image/profile-images/filename.jpg`

## Files Modified:
- `app/Filament/Resources/UserResource.php` - Enhanced file upload
- `app/Models/User.php` - Better URL generation with fallbacks
- `routes/web.php` - Added image serving route
- `app/Console/Commands/FixStoragePermissions.php` - Storage setup
- `app/Http/Controllers/StorageDebugController.php` - Debug tools

## Production Notes:
- Remove the `/debug-storage` route before going live
- Consider using a CDN for better image performance
- Regular backups of uploaded files recommended
