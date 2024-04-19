<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use JWTAuth;

class userController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','registerUser', 'getUsers', 'editUser', 'deleteUser', 'getById']]);
    }

    public function login(Request $request){
        try{
            if(!Auth::attempt($request->only('email','password'))){
                return response()->json(['message'=>'unauthorized'],401);
            }
            $user= User::where('email',$request['email'])->addSelect([
                'rol'=>role::select('role')->whereColumn('role_id','id')])->firstOrFail();
            $user -> load('role');
            $token=JWTAuth::fromUser($user);
                Log::info('Token generado:' .$token);
                return response()->json([
                    'message'=>'Success: ',
                    'user' => $user,
                    'token' => $this->respondWithToken($token),
                ]);
        } catch (\Exeption $e){
            return response()->json([
                'message'=> 'Error al iniciar sesion',
                'error' => $e->getMessage()
            ],500);
        }
    }

    protected function respondWithToken($token)
    {
     $expiration = JWTAuth::factory()->getTTL() * 60;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration,
            'expiration_date' => now()->addSeconds($expiration)->toDateTimeString(),
        ]);
    }

    public function getById( int $id ) {
        $user = User::find($id);
        if ( is_null($user) ){
            return response() -> json('User not found', 404);
        }
        return response() -> json( $user, 200);
    }

    public function registerUser ( Request $request){
        try {
            $validators = Validator::make($request -> all(),
            [
                'username' => 'required|string|max:50',
                'name' => 'required|string|max:50',
                'email' => 'required|string|email|max:50|unique:users',
                'password' => 'required|string|min:6',
                'role_id' => 'required|integer',
            ]);

            if($validators->fails()){
                return response()->json($validators->errors()->toJson(),400);
            }
            $user = User::create([
                'username' => $request->get('username'),
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->get('password')),
                'role_id' => $request->get('role_id'),
            ]);

            $token = JWTAuth::fromUser($user);
            return response()->json(compact('user','token'),201);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Error al crear el usuario',
                'error'=>$e->getMessage()],500);
            // throw $th;
        }
    }

    public function logout(){
        $user = Auth::user();
        //Aun no hay JWT
        $userToken = $user->tokens();
        $userToken->delete();
        return response(['message'=>'Logged Out!!'],200);
    }

    public function getUsers(){
        $users = User::all();
        return response()->json($users, 200);
    }

    public function editUser( Request $request){
        $user = User::find($request -> id);
        if( is_null($user) ){
            return response()-> json('User not found', 404);
        }
        $user -> update([
            'name' => $request -> name,
            'username' => $request -> username,
            'email' => $request -> email,
            'role_id' => $request -> role_id,
        ]);

        return response() -> json($user,200);
    }

    public function deleteUser( int $id ){
        $user = User::find($id);
        if( is_null($user) ){
            return response()-> json('User not found', 404);
        }

        $user -> delete();

        return response() -> json('User Deleted',200);
    }
}
