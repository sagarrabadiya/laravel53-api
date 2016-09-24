<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>.
 */
namespace App\Http\Controllers;

use App\Events\BoardItem\BoardItemCreated;
use App\Events\BoardItem\BoardItemUpdated;
use App\Models\Project;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use App\Models\BoardItem;
use App\Core\Transformers\BoardItemTransformer;

/**
 * Class BoardItemsController.
 */
class BoardItemsController extends Controller
{
    /**
     * @var BoardItemTransformer
     */
    protected $transformer;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * BoardItemsController constructor.
     *
     * @param BoardItemTransformer $boardItemTransformer
     */
    public function __construct(BoardItemTransformer $boardItemTransformer, Dispatcher $dispatcher)
    {
        $this->transformer = $boardItemTransformer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param \App\Models\Project $project
     *
     * @return mixed
     */
    public function index(Project $project)
    {
        $this->authorize('read', [new BoardItem(), $project]);
        //get board items
        $boardItems = $project->boardItems()->with(['creator', 'updater'])->paginate(self::PAGER);

        return response()->paginator($boardItems, $this->transformer, ['key' => 'board_items']);
    }

    /**
     * @param Request             $request
     * @param \App\Models\Project $project
     *
     * @return mixed
     */
    public function store(Request $request, Project $project)
    {
        $boardItem = new BoardItem($request->all());
        $this->authorize('write', [$boardItem, $project]);
        $project->boardItems()->save($boardItem);
        // fire event about board item created
        $this->dispatcher->fire(new BoardItemCreated($boardItem));

        return response()->created($boardItem, $this->transformer, ['key' => 'board_item']);
    }

    /**
     * @param \App\Models\Project   $project
     * @param \App\Models\BoardItem $boardItem
     *
     * @return mixed
     */
    public function show(Project $project, BoardItem $boardItem)
    {
        $this->authorize('read', [$boardItem, $project]);

        return response()->item($boardItem, $this->transformer, ['key' => 'board_item']);
    }

    /**
     * @param Request               $request
     * @param \App\Models\Project   $project
     * @param \App\Models\BoardItem $boardItem
     *
     * @return mixed
     */
    public function update(Request $request, Project $project, BoardItem $boardItem)
    {
        $this->authorize('write', [$boardItem, $project]);
        $boardItem->update($request->all());
        // fire boarditem updated event
        $this->dispatcher->fire(new BoardItemUpdated($boardItem));

        return response()->item($boardItem, $this->transformer, ['key' => 'board_item']);
    }

    /**
     * @param \App\Models\Project   $project
     * @param \App\Models\BoardItem $boardItem
     *
     * @return mixed
     */
    public function destroy(Project $project, BoardItem $boardItem)
    {
        $this->authorize('write', [$boardItem, $project]);
        $boardItem->delete();

        return response()->deleted();
    }
}
