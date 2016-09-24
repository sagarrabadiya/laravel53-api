<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\NotePage;

/**
 * Class NotePageTransformer
 * @package App\Transformers
 */
class NotePageTransformer extends Transformer
{
    protected $defaultIncludes = ['creator','updater'];
    /**
     * @param $page
     * @return array
     */
    public function transform(NotePage $page)
    {
        return [
            'id'    =>    $page->id,
            'title'    =>    $page->title,
            'description'    =>    $page->description,
            'created_at'    =>    $page->created_at->toIso8601String(),
            'updated_at'    =>    $page->updated_at->toIso8601String()
        ];
    }

    public function includeCreator(NotePage $page)
    {
        if ($page->creator) {
            return $this->item($page->creator, new UserTransformer);
        }
        return null;
    }

    public function includeUpdater(NotePage $page)
    {
        if ($page->updater) {
            return $this->item($page->updater, new UserTransformer);
        }
        return null;
    }
}
