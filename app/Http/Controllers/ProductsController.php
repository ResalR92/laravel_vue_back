<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Auth;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $products = Product::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //exploded img->base64
        $exploded = explode(',', $request->image);

        $decoded = base64_decode($exploded[1]);
        //extension
        if(str_contains($exploded[0],'jpeg')) {
            $extension = 'jpg';
        }
        else {
            $extension = 'png';
        }
        $fileName = str_random().'.'.$extension;
        $path = public_path().'/img/'.$fileName;

        file_put_contents($path, $decoded);

        $product = Product::create($request->except('image') + ['user_id'=> Auth::id(),'image'=>$fileName]);

        return $product;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if(count($product) > 0) {
            return response()->json($product);
        }
        return response()->json(['error'=>'Resource not found'],404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        $product->update($request->all());

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Product::destroy($id);

            return response([],204);
        } catch (Exception $e) {
            return response(['Problem deleting the product',500]);
        }
    }
}
