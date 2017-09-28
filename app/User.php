<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;
use Auth;
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
            $query
                 ->join('OUDP', 'OHEM.dept', '=', 'OUDP.Code')
                ->innerJoin('HEM6', 'OHEM.empID', '=', 'HEM6.empID')
                ->leftJoin('OHST', 'OHEM.status', '=', 'OHST.statusID')
                ->where(\DB::raw("(firstName + ' ' +lastName)"), "LIKE" , "%$name%");
        }

    }

    public function getTareas(){
        $actividades = DB::table('OHEM')
            ->leftJoin('HEM6', 'OHEM.empID', '=', 'HEM6.empID')
            ->join('OHTY', 'OHTY.typeID', '=', 'HEM6.roleID')
            ->leftJoin('MODULOS_GRUPO_SIZ','MODULOS_GRUPO_SIZ.id_grupo' ,'=', 'HEM6.roleID')
            ->leftJoin('MODULOS_SIZ','MODULOS_SIZ.id' ,'=', 'MODULOS_GRUPO_SIZ.id_modulo')
            ->leftJoin('MENU_ITEM_SIZ','MENU_ITEM_SIZ.id' ,'=', 'MODULOS_GRUPO_SIZ.id_menu')
            ->leftJoin('TAREA_MENU_SIZ','TAREA_MENU_SIZ.id' ,'=', 'MODULOS_GRUPO_SIZ.id_tarea')
            ->where('U_EmpGiro', Auth::user()->U_EmpGiro)
            ->where('HEM6.line', '1')
            ->whereNotNull('MODULOS_GRUPO_SIZ.id_tarea')
            ->select('MODULOS_GRUPO_SIZ.*',
                'MODULOS_SIZ.name AS modulo',
                'MENU_ITEM_SIZ.name AS menu',
                'TAREA_MENU_SIZ.name AS tarea')
            ->orderBy('id_menu', 'asc')
            ->orderBy('id_tarea', 'asc')
            ->get();
        return $actividades;
    }

    public static function getEstaciones($id){

    }

}
