<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\customer;

class CustomerController extends Controller
{
    public function index(){
        $customer = customer::all();
        if(count($customer)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $customer
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $customer = customer::find($id);

        if(!is_null($customer)){
            return response([
                'message'=>'Retrive Customer Success',
                'data'=> $customer
            ],200);
        }

        return response([
            'message'=>'Customer Not Found',
            'data'=>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'nama_customer' => 'required',
            'telepon_customer' => 'required|numeric',
            'email_customer' => ''
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],460);

        $customer = customer::create($storeData);
        return response([
            'message' => 'Add Customer Success',
            'data' => $customer,
        ],200);

//        $storeData = $request->all();
//        $validate = Validator::make($storeData,[
//            'nama_customer' => 'required|max:60|unique:customer',
//            'telepon_customer' => 'required|max:12',
//            'email_customer' => 'required|email:rfc,dns',
//        ],200);
//
//        if($validate->fails())
//            return response(['message'=>$validate->errors()],400);
//
//        $customer = customer::create($storeData);
//        return response([
//            'message'=>'Customer Added',
//            'data'=>$customer,
//        ],200);
    }

    public function destroy($id){
        $customer=customer::find($id);

        if(is_null($customer)){
            return response([
                'message'=>'Customer Not Found',
                'data'=>null,
            ],400);
        }

        if($customer->delete()){
            return response([
                'message'=>'Delete Customer Success',
                'data'=>$customer,
            ],200);
        }

        return response([
            'message'=>'Delete Customer Failed',
            'data'=>null,
        ],400);
    }

    public function update(Request $request,$id){
        $customer = customer::find($id);
        if(is_null($customer)){
            return response([
                'message'=>'Customer not Found',
                'data'=>null,
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'nama_customer' => 'required',
            'telepon_customer' => 'required|numeric',
            'email_customer' => ''
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $customer->nama_customer = $updateData['nama_customer'];
        $customer->telepon_customer = $updateData['telepon_customer'];
        $customer->email_customer = $updateData['email_customer'];

        if ($customer->save()) {
            return response([
                'message' => 'Update Product Success',
                'data' => $customer,
            ], 200);
        }
        return response([
            'message' => 'Update Product Failed',
            'data' => null,
        ], 400);
    }
}
