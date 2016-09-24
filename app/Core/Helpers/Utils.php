<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Helpers;

use App\Models\Company;
use App\Models\Project;
use Illuminate\Filesystem\FilesystemAdapter;
use Storage;

class Utils
{
    /**
     * @param $bytes
     * @return mixed
     */
    public static function bytes2kb($bytes)
    {
        return number_format($bytes / 1024, 2);
    }

    /**
     * @param $bytes
     * @return mixed
     */
    public static function bytes2mb($bytes)
    {
        return number_format($bytes / 1048576, 2);
    }

    /**
     * @param $bytes
     * @return mixed
     */
    public static function bytes2gb($bytes)
    {
        return number_format($bytes / 1073741824, 2);
    }

    /**
     * @param $kb
     * @return mixed
     */
    public static function kb2mb($kb)
    {
        return number_format($kb / 1024, 2);
    }

    /**
     * @param Company $company
     * @param bool $isPublic
     * @return FilesystemAdapter
     */
    public static function getDisk(Company $company, $isPublic = false)
    {
        // here we can implement other file system and return the disk based on cloud settings
        return Storage::disk($isPublic ? 'public' : 'local');
    }

    /**
     * function to get file path on disk
     * @param Project $project
     * @return string
     */
    public static function buildFilePath(Project $project)
    {
        return $project->company_id.DIRECTORY_SEPARATOR;
    }
}
