<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\Comment;

class CommentTransformer extends Transformer
{
    protected $defaultIncludes = ['creator'];

    public function transform(Comment $item)
    {
        return [
            'id'    =>    $item->id,
            'text'    =>    $item->text,
            'created_at'    =>    $item->created_at->toIso8601String(),
            'updated_at'    =>    $item->updated_at->toIso8601String()
        ];
    }

    public function includeFiles(Comment $comment)
    {
        $transformer = new FileTransformer();
        $transformer->setDefaultIncludes([]);
        return $this->collection($comment->files, $transformer);
    }

    public function includeCreator(Comment $comment)
    {
        if ($comment->creator) {
            return $this->item($comment->creator, new UserTransformer);
        }
        return null;
    }
}
