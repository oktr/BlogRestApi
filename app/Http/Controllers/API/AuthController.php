<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;

class AuthController extends BaseController {

    public function signin( Request $request ) {

        if( Auth::attempt([ "email" => $request->email, "password" => $request->password ])) {

            $authUser = Auth::user();
            $success[ "token" ] = $authUser->createToken( "MyAuthApp" )->plainTextToken;
            $success[ "name" ] = $authUser->name;

            return $this->sendResponse( $success, "User signed in" );

        }else {

            return $this->sendError( "Unauthorised.", [ "error" => "Unathorized" ]);
        }
    }

    public function signup( Request $request ) {

        $validator = Validator::make( $request->all(), [
            "name" => "required",
            "email" => "required",
            "password" => "required",
            "confirm_password" => "required|same:password",
        ]);

        if( $validator->fails() ) {

            return $this->sendError( "Error validation", $validator->errors() );
        }

        $input = $request->all();
        $input[ "password" ] = bcrypt( $input[ "password" ]);
        $user = User::create( $input );
        $succes[ "token" ] = $user->createToken( "MyAuthApp" )->plainTextToken;
        $succes[ "name" ] = $user->name;

        return $this->sendResponse( $succes, "User created successfully" );
    }
}