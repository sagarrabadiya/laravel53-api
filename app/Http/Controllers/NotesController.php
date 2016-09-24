<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers;

use App\Events\Note\NoteCreated;
use App\Events\Note\NoteUpdated;
use App\Models\Note;
use App\Core\Transformers\NoteTransformer;
use App\Models\Project;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;

/**
 * Class NotesController
 * @package App\Http\Controllers
 */
class NotesController extends Controller
{
    /**
     * @var NoteTransformer
     */
    protected $transformer;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * NotesController constructor.
     * @param NoteTransformer $noteTransformer
     * @param Dispatcher $dispatcher
     */
    public function __construct(NoteTransformer $noteTransformer, Dispatcher $dispatcher)
    {
        $this->transformer = $noteTransformer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function index(Project $project)
    {
        $this->authorize('read', [new Note, $project]);
        $notes = $project->notes()->with(['creator', 'updater'])->paginate(self::PAGER);
        return response()->paginator($notes, $this->transformer, ['key' => 'notes']);
    }

    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('write', [new Note, $project]);
        $note = new Note($request->all());
        $project->notes()->save($note);
        // fire note created event
        $this->dispatcher->fire(new NoteCreated($note));
        return response()->created($note, $this->transformer, ['key' => 'note']);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Note $note
     * @return mixed
     */
    public function show(Project $project, Note $note)
    {
        $this->authorize('read', [$note, $project]);
        return response()->item($note, $this->transformer, ['key' => 'note']);
    }

    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @param \App\Models\Note $note
     * @return mixed
     */
    public function update(Request $request, Project $project, Note $note)
    {
        $this->authorize('write', [$note, $project]);
        $note->update($request->all());
        // fire note updated event
        $this->dispatcher->fire(new NoteUpdated($note));
        return response()->item($note, $this->transformer, ['key' => 'note']);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Note $note
     * @return mixed
     */
    public function destroy(Project $project, Note $note)
    {
        $this->authorize('write', [$note, $project]);
        $note->delete();
        return response()->deleted();
    }
}
