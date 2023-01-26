<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use App\Http\Resources\Product as ProductResource;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductController extends BaseController
{
    public function index()
    {
        $products = Product::all();

        return $this->sendResponse(ProductResource::collection($products), "Adatok megjelenítve");
    }

    public function show($id)
    {
        $product = Product::find($id);

        if(is_null($product))
        {
            return $this->sendError("Termék nem létezik");
        }

        return $this->sendResponse(new ProductResource($product), "üzenet");
    }

    public function create(Request $request)
    {
        $input = $request->all();
        DB::statement("ALTER TABLE products AUTO_INCREMENT = 1;");
        $validator = Validator::make($input,
            [
                "name" => "required",
                "itemnumber" => "required",
                "quantity" => "required",
                "price" => "required"
            ],
            [
                "name.required" => "A Név megadása kötelező!",
                "itemnumber.required" => "A cikkszám megadása kötelező!",
                "price.required" => "Az ár megadása kötelező!",
                "quantity.required" => "A mennyiség megadása kötelező!",
               
            ]
    
        );

        if($validator->fails())
        {
            return $this->sendError($validator, "Hiba!");
        }

        $input = Product::create($input);

        return $this->sendResponse(new ProductResource($input), "Üzenet");
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input,
            [
                "name" => "required",
                "itemnumber" => "required",
                "quantity" => "required",
                "price" => "required"
                
            ]
        );

        if($validator->fails())
        {
            return $this->sendError($validator, "Hiba!");
        }

        $product = Product::find($id);
        $product->update($input);
        $product->save();

        return $this->sendResponse(new ProductResource($product), "Frissítve");
    }

    

    public function delete($id)
{
    $product = Product::find($id);
    $product->delete();
    $products = Product::all();
    foreach ($products as $i => $product) {
        $product->id = $i + 1;
        $product->save();
    }
    return $this->sendResponse(new ProductResource($product), "Törölve");
}

}
