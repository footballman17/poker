<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'login', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function friends()
    {
        return $this->belongsToMany('App\User', 'friends', 'user1_id', 'user2_id');
    }

    public function rooms()
    {
        return $this->belongsToMany('App\Room', 'user_room');
    }

    public function invitations()
    {
        return $this->belongsToMany('App\User', 'invitations', 'id_src_user', 'id_dst_user');
    }
}
