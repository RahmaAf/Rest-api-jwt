<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Componenet\HttpFoundation\Response;
use Illuminate\Support\Facedes\Validator;

class ApiController extends Controller
{
    public function register(Request $request) {
        $data= $request->only('name','email','password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' =>'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        if($validator->fails()){
            return response()->json(['erorr'=> $validator->messages()], 200);
        }
        $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' =>bcrypt ($request->password)
        ]);
        return response()->json([
                'success'=> true,
                'messages' => 'User created succesfully',
                'data' => $user
        ], Response::HTTP_OK);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email','password');
        $validator = Validator::make($credentials,[
            'email' => 'rewuired|email',
            'password' => 'required|string|min:6|max:50'
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->messages],200);

        }
        try {
            if(!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'messages' => 'login credentials are invalid'
                ], 400);

                } 
            }catch (JWTException $e) {
                    return $credentials;
                    return response()->json([
                        'success' => false,
                        'message' => 'could not create token'
                    ], 500);
                }
                return response()->json([
                        'success' => true,
                        'token' => $token
                ],200);
        }
    }

