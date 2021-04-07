<?php

namespace App\Http\Controllers;

use App\Exceptions\WalletNotFoundException;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\WalletCollection;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    public function makeTransaction ($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'value' => 'required|numeric',
        ]);
        if ($validator->fails())
            return response()->json(['error' => $validator->errors()], 400);
        $user = Auth::user();
        $wallet = $user->wallets()->find($id);
        if (is_null($wallet))
            throw new WalletNotFoundException();
        if ($wallet->value + $request->value < 0)
            return response()->json(['error' => 'اجازه برداشت بیش از مقدار کیف پول را ندارید'], 403);
        $wallet->transactions()->create([
            'value' => $request->value,
        ]);
        $wallet->value += $request->value;
        $wallet->save();
        return response()->json(['message' => 'تراکنش با موفقیت انجام شد']);
    }

    public function getTransactions ($id = 0) {
        $user = Auth::user();
        if ($id == 0)
            return TransactionResource::collection($user->transactions()->get());
        else {
            $wallet = $user->wallets()->find($id);
            return TransactionResource::collection($wallet->transactions()->get());
        }
    }

    public function view ($id = 0) {
        $user = Auth::user();
        $wallets = $user->wallets();
        if ($id != 0)
            $wallets->where('id', $id);
        $wallets = $wallets->get();
        WalletCollection::withoutWrapping();
        return new WalletCollection($wallets);
    }

    public function delete ($id) {
        $user = Auth::user();
        $wallet = $user->wallets()->find($id);
        if (is_null($wallet))
            throw new WalletNotFoundException();
        if ($wallet->value != 0)
            return response()->json(['error' => 'برای حذف کیف پول باید موجودی آن را خالی کنید'], 403);
        $wallet->delete();
        return response()->json(['message'=>'کیف پول مورد نظر با موفقیت حذف شد']);
    }

    public function add (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => ['required', Rule::in(Wallet::$types)],
        ]);
        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);
        $user = Auth::user();
        if ($user->wallets()->where('name', $request->name)->exists())
            return response()->json(['error'=>'کیف پول با این نام وجود دارد'], 400);
        $user->wallets()->create([
            'name' => $request->name,
            'type' => $request->type,
            'value' => 0,
        ]);
        return response()->json(['message'=>'کیف پول '.$request->name.' با موفقیت ایجاد شد']);
    }
}
