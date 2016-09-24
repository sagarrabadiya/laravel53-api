<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\Note;

class NoteTransformer extends Transformer
{
    protected $defaultIncludes = ['creator', 'updater'];
    /**
     * @param $note
     * @return array
     */
    public function transform(Note $note)
    {
        return [
            'id'    =>    $note->id,
            'title'    =>    $note->title,
            'description'    =>    $note->description,
            'created_at'    =>    $note->created_at->toIso8601String(),
            'updated_at'    =>    $note->updated_at->toIso8601String()
        ];
    }

    public function includeCreator(Note $note)
    {
        if ($note->creator) {
            return $this->item($note->creator, new UserTransformer);
        }
        return null;
    }

    public function includeUpdater(Note $note)
    {
        if ($note->updater) {
            return $this->item($note->updater, new UserTransformer);
        }
        return null;
    }
}
