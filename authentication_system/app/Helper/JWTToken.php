<?php

namespace App\Helper;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken
{

    public static function CreateJWTToken($userEmail):string{
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'authentication_system',
            'iat' => time(),
            'exp' => time()+ 60*60,
            'userEmail' =>$userEmail
        ];
        return JWT::encode($payload, $key, 'HS256');
    }

    public static function CreateTokenForResetPass($userEmail):string{
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'authentication_system',
            'iat' => time(),
            'exp' => time()+ 60*3,
            'userEmail' =>$userEmail
        ];
        return JWT::encode($payload, $key, 'HS256');
    }


    public function VarifyJWTToken($token):string{
        try{
            $key = env('JWT_KEY');
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            return $decode->userEmail;
        }
        catch(Exception $e){
            return 'Timeout, unauthorized.';
        }
    }

}
