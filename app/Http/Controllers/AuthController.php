<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        $credenciais = $request->all(['email', 'password']);

        //autenticacao (email e senha)
        $token = auth('api')->attempt($credenciais);

        if($token){ //usuario autenticado com sucesso
            return response()->json(['token' => $token]);
        } else { // erro de usuario e senha
            return response()->json(['erro' => 'Usuario ou senha invalidos'], 403);

            //401 = Unauthorized -> não autorizado
            //403 = forbidden -> proibido (login invalido)
        }
        //retornar um json web token
        return 'login';
    }

    public function logout(){
        auth('api')->logout();
        return response()->json(['msg' => 'Logout realizado com sucesso.']);
    }

    public function refresh(){
        $token = auth('api')->refresh(); //client encaminhe um jwt valido
        return response()->json(['token' => $token]);
    }

    public function me(){
        return response()->json(auth()->user());
    }

}
