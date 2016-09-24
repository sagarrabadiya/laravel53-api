<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

class CompaniesTest extends TestCase
{
    /**
     * @var string
     */
    private $route = "/companies";


    /**
     * @var \App\Models\Company
     */
    private $tempCompany;

    /**
     * @param array $attributes
     * @return array
     */
    private function createCompany($attributes = array())
    {
        return factory(App\Models\Company::class)->create($attributes);
    }

    /**
     * method to test user can get his company
     */
    public function test_show_company_with_id()
    {
        $this->authUserGet($this->route."/".$this->company->id)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->company->id])
            ->assertResponseOk();
    }

    /**
     * method to test admin can update company
     */
    public function test_update_company_by_admin()
    {
        $this->authUserPut($this->route."/".$this->company->id, ['name' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['name'=>'updated'])
            ->assertResponseOk();
    }

    /**
     * method to test not allowed user can't access company
     */
    public function test_unauthorized_user_cant_access_company()
    {
        $this->tempCompany = $this->createCompany();
        $this->authUserGet($this->route."/".$this->tempCompany->id)
            ->assertResponseStatus(403);
    }

    /**
     * method to test that not allowed user can't update
     */
    public function test_not_allowed_user_cant_update()
    {
        $this->tempCompany = $this->createCompany();
        $this->authUserPut($this->route."/".$this->tempCompany->id)
            ->assertResponseStatus(403);
    }


    /**
     *  method to test non admin can't update company
     */
    public function test_unauthorized_user_cant_update()
    {
        $this->makeUserNonAdmin();
        $this->authUserPut($this->route."/".$this->company->id, ['name' => 'updated'])
            ->assertResponseStatus(403);
    }

    /**
     * @tearDown
     */
    public function tearDown()
    {
        if ($this->tempCompany) {
            $this->tempCompany->forceDelete();
        }
        parent::tearDown();
    }
}
