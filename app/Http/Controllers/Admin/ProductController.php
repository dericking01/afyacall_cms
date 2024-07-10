<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Service;
use App\Models\Opt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController 
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return view('admin.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $product = new Product();
        $product->name = $request->name;
        $product->shortcode = $request->shortcode;
        $product->product_ID = $request->product_ID;
        $product->description = $request->description;
        $product->status = 1;
        $product->price = $request->price;
        $product->user_id = Auth::id();
        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'product Saved Successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('admin.product.edit',compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $productupdate = Product::findOrFail($product->id);
        $productupdate->name = $request->name;
        $productupdate->shortcode = $request->shortcode;
        $productupdate->product_ID = $request->product_ID;
        $productupdate->description = $request->description;
        $productupdate->status = 1;
        $productupdate->price = $request->price;
        $productupdate->user_id = Auth::id();
        $productupdate->update();

        return redirect()->route('admin.products.index')->with('success', 'Product Updated Successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
