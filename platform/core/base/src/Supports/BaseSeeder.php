<?php

namespace Botble\Base\Supports;

use Botble\Media\Models\MediaFile;
use Botble\Media\Models\MediaFolder;
use File;
use Illuminate\Database\Seeder;
use Mimey\MimeTypes;
use RvMedia;

class BaseSeeder extends Seeder
{
    /**
     * @param string $folder
     * @return array
     */
    public function uploadFiles(string $folder): array
    {
        File::deleteDirectory(config('filesystems.disks.public.root') . '/' . $folder);
        MediaFile::where('url', 'LIKE', $folder . '/%')->forceDelete();
        MediaFolder::where('name', $folder)->forceDelete();

        $mimeType = new MimeTypes;

        $files = [];
        foreach (File::allFiles(database_path('seeders/files/' . $folder)) as $file) {
            $type = $mimeType->getMimeType(File::extension($file));
            $files[] = RvMedia::uploadFromPath($file, 0, $folder, $type);
        }

        return $files;
    }
}
