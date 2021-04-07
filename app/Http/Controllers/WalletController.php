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
    /**
     * If the wallet id is found and is related to the user,
     * checks the wallet value and creates a transaction record
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws WalletNotFoundException
     */
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

    /**
     * If id is set to zero, returns all transactions related to user.
     * Otherwise, returns transactions performed on selected wallet
     *
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getTransactions ($id = 0) {
        $user = Auth::user();
        if ($id == 0)
            return TransactionResource::collection($user->transactions()->get());
        else {
            $wallet = $user->wallets()->find($id);
            return TransactionResource::collection($wallet->transactions()->get());
        }
    }

    /**
     * If id is set to zero, returns all wallets the user owns.
     * Otherwise, returns details of selected wallet
     *
     * @param int $id
     * @return WalletCollection
     */
    public function view ($id = 0) {
        $user = Auth::user();
        $wallets = $user->wallets();
        if ($id != 0)
            $wallets->where('id', $id);
        $wallets = $wallets->get();
        WalletCollection::withoutWrapping();
        return new WalletCollection($wallets);
    }

    /**
     * If the user owns the selected wallet and if it is empty,
     * the function deletes the wallet record from database
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws WalletNotFoundException
     */
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

    /**
     * Given a unique name and a proper type,
     * creates a new wallet for the user with no credit in it
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
