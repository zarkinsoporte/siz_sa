<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Hash;
use Input;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Session;
class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

//entre comillas la ruta a la que deseas redireccionar
    protected $redirectTo = 'home';


    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }


    public function postLogin(Request $request)
    {
        if ($request->get('password') != "1234"){
            try {
                if (Auth::attempt(['U_EmpGiro' => $request->get('id'), 'password'   => $request->get('password'), 'status' => 1])) {
                    //dd($request->all());
                    $user = Auth::user();
                    $apellido = Self::getApellidoPaternoUsuario(explode(' ',$user->lastName));
                    $actividades = $user->getTareas_ini();
                    $userNombre = explode(' ',$user->firstName)[0].' '.$apellido;
                    $userID = $user->U_EmpGiro;
                    $deptName = $user->getDepto();
                    session(['userActividades' => $actividades, 'userNombre'=> $userNombre, 'userID'=> $userID, 'deptName' => $deptName]);
                    //->withCookie(cookie()->forever('user', $user->U_EmpGiro.'-'. ))->withCookie(cookie()->forever('actividades', $actividades))
                    if(User::isProductionUser()){
                       Session::flash('send', 'send');
                       Session::flash('miusuario', '');
                       Session::flash('pass', '0123');
                       Session::flash('pass2', '1234');
                       return redirect()->action('Mod01_ProduccionController@traslados');   
                    }else{
                       return redirect()->intended('home');
                    }
                }else{
                    return redirect($this->loginPath())
                        ->withInput($request->only($this->loginUsername(), 'remember'))
                        ->withErrors('Usuario/contraseña inválidos, ó Baja');
                }
            } catch(\Exception $e) {
                echo ''. $e->getMessage();
            }
    
        }else{
            if (Auth::attempt(['U_EmpGiro' => $request->get('id'), 'password'   => $request->get('password'), 'status' => 1])) {
                //dd($request->all());                     
                //return view('auth.updatepassword');
                return redirect()->route('viewpassword');

            }else{
                return redirect($this->loginPath())
                    ->withInput($request->only($this->loginUsername(), 'remember'))
                    ->withErrors('Usuario/contraseña inválidos, ó Baja');
            }
           
        }

    }
   public function getApellidoPaternoUsuario($apellido){
        $preposiciones = ["DE", "LA", "LAS", "D", "LOS", "DEL"]; 
        if (in_array($apellido[0], $preposiciones) && count($apellido)>1 ) {
            if (in_array($apellido[1], $preposiciones) && count($apellido)>2 ) {
               return $apellido[0].' '.$apellido[1].' '.$apellido[2];
            } else {
                return $apellido[0].' '.$apellido[1];
            }            
        } else {
            return $apellido[0];
        }
    }
}
