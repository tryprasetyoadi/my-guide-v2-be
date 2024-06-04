<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    public function me()
    {
        $user = Auth::user();
        $me = [];
        if ($user->roles_id == 1) {
            $me['countUser'] = User::count();
            $me['countWisata'] = Product::count();
            $me['user'] = User::where('id', $user->id)->first();
            $me['lastuser'] = $me['lastuser'] = User::selectRaw("DATE(created_at) as created_at, id, name")
                ->orderBy("created_at", "DESC")
                ->limit(10)
                ->get();
            $me['lastwisata'] = Product::orderBy("created_at", "DESC")->limit(10)->get();
        } else if ($user->roles_id == 3) {
            $me['countWisata'] = Product::where('user_id', $user->id)->count();
            $me['user'] = User::where('id', $user->id)->first();
            $me['lastwisata'] = Product::where('user_id', $user->id)->orderBy("created_at", "DESC")->limit(10)->get();
        } else {
            $me['user'] = User::where('id', $user->id)->first();
        }

        return response()->json(["data" => $me, "message" => 'User Fetched Successfully'], 200);
    }

    public function index()
    {
        $user = User::selectRaw('users.name as name, users.email as email, roles.role_name as role_name, users.id as id')->join('roles', 'roles.id', '=', 'users.roles_id')->orderBy('users.id', 'ASC')->paginate();
        return response()->json(['data' => $user, 'message' => 'User Fetched Successfully'], 200);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $user = User::where('name', $search)->first();

        return response()->json(['data' => $user, 'message' => 'User Fetched Successfully'], 200);
    }

    public function show($id)
    {
        $user = User::find($id);
        return response()->json(['data' => $user, 'message' => 'User Fetched successfully'], 200);
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'email' => 'required|string|email:rfc,dns|max:250',
            'roles_id' => 'required|exists:roles,id'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }
        $user = User::find($id);
        if ($user) {

            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->password != '') {
                $user->password = bcrypt($request->password);
            }
            $user->roles_id = $request->roles_id;
            if ($user->update()) {
                return response()->json(['data' => $user, 'message' => 'User Updated Successfully'], 200);
            };
        }

        return response()->json(['message' => 'error! user tidak ditemukan'], 400);
    }

    public function destroy($id)
    {
        if (User::destroy($id)) {

            return response()->json(['message' => 'User Deleted Successfully'], 200);
        };
        return response()->json(['message' => 'error! user tidak ditemukan'], 400);
    }
}
