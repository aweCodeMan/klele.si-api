<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_user()
    {
        $data = $this->getUserRegisterData();

        $response = $this->post(route('users.register'), $data)->assertStatus(200);

        $this->assertDatabaseHas(User::class, ['email' => $data['email'], 'name' => $data['name'], 'surname' => $data['surname'], 'full_name' => "{$data['name']} {$data['surname']}"]);
    }

    /** @test */
    public function it_fails_if_user_exists()
    {
        $user = User::factory()->create();

        $data = $this->getUserRegisterData();

        $data['email'] = $user->email;

        $response = $this->post(route('users.register'), $data)->assertStatus(422);
    }

    /** @test */
    public function it_sends_a_verification_email_when_a_user_registers()
    {
        Notification::fake();

        $data = $this->getUserRegisterData();

        $response = $this->post(route('users.register'), $data)->assertStatus(200);

        Notification::assertSentTo(User::first(), VerifyEmail::class);
    }

    /** @test */
    public function it_verifies_a_users_email()
    {
        $data = $this->getUserRegisterData();

        $response = $this->post(route('users.register'), $data)->assertStatus(200);

        $this->assertDatabaseHas(User::class, ['email' => $data['email'], 'email_verified_at' => null]);

        $user = User::first();
        $url = URL::temporarySignedRoute('verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $response = $this->get($url)->assertStatus(200);

        $this->assertDatabaseMissing(User::class, ['email' => $data['email'], 'email_verified_at' => null]);
    }

    /** @test */
    public function it_resends_a_verification_email()
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->actingAs($user, 'api')->post(route('users.reverify'), [])->assertStatus(200);

        Notification::assertSentTo(User::first(), VerifyEmail::class);
    }

    /** @test */
    public function it_sends_a_forgot_password_email()
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('forgot-password'), ['email' => $user->email])->assertStatus(200);

        Notification::assertSentTo(User::first(), ResetPassword::class);
    }

    /** @test */
    public function it_resets_a_password_email()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);
        $password = $user->password;

        $this->post(route('password.reset'), ['email' => $user->email, 'token' => $token, 'password' => 'newSecretSecret'])->assertStatus(200);

        $user = $user->refresh();
        $this->assertNotSame($password, $user->password);
    }

    private function getUserRegisterData(): array
    {
        return [
            'email' => 'example@example.com',
            'password' => 'secretSecret',
            'name' => 'John',
            'surname' => 'Walker',
        ];
    }
}
