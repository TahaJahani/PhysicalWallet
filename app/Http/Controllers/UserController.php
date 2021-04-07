<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\SafePassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Given a name, email and password, the function
     * checks for email duplication and registers the user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', new SafePassword()],
        ]);
        if ($validator->fails())
            return response()->json(['errors'=> $validator->errors()], 400);
        if (User::where('email', $request->email)->exists())
            return response()->json(['error' => 'کاربری با این آدرس ایمیل وجود دارد'], 400);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        return response()->json(['message'=>'کاربر با موفقیت ثبت نام شد']);
    }

    /**
     * Checks the given email and password, if no problem
     * occurs, creates a token and returns
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);
        $user = User::where('email', $request->email)->first();
        if (is_null($user) || !Hash::check($request->password, $user->password))
            return response()->json(['error'=>'نام کاربری یا رمز عبور نادرست است'], 403);
        $token = $user->createToken(now())->plainTextToken;
        return response()->json(['message'=>'خوش آمدید', 'token'=>$token]);
    }

    /**
     * Logs the user out and removes the saved token
     *
     * @return JsonResponse
     */
    public function logout (){
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response()->json(['message' => 'خدانگهدار!']);
    }
}
