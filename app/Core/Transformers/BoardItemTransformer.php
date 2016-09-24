<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\BoardItem;

/**
 * Class BoardItemTransformer
 * @package App\Transformers
 */
class BoardItemTransformer extends Transformer
{
    protected $defaultIncludes = ['creator','updater'];

    /**
     * @param $board
     * @return array
     */
    public function transform(BoardItem $board)
    {
        return [
            'id'    =>    $board->id,
            'title'    =>    $board->title,
            'description'    =>    $board->description,
            'created_at'    =>    $board->created_at->toIso8601String(),
            'updated_at'    =>    $board->updated_at->toIso8601String()
        ];
    }

    /**
     * @param BoardItem $board
     * @return \League\Fractal\Resource\Item
     */
    public function includeCreator(BoardItem $board)
    {
        if ($board->creator) {
            return $this->item($board->creator, new UserTransformer);
        }
        return null;
    }

    /**
     * @param BoardItem $board
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUpdater(BoardItem $board)
    {
        if ($board->updater) {
            return $this->item($board->updater, new UserTransformer);
        }
        return null;
    }
}
