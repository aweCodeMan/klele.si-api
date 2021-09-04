<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use App\Notifications\ResetPasswordLinkNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $primaryKey = 'uuid';

    protected $keyType = 'uuid';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'surname',
        'nickname',
        'full_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordLinkNotification($token));
    }

    public function markNotificationAsRead($id)
    {
        $notification = $this->notifications()->where('id', $id)->firstOrFail();

        if (!$notification->read_at) {
            $notification->markAsRead();

            DB::table('user_counters')->where('user_uuid', '=', $this->uuid)->decrement('number_of_unread_notifications');
        }

        return $notification->refresh();
    }

    public function markAllNotificationsAsRead()
    {
        $this->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        DB::table('user_counters')->where('user_uuid', '=', $this->uuid)->update(['number_of_unread_notifications' => 0]);
    }
}
