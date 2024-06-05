<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user()->roles_id;

        if ($user == 1) {
            $products = Product::latest()->paginate(10);
            $response = [
                'cek' => 1,
                'status' => 'success',
                'message' => 'Tours are retrieved successfully.',
                'data' => $products,
            ];

            return response()->json($response, 200);
        } else if ($user == 3) {
            $products = Product::where('user_id', $user)->paginate(10);
            $response = [
                'cek' => 2,
                'status' => 'success',
                'message' => 'Tours are retrieved successfully.',
                'data' => $products,
            ];

            return response()->json($response, 200);
        }
        $response = [
            'cek' => $user,
            'status' => 'success',
            'message' => 'Tours are retrieved successfully.',
            'data' => null,
        ];

        return response()->json($response, 200);
    }

    public function getWisata()
    {
        $wisata = Product::selectRaw('users.id as tourguide_id, users.name as tourguide_name, users.email as tourguide_email, products.*')->join('users', 'users.id', '=', 'products.user_id')->get();
        return response()->json(['data' => $wisata, 'message' => 'wisata successfully fetched!'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'description' => 'required|string|',
            'price' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png|max:2048',
            'alamat' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $tour = new Product();

        $tour->user_id = Auth::user()->id;
        $tour->name = $request->name;
        $tour->description = $request->description;
        if ($request->file('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $filename = time() . '-' . $extension;
            $path = 'uploads/category/';
            $image->move($path, $filename);
            $tour->image =  $path . $filename;
        }
        $tour->price = $request->price;
        $tour->alamat = $request->alamat;
        $tour->save();


        $response = [
            'status' => 'success',
            'message' => 'Tours is added successfully.',
            'data' => $tour,
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Tours is not found!',
            ], 200);
        }

        $response = [
            'status' => 'success',
            'message' => 'Tours is retrieved successfully.',
            'data' => $product,
        ];

        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'mimes:jpg,jpeg,png|max:2048',
            'alamat' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Tour is not found!',
            ], 200);
        }

        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->alamat = $request->alamat;
        if ($request->file('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $filename = time() . '-' . $extension;
            $path = 'uploads/category/';
            $image->move($path, $filename);
            $product->image =  $path . $filename;
        }
        $save = $product->save();

        if ($save) {
            $response = [
                'status' => 'success',
                'message' => 'Tour is updated successfully.',
                'data' => $product,
            ];

            return response()->json($response, 200);
        }
        $response = [
            'status' => 'success',
            'message' => 'Tour  update failed.',
            'data' => null,
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Tour is not found!',
            ], 200);
        }

        Product::destroy($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Tour is deleted successfully.'
        ], 200);
    }

    /**
     * Search by a product name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        $products = Product::where('name', 'like', '%' . $name . '%')
            ->latest()->get();

        if (is_null($products->first())) {
            return response()->json([
                'status' => 'failed',
                'message' => 'No tour found!',
            ], 200);
        }

        $response = [
            'status' => 'success',
            'message' => 'Tours are retrieved successfully.',
            'data' => $products,
        ];

        return response()->json($response, 200);
    }
}
