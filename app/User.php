<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dbo.OHEM';
    protected $primaryKey = 'U_EmpGiro';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'U_CP_Password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'U_CP_Password', 'U_remember_token',
    ];


    public function getAuthPassword()
    {
        return $this->U_CP_Password;
    }

    public function getRememberToken()
    {
        return $this->U_remember_token;
    }

    public function setRememberToken($value)
    {
        $this->U_remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'U_remember_token';
    }

    Public function  scopeName($query, $name){
        if (trim($name) != ""){
            $query->where(\DB::raw("(firstName + ' ' +lastName)"), "LIKE" , "%$name%");
        }

    }
}
