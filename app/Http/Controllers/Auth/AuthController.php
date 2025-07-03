<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validated =Validator::make($request->all(),[
            'full_name'=>'required|string',
            'email'=>'required|email',
            'password'=>'required|confirmed|min:6'
        ]);
        if($validated->fails()){
            return response()->json(["errors"=>$validated->errors()]);
        }
        $user=User::where('email',$request->email)->first();
        if($user){
            return response()->json(["errors"=>"User already exists"]);
        }
        try {

            $user =User::create([
                'full_name'=>$request->full_name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password)

            ]);
            $token=$user->createToken('token')->plainTextToken;
            $user['token']=$token;
            return response()->json(["data"=>$user,"message"=>"User created successfully"]);

        }catch (\Exception $e){
            return response()->json(["errors"=>$e->getMessage()]);

        }
    }

    public function login(Request $request){
        $validator =Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'

        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }
        try {
            $credentials =$request->only('email','password');
            if(!auth()->attempt($credentials)){
                return response()->json(['errors'=>'Invalid credentials']);
            }
            $user =User::where('email',$request->email)->first();
            $token =$user->createToken('token')->plainTextToken;
            $user['token']=$token;
            return response()->json(['message'=>"Login successful","data"=>$user]);

        }catch(\Exception $e){
            return response()->json(['errors'=>$e->getMessage()]);
        }
    }
}
