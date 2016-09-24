<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\File;

class FileTransformer extends Transformer
{
    /**
     * @var array
     */
    protected $defaultIncludes = ['creator'];

    /**
     * @param File $file
     * @return array
     */
    public function transform(File $file)
    {
        return [
            'name' =>    $file->name,
            'salt'    =>    $file->salt,
            'ext'    =>    $file->ext,
            'size'      =>      $file->size." KB",
            'created_at'    =>    $file->created_at->toIso8601String(),
            'updated_at'    =>    $file->updated_at->toIso8601String()
        ];
    }

    /**
     * @param File $file
     * @return \League\Fractal\Resource\Item
     */
    public function includeCreator(File $file)
    {
        if ($file->creator) {
            return $this->item($file->creator, new UserTransformer());
        }
        return null;
    }
}
