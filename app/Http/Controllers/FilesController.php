<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>.
 */
namespace App\Http\Controllers;

use App\Core\Helpers\Utils;
use App\Models\Company;
use App\Models\File;
use App\Core\Transformers\FileTransformer;
use App\Models\Project;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FilesController extends Controller
{
    /**
     * @var FileTransformer
     */
    private $transformer;

    public function __construct(FileTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param \App\Models\Project                                          $project
     * @param \App\Models\Ticket|\App\Models\BoardItem|\App\Models\Comment $parent
     * @param \App\Models\Comment|null                                     $comment
     *
     * @return mixed
     */
    public function index(Project $project, $parent, $comment = null)
    {
        $this->authorize('read', [$parent, $project]);
        if ($comment) {
            $parent = $comment;
        }
        $files = $parent->files()->get();

        return response()->collection($files, $this->transformer, ['key' => 'files']);
    }

    /**
     * @param Request                                                      $request
     * @param \App\Models\Project                                          $project
     * @param \App\Models\Ticket|\App\Models\BoardItem|\App\Models\Comment $parent
     * @param \App\Models\Comment|null                                     $comment
     *
     * @return mixed
     */
    public function store(Request $request, Project $project, $parent, $comment = null)
    {
        $this->validatePost($request);
        $this->authorize('write', [$parent, $project]);
        $file = File::where($request->only(['salt', 'name']))->where('is_orphan', true)->firstOrFail();
        $file->is_orphan = false;
        $file->creator()->associate($this->user()); // set current user as file saver
        if ($comment) {
            $parent = $comment;
        }
        $parent->files()->save($file);

        return response()->created($file, $this->transformer, ['key' => 'file']);
    }

    /**
     * @param Request                                                      $request
     * @param \App\Models\Project                                          $project
     * @param \App\Models\Ticket|\App\Models\BoardItem|\App\Models\Comment $parent
     * @param \App\Models\File|\App\Models\Comment                         $comment
     * @param \App\Models\File|null                                        $file
     *
     * @return StreamedResponse
     */
    public function show(Request $request, Project $project, $parent, $comment, $file = null)
    {
        $this->authorize('read', [$parent, $project]);
        if (!$file) {
            $file = $comment;
        } else {
            $parent = $comment;
        }
        if (preg_match('/\./', $file)) {
            list($salt, $ext) = explode('.', $file);
        } else {
            $salt = $file;
        }
        $file = $parent->files()->where('salt', $salt)->firstOrFail();
        $filePath = Utils::buildFilePath($project).$file->salt.".{$file->ext}";
        if ($request->query('read')) {
            return Utils::getDisk($project->company)->get($filePath);
        }
        if ($request->query('download')) {
            return $this->downloadFile($project->company, $project, $file);
        }

        return response()->item($file, $this->transformer, ['key' => 'file']);
    }

    /**
     * @param \App\Models\Project                                          $project
     * @param \App\Models\Ticket|\App\Models\BoardItem|\App\Models\Comment $parent
     * @param \App\Models\File|\App\Models\Comment                         $comment
     * @param \App\Models\File|null
     *
     * @return mixed
     */
    public function destroy(Project $project, $parent, $comment, $file = null)
    {
        $this->authorize('write', [$parent, $project]);
        if (!$file) {
            $file = $comment;
        } else {
            $parent = $comment;
        }
        if (preg_match('/\./', $file)) {
            list($salt, $ext) = explode('.', $file);
        } else {
            $salt = $file;
        }
        $file = $parent->files()->where('salt', $salt)->firstOrFail();
        if (Utils::getDisk($project->company)->has(Utils::buildFilePath($project).$file->salt.'.'.$file->ext)) {
            Utils::getDisk($project->company)->delete(Utils::buildFilePath($project).$file->salt.'.'.$file->ext);
        }
        $file->delete();

        return response()->deleted();
    }

    /**
     * @param Request $request
     */
    protected function validatePost(Request $request)
    {
        $this->validate($request, [
            'salt' => 'required|exists:files',
            'name' => 'required',
        ]);
    }

    /**
     * @param Company $company
     * @param Project $project
     * @param File    $file
     *
     * @return StreamedResponse
     */
    protected function downloadFile(Company $company, Project $project, File $file)
    {
        $path = Utils::buildFilePath($project).$file->salt.".{$file->ext}";
        $response = new StreamedResponse();
        $response->setCallBack(function () use ($path, $company) {
            echo Utils::getDisk($company)->get($path);
        });
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->name);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
