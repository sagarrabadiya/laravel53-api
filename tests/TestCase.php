<?php

use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{

    use DatabaseMigrations;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * @var null| \App\Models\User
     */
    protected $user = null;

    /**
     * @var null | string
     */
    protected $authUserToken = null;

    /**
     * @var string
     */
    protected $baseApi = '/api';


    /**
     * @var App\Models\Company
     */
    protected $company;

    /**
     * @var Faker\Generator
     */
    protected $faker;

    /**
     * @var App\Models\Project
     */
    protected $project;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate');
        // seed plan data
        $this->artisan('db:seed', ['--class'=>'ResourceLimitSeed']);
        // create personal oauth client
        $this->artisan('passport:client', ['--personal' => true, '-n' => true]);

        $this->faker = Factory::create();
        $this->company = factory(App\Models\Company::class)->create();
        $this->getUser();
    }



    public function getUser()
    {
        if (! $this->user) {
            $this->setAuthUserToken();
        }

        return $this->user;
    }

    public function getAuthUserToken()
    {
        return $this->user->createToken('testing token')->accessToken;
    }

    /*Laravel angular material starter test helpers*/

    public function seeApiSuccess()
    {
        return $this->dontSeeJson(['errors'=>true]);
    }

    public function seeValidationError()
    {
        $this->assertResponseStatus(422);

        return $this->see('"errors":{');
    }

    public function seeApiError($error_code)
    {
        $this->assertResponseStatus($error_code);

        return $this->see('"errors":{');
    }

    public function seeJsonKey($entity)
    {
        return $this->see('"'.$entity.'":');
    }

    public function seeJsonValue($value)
    {
        return $this->see('"'.$value.'"');
    }

    public function seeJsonArray($entity)
    {
        return $this->see('"'.$entity.'":[');
    }

    public function seeJsonObject($entity)
    {
        return $this->see('"'.$entity.'":{');
    }

    /**
     * login the authUser using JWT and store the token.
     */
    private function setAuthUserToken()
    {
        $authUser = factory(App\Models\User::class)->create(['designation' => 'admin', 'company_id' => $this->company->id]);

        $this->user = $authUser;
    }

    public function authUserGet($uri)
    {
        $headers = ['Authorization' => 'Bearer '.$this->getAuthUserToken()];
        return $this->get($this->baseApi . $uri, $headers);
    }

    public function authUserPost($uri, $parameters = [])
    {
        $headers = ['Authorization' => 'Bearer '.$this->getAuthUserToken()];
        return $this->json('post', $this->baseApi . $uri, $parameters, $headers);
    }

    public function authUserPut($uri, $parameters = [])
    {
        $headers = ['Authorization' => 'Bearer '.$this->getAuthUserToken()];

        return $this->put($this->baseApi . $uri, $parameters, $headers);
    }

    public function authUserDelete($uri, $parameters = [])
    {
        $headers = ['Authorization' => 'Bearer '.$this->getAuthUserToken()];

        return $this->delete($this->baseApi . $uri, $parameters, $headers);
    }

    public function authUserCall($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $uri .= '?api_token='.$this->getAuthUserToken();

        return $this->call($method, $this->baseApi . $uri, $parameters, $cookies, $files, $server, $content);
    }

    protected function makeUserNonAdmin()
    {
        $this->user->designation = "designer";
        $this->user->save();
    }
}
