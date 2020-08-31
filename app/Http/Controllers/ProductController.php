<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function order(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|gt:0',
        ]);

        $product = $this->getProduct($request);

        if (! $this->checkAvailability($product, $request->get('quantity')))
            return $this->response('Failed to order this product due to unavailability of the stock.', 400);

        $this->updateStock($product, $request->get('quantity'));

        return $this->response('You have successfully ordered this product.', 201);
    }

    protected function getProduct(Request $request) : ?Product
    {
        return Product::find( $request->get('product_id') );
    }

    protected function checkAvailability(Product $product, int $quantity) : bool
    {
        return $product->stock >= $quantity;
    }

    protected function updateStock(Product $product, int $quantity) : void
    {
        $remaining = $product->stock - $quantity;

        $product->update([
            'stock' => $remaining,
        ]);
    }
}
