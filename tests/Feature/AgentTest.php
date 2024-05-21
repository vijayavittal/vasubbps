<?php

namespace Tests\Feature;

use App\InstantPay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AgentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testMain()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testAgentLoginPage()
    {
        $res = $this->get('/login');

        $res->assertOk();
    }

    public function testAgentRegistrationPage()
    {
        $res = $this->get('/register');

        $res->assertOk();


    }

    public function testDashboadPage()
    {
        $this->withoutExceptionHandling();

        $res = $this->get('/dashboard');

        $instantPay = new InstantPay();

        $this->assertTrue($instantPay->registrationOTP("9483345594"));

        $data = [
            'name'=>'Ganesh P Bhat',
            'pan'=>'BMBPB9088H',
            'otp'=>'125864',
            'phone'=>'9483345594',
            'email'=>'ganesh@gmail.com',
            'company'=>'Ganesh stores',
            'password'=>'ganesh@3105',
            'password_confirmation'=>'ganesh@3105',
            'mobile'=>'9483345594',
            'address'=>'sirsi',
            'pincode'=>'500094',
        ];

        $handle = fopen("php://stdin","r"); // read from STDIN
        $line = trim(fgets($handle));

        $data['otp'] = $line;

        $this->post('/register',$data);

        $res->assertOk();
    }



}
