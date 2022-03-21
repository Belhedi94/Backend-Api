<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('create-user', function () {
            return auth()->user()->is_admin == true;
        });
        Gate::define('update-user', function ($user, $id) {
            return auth()->user()->id == $id;
        });
        Gate::define('get-users', function () {
            return auth()->user()->is_admin == true;
        });
        Gate::define('ban-user', function () {
            return auth()->user()->is_admin == true;
        });
        Gate::define('delete-user', function () {
            return auth()->user()->is_admin == true;
        });
        Gate::define('get_admins', function () {
            return auth()->user()->is_admin == true;
        });
        Gate::define('get-banned-users', function () {
            return auth()->user()->is_admin == true;
        });Gate::define('get-active-users', function () {
            return auth()->user()->is_admin == true;
        });


        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $url);
        });
    }
}
