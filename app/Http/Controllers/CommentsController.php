<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers;

use App\Core\Transformers\CommentTransformer;
use App\Events\Comment\CommentCreated;
use App\Models\Project;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use App\Models\Comment;

/**
 * Class CommentsController
 * @package App\Http\Controllers
 */
class CommentsController extends Controller
{
    /**
     * @var CommentTransformer
     */
    protected $transformer;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * CommentsController constructor.
     * @param CommentTransformer $transformer
     * @param Dispatcher $dispatcher
     */
    public function __construct(CommentTransformer $transformer, Dispatcher $dispatcher)
    {
        $this->transformer = $transformer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket|\App\Models\BoardItem $parent
     * @return mixed
     */
    public function index(Project $project, $parent)
    {
        $this->authorize('read', [$parent, $project]);
        $comments = $parent->comments()->paginate(self::PAGER);
        return response()->paginator($comments, $this->transformer, ['key' => 'comments']);
    }

    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket|\App\Models\BoardItem $parent
     * @return mixed
     */
    public function store(Request $request, Project $project, $parent)
    {
        $this->authorize('write', [$parent, $project]);
        $comment = new Comment(['text'=>$request->input('text')]);
        $comment = $parent->comments()->save($comment);
        //fire new comment added event
        $this->dispatcher->fire(new CommentCreated($parent, $comment));
        return response()->created($comment, $this->transformer, ['key' => 'comment']);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket|\App\Models\BoardItem $parent
     * @param \App\Models\Comment $comment
     * @return mixed
     */
    public function show(Project $project, $parent, Comment $comment)
    {
        $this->authorize('read', [$parent, $project]);
        return response()->item($comment, $this->transformer, ['key' => 'comment']);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket|\App\Models\BoardItem $parent
     * @param \App\Models\Comment $comment
     * @return mixed
     */
    public function destroy(Project $project, $parent, Comment $comment)
    {
        $this->authorize('write', [$parent, $project]);
        $comment->delete();
        return response()->deleted();
    }
}
