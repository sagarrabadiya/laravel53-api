<?php

/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */
class UploadControllerTest extends TestCase
{

    private function mockFileSystem()
    {
        $mockLocalStorage = Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
        $mockLocalStorage->shouldReceive('put')->once()->withAnyArgs()->andReturn(true);
        Storage::shouldReceive('disk')->once()->andReturn($mockLocalStorage);
    }

    public function test_it_should_allow_authorized_user_to_upload_file()
    {
        $this->mockFileSystem();
        $uploadedFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(base_path('tests/test_files/sample.png'), 'sample.png');
        $server = ['HTTP_Authorization' => 'Bearer '.$this->getAuthUserToken()];
        $this->call("POST", '/api/companies/' . $this->company->id . "/files", [], [], ['file' => $uploadedFile], $server);
        $this->assertResponseStatus(201);
    }

    public function test_it_should_not_allow_user_from_different_company_to_upload()
    {
        $company = factory(\App\Models\Company::class)->create();
        $this->user->company_id = $company->id;
        $this->user->save();
        $file = new \Symfony\Component\HttpFoundation\File\UploadedFile(base_path('tests/test_files/sample.png'), 'sample.png');
        $server = ['HTTP_Authorization' => 'Bearer '.$this->getAuthUserToken()];
        $this->call("POST", '/api/companies/' . $this->company->id . "/files", [], [], ['file' => $file], $server);
        $this->assertResponseStatus(403);
        $company->forceDelete();
    }

}