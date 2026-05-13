<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();
        
        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }
    public function update(Request $request, $id)
{
    // Pronađi stavku u korpi koja pripada ulogovanom korisniku
    $cartItem = Cart::where('user_id', auth()->id())->findOrFail($id);
    
    // Validacija
    $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);

    // Ažuriraj količinu
    $cartItem->update([
        'quantity' => $request->quantity
    ]);

    return back()->with('success', 'Količina je uspešno promenjena!');
}
    public function addToCart(Request $request, $id)
{
    $product = Product::findOrFail($id);

    if ($product->stock <= 0) {
        return redirect()->back()->with('error', 'Nažalost, proizvod više nije na stanju.');
    }
    if (!auth()->check()) {
        return redirect()->route('login')->with('info', 'Morate biti ulogovani.');
    }

    $cartItem = Cart::where('user_id', auth()->id())
                    ->where('product_id', $id)
                    ->first();

    if ($cartItem) {
        
        $cartItem->increment('quantity');
    } else {
        
        Cart::create([
            'user_id' => auth()->id(),
            'product_id' => $id,
            'quantity' => 1
        ]);
    }

    return back()->with('success', 'Proizvod dodat u korpu!');
}
public function destroy($id)
    {
        Cart::where('user_id', auth()->id())->where('id', $id)->delete();
        return back()->with('success', 'Proizvod uklonjen iz korpe.');
    }
    
}
