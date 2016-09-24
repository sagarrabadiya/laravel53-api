<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers;

use App\Events\Note\NotePageCreated;
use App\Events\Note\NotePageUpdated;
use App\Models\Note;
use App\Models\NotePage;
use App\Core\Transformers\NotePageTransformer;
use App\Models\Project;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;

/**
 * Class NotePagesController
 * @package App\Http\Controllers
 */
class NotePagesController extends Controller
{

    /**
     * @var NotePageTransformer
     */
    protected $transformer;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;


    /**
     * NotePagesController constructor.
     * @param Dispatcher $dispatcher
     * @param NotePageTransformer $notePageTransformer
     */
    public function __construct(NotePageTransformer $notePageTransformer, Dispatcher $dispatcher)
    {
        $this->transformer = $notePageTransformer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Note $note
     * @return mixed
     */
    public function index(Project $project, Note $note)
    {
        $this->authorize('read', [$note, $project]);
        $pages = $note->pages()->with(['creator', 'updater'])->paginate(self::PAGER);
        return response()->paginator($pages, $this->transformer, ['key' => 'pages']);
    }

    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @param \App\Models\Note $note
     * @return mixed
     */
    public function store(Request $request, Project $project, Note $note)
    {
        $this->authorize('write', [$note, $project]);
        $page = new NotePage($request->all());
        $note->pages()->save($page);
        // fire new page created event
        $this->dispatcher->fire(new NotePageCreated($page));
        return response()->created($page, $this->transformer, ['key' => 'page']);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Note $note
     * @param \App\Models\NotePage $page
     * @return mixed
     */
    public function show(Project $project, Note $note, NotePage $page)
    {
        $this->authorize('read', [$note, $project]);
        return response()->item($page, $this->transformer, ['key' => 'page']);
    }

    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @param \App\Models\Note $note
     * @param \App\Models\NotePage $page
     * @return mixed
     */
    public function update(Request $request, Project $project, Note $note, NotePage $page)
    {
        $this->authorize('write', [$note, $project]);
        $page->update($request->all());
        // fire note page updated event
        $this->dispatcher->fire(new NotePageUpdated($page));
        return response()->item($page, $this->transformer, ['key' => 'page']);
    }


    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Note $note
     * @param \App\Models\NotePage $page
     * @return mixed
     */
    public function destroy(Project $project, Note $note, NotePage $page)
    {
        $this->authorize('write', [$note, $project]);
        $page->delete();
        return response()->deleted();
    }
}
