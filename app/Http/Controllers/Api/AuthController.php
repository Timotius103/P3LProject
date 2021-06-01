<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Karyawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;
use Carbon\Carbon;


class AuthController extends Controller
{
    public function login(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email_karyawan' => 'required|email:rfc,dns',
            'password' => 'required'
        ]); //membuat rule validasi input

        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input

        if(!Auth::attempt($loginData))
            return response(['message' => 'Invalid Credentials'],401); //return error gagal login

        $user = Auth::user();

        if ($user->status_karyawan == 'Inactive') {
            return response(['message' => 'Akun Anda Sudah Tidak Aktif.'],400);
        }

        $token = $user->createToken('Authentication Token')->accessToken; //generate token

        return response([
            'message' => 'Authenticated',
            'karyawan' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]); //return data user dan token dalam bentuk json
    }

    public function logout(){
        Auth::Guard('api')->user()->token()->delete();
        return response([
            'message' => 'Logged Out Successfully'
        ]);
    }

    public function index(){
        $karyawan = karyawan::all();
        if(count($karyawan)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $karyawan
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function waiterFind(){
//        $karyawan = karyawan::all();
        $karyawan = DB::table('karyawan')
            ->where('jabatan','=','Opr Manager')
            ->orWhere('jabatan','=','Waiter')
            ->where('status_karyawan','=','Active')
            ->get();
        if(count($karyawan)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $karyawan
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }


    public function show($id){
        $karyawan = karyawan::find($id);

        if(!is_null($karyawan)){
            return response([
                'message'=>'Retrive Karyawan Success',
                'data'=> $karyawan
            ],200);
        }

        return response([
            'message'=>'Karyawan Not Found',
            'data'=>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'nama_karyawan' => 'required',
            'jenis_kelamin'=>'required',
            'tlp_karyawan' =>'regex:/^(08)[0-9]{8,11}$/',
            'jabatan'=>'required',
            'email_karyawan'=>'required|max:60|email:rfc,dns',
            'tgl_bergabung'=>'required|date',
            'status_karyawan'=>'required',
            'password'=>'required',
        ]);
        if($validate->fails())
            return response(['message' => $validate->errors()],460);

        $storeData['password'] = bcrypt($request->password);
        $karyawan = karyawan::create($storeData);
        return response([
            'message' => 'Add karyawan Success',
            'data' => $karyawan,
        ],200);

    }

    public function destroy($id){
        $karyawan=karyawan::find($id);

        if(is_null($karyawan)){
            return response([
                'message'=>'karyawan Not Found',
                'data'=>null,
            ],400);
        }

        if($karyawan->delete()){
            return response([
                'message'=>'Delete karyawan Success',
                'data'=>$karyawan,
            ],200);
        }

        return response([
            'message'=>'Delete karyawan Failed',
            'data'=>null,
        ],400);
    }


//    public function update(Request $request,$id){
//        $karyawan = karyawan::find($id);
//        if(is_null($karyawan)){
//            return response([
//                'message'=>'karyawan not Found',
//                'data'=>null,
//            ],404);
//        }
//
//        $updateData = $request->all();
//        $validate = Validator::make($updateData,[
//            'nama_karyawan' => 'required',
//            'jenis_kelamin'=>'required',
//            'tlp_karyawan' =>'regex:/^(08)[0-9]{8,11}$/',
//            'jabatan'=>'required',
//            'email_karyawan'=>'required|max:60|email:rfc,dns',
//            'tgl_bergabung'=>'required|date',
//            'status_karyawan'=>'required',
//            'password'=>'required',
//        ]);
//
//        if ($validate->fails())
//            return response(['message' => $validate->errors()], 400);
////        $updateData['tgl_bergabung'] = Carbon::createFromFormat('Y-m-d',$updateData['tgl_bergabung']);
//        $karyawan->nama_karyawan = $updateData['nama_karyawan'];
//        $karyawan->jenis_kelamin = $updateData['jenis_kelamin'];
//        $karyawan->tlp_karyawan = $updateData['tlp_karyawan'];
//        $karyawan->jabatan = $updateData['jabatan'];
//        $karyawan->email_karyawan = $updateData['email_karyawan'];
//        $karyawan->tgl_bergabung = $updateData['tgl_bergabung'];
//        $karyawan->status_karyawan = $updateData['status_karyawan'];
//
//        if ($karyawan->save()) {
//            return response([
//                'message' => 'Update Karyawan Success',
//                'data' => $karyawan,
//            ], 200);
//        }
//        return response([
//            'message' => 'Update Karyawan Failed',
//            'data' => null,
//        ], 400);
//    }

    //Untuk Ganti Password
    public function update(Request $request,$id){
//        $uploadFolder = 'gambarUser';

        $karyawan = karyawan::find($id);
        if(is_null($karyawan)){
            return response([
                'message' => 'User Not Found',
                'data' => null
            ],404);
        }
        $updateData = $request->all();
        if($request->password!=null || $request->newPassword!=null || $request->newPasswordConfirm!=null){
            $validate = Validator::make($updateData,[
//                'email' => ['max:60|email:rfc,dns', Rule::unique('users')->ignore($karyawan)],
//                'name' => 'required',
                'password' => 'required',
                'newPassword' => 'required',
                'newPasswordConfirm' => 'required|same:newPassword',
//                'fileUpload' => 'mimes:jpg,jpeg,png'
            ]);

            if($validate->fails())
            {
                return response(['message' => $validate->errors()],400);
            }else if(Hash::check($updateData['password'], $karyawan->password)){
                $updateData['newPassword'] = bcrypt($request->newPassword);
//                $karyawan->name = $updateData['name'];
//                $karyawan->email = $updateData['email'];
                $karyawan->password = $updateData['newPassword'];

//                $file = $request->file('fileUpload');
//                if($file!=null){
//
//                    $file_upload_path = $file->store($uploadFolder,'public');
//                    $uploadImageResponse = array(
//                        "image_name" => basename($file_upload_path),
//                        "image_url" => Storage::disk('public')->url($file_upload_path),
//                        "mime" => $file->getClientMimeType()
//                    );
//                    Storage::disk('public')->delete('gambarUser/' . $karyawan->fileUpload);
//                    $user->fileUpload = basename($file_upload_path);
//                }
            }
            else{
                return response(['message' => 'Old Password is not matched in database !'],400);
            }
        }else{
            $validate = Validator::make($updateData,[
                'nama_karyawan' => 'required',
                'jenis_kelamin'=>'required',
                'tlp_karyawan' =>'regex:/^(08)[0-9]{8,11}$/',
                'jabatan'=>'required',
                'email_karyawan'=>'required|max:60|email:rfc,dns',
                'tgl_bergabung'=>'required|date',
                'status_karyawan'=>'required',
            ]);
            if($validate->fails()){
                return response(['message' => $validate->errors()],400);
            }else{
                $karyawan->nama_karyawan = $updateData['nama_karyawan'];
                $karyawan->jenis_kelamin = $updateData['jenis_kelamin'];
                $karyawan->tlp_karyawan = $updateData['tlp_karyawan'];
                $karyawan->jabatan = $updateData['jabatan'];
                $karyawan->email_karyawan = $updateData['email_karyawan'];
                $karyawan->tgl_bergabung = $updateData['tgl_bergabung'];
                $karyawan->status_karyawan = $updateData['status_karyawan'];
            }
        }
        if($karyawan->save()){
            return response([
                'message' => 'Update Karyawan Success',
                'data' => $karyawan,
            ],200);
        }

        return response([
            'message' => 'Update Karyawan Failed',
            'data' => null,
        ],400);

    }
}
