<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::with(['User'])->get();

        if(count($subscriptions) > 0){
            return response([
                'message' => 'Retrieve all subscriptions data success',
                'data' => $subscriptions
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $subscriptionsData = $request->all();

        $validate = Validator::make($subscriptionsData, [
            'id_user' => 'required',
            'category' => 'required',
        ]);

        if($validate->fails())
            return response()->json(['message' => $validate->errors()], 400);    
        
        
        if($subscriptionsData['category'] == 'Basic'){
            $price = 50000;
        } else if($subscriptionsData['category'] == 'Standard'){
            $price = 100000;
        } else if($subscriptionsData['category'] == 'Premium'){
            $price = 150000;
        } else {
            return response()->json(['message' => 'Inputan Category Invalid. Hanya Basic, Standard dan Premium saja yang diterima'], 400);
        }

        $idUserToAktif = $subscriptionsData['id_user'];
        $isUserExists = DB::table('users')->where(['id' => $idUserToAktif])->first();

        if(is_null($isUserExists)){
            return response(['message' => 'User Not Found'], 400);
        }

        if($isUserExists->status == 1){
            return response([
                'message' => 'User Already Active',
            ], 400);
        }
        
        DB::table('users')->where(['id' => $idUserToAktif])->update(['status' => 1]);

        $subscriptionsData['price'] = $price;
        $subscriptionsData['transaction_date'] = date('Y-m-d H:i:s');

        $subscription = Subscription::create($subscriptionsData);

        return response([
            'message' => 'Add Subscription success',
            'data' => $subscription
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subscription = Subscription::find($id);

        if(!is_null($subscription)){
            return response([
                'message' => 'Subscription Found',
                'data' => $subscription
            ], 200);
        }

        return response([
            'message' => 'Subscription not found',
            'data' => null
        ], 400);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $subscriptionUpdate = $request->all();

        $validate = Validator::make($subscriptionUpdate, [
            'id_user' => 'required',
            'category' => 'required'
        ]);

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }

        if($subscriptionUpdate['category'] == 'Basic'){
            $price = 50000;
        } else if($subscriptionUpdate['category'] == 'Standard'){
            $price = 100000;
        } else if($subscriptionUpdate['category'] == 'Premium'){
            $price = 150000;
        } else {
            return response()->json(['message' => 'Inputan Category Invalid. Hanya Basic, Standard dan Premium saja yang diterima'], 400);
        }

        // Cari user dari inputan id_user nya yang baru (dari inputan update nya)
        $findUser = User::find($subscriptionUpdate['id_user']);

        // kalo data user tidak ditemukan brarti kirim message user not found 400
        if(is_null($findUser)){
            return response(['message' => 'User not found'], 400);
        }

        // kalo data user ada tapi ternyata yang ditunjuk itu user belum aktif, brarti kirim message user belum aktif 403 (Forbidden)
        if($findUser->status == 0){
            return response(['message' => 'Tidak bisa update data subscription pada user yang belum aktif'], 403);
        }

        // cari data subscription nya
        $findSubscription = Subscription::find($id);

        // kalo ternyata gaada, kirim message subscription not found 400
        if(is_null($findSubscription)){
            return response(['message' => 'Subscription Not Found'], 400);
        }

        // kalo ada brarti langsung update
        $findSubscription->category = $subscriptionUpdate['category'];
        $findSubscription->price = $price;
        
        // Cuma notes doang sih kak, tapi coba dibaca kak :)
        // saya masih bingung disini ketika update itu apakah brarti jika user yang ditemukan sudah aktif sebelumnya, data subscription
        // yang di id subscription yang ditunjuk itu id_user nya ganti jadi id_user yang ditemukan aktif itu atau tidak.
        // Kalau iya, brarti nanti ada id_user yang dobel subscription nya di tabel subscriptions dan brarti CODE DIBAWAH di uncomment.
        // Tapi kalau engga brarti yasudah hehe :) Makasi kak...

        // code ini maksudnya
        // $findSubscription->id_user = $subscriptionUpdate['id_user'];


        if($findSubscription->save()){
            return response([
                'message' => 'Update Subscription Success',
                'data' => $findSubscription
            ], 200);
        }

        // terjadi kesalahan tertentu yang tidak diketahui, brarti kirim ini
        return response([
            'message' => 'Update Subscriptions failed',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subscription = Subscription::find($id);

        if(is_null($subscription)){
            return response([
                'message' => 'Subscription not found',
                'data' => null
            ], 400);
        }

        $user = User::find($subscription['id_user']);

        if(!is_null($user)){
            DB::table('users')->where(['id' => $user->id])->update(['status' => 0]);
        }

        if($subscription->delete()){
            return response([
                'message' => 'Delete Subscription Success',
                'data' => $subscription
            ], 200);
        }

        return response([
            'message' => 'Delete Subscription Failed',
            'data' => null
        ], 400);
    }
}
