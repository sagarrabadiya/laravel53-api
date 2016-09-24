<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers;

use App\Core\Helpers\Utils;
use Illuminate\Filesystem\FilesystemAdapter;
use App\Models\Company;
use App\Models\File;
use App\Core\Transformers\FileTransformer;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UploadController extends Controller
{
    /**
     * @var FileTransformer
     */
    private $transformer;

    /**
     * UploadController constructor.
     * @param FileTransformer $transformer
     */
    public function __construct(FileTransformer $transformer)
    {
        $this->middleware('file.create', ['only'   =>  'upload']);
        $this->transformer = $transformer;
    }

    /**
     * @param Request $request
     * @param \App\Models\Company $company
     * @return mixed
     */
    public function upload(Request $request, Company $company)
    {
        $file = $this->uploadCore($request, $company);
        return response()->created($file, $this->transformer, ['key' => 'file']);
    }

    /**
     * @param Request $request
     * @param \App\Models\Company $company
     * @return mixed
     */
    public function uploadPublicImage(Request $request, Company $company)
    {
        $file = $this->uploadCore($request, $company, true);
        return response()->created($file, $this->transformer, ['key' => 'file']);
    }


    /**
     * @param Request $request
     * @param \App\Models\Company $company
     * @param bool $public
     * @return mixed
     */
    private function uploadCore(Request $request, Company $company, $public = false)
    {
        $this->validate($request, ['file' => 'required']);
        if (! $request->hasFile('file') && is_null($request->input('file', null))) {
            return response()->error('file is required', 422);
        }
        // check for valid operation
        if ($this->user()->company_id != $company->id) {
            throw new AccessDeniedHttpException;
        }
        $salt = str_random(30);

        $disk = Utils::getDisk($company, $public);
        if ($request->hasFile('file')) {
            $file = $this->uploadFile($request, $salt, $company, $disk);
        } else {
            $file = $this->uploadString($request, $salt, $company, $disk);
        }
        return $file;
    }

    /**
     * @param Request $request
     * @param $salt
     * @param Company $company
     * @param FilesystemAdapter $disk
     * @return File
     */
    private function uploadFile(Request $request, $salt, Company $company, FilesystemAdapter $disk)
    {
        $uploadedFile = $request->file('file');
        $stream = fopen($uploadedFile->getRealPath(), 'r+');
        $disk->put(
            $company->id . DIRECTORY_SEPARATOR . $salt . "." . $uploadedFile->getClientOriginalExtension(),
            $stream
        );
        return $this->saveFile(
            $salt,
            $uploadedFile->getClientOriginalName(),
            $uploadedFile->getClientOriginalExtension(),
            $uploadedFile->getSize(),
            $company
        );
    }

    /**
     * @param Request $request
     * @param $salt
     * @param Company $company
     * @param FilesystemAdapter $disk
     * @return File
     */
    private function uploadString(Request $request, $salt, Company $company, FilesystemAdapter $disk)
    {

        // save file
        $data = $this->getBase64String($request);
        $disk->put(
            $company->id . DIRECTORY_SEPARATOR . $salt . ".png",
            base64_decode($data)
        );

        $size = $disk->size($company->id . DIRECTORY_SEPARATOR . $salt . ".png");

        return $this->saveFile($salt, "{$salt}.png", 'png', $size, $company);
    }

    /**
     * @param $salt
     * @param $originalName
     * @param $extension
     * @param $size
     * @param $company
     * @return File
     */
    protected function saveFile($salt, $originalName, $extension, $size, Company $company)
    {
        $file = new File([
            'salt' => $salt,
            'name' => $originalName,
            'ext' => $extension,
            'size' => intval(Utils::bytes2kb($size)),
            'is_orphan' => true
        ]);
        $company->files()->save($file);
        return $file;
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    private function getBase64String(Request $request)
    {
        $data = explode(',', $request->input('file'));
        if (count($data) > 1) {
            $data = $data[1];
        } else {
            $data = reset($data);
        }
        if (base64_decode($data, true) == false) {
            return response()->error('invalid base64 image string provided', 422);
        }
        return $data;
    }
}
