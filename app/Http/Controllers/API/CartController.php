<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\CartRequest;
use App\Http\Requests\Website\CartUpdateRequest; 
use App\Http\Resources\Website\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariation;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cart::query()
        ->where('guest_id', $request->guest_id)
        ->with([
            'product:id,name,cover_image,store_id,campaign_id',
            'product.store',
            'product.campaign',
            'product.variations.size',
            'product.colors',
            'productVariation',
            'productColor'
        ]);


        $cartItems = $query->get();
        $cartTotal = $cartItems->sum('total');

        return response()->json([
            'cart_items' => CartResource::collection($cartItems),
            'cart_total' => $cartTotal
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartRequest $request)
    {
        $data = $request->validated();

        $existingCartItem = Cart::where('product_id', $data['product_id'])
            ->where('product_color_id', $data['product_color_id'])
            ->where('product_variation_id', $data['product_variation_id'] ?? null)
            ->where('guest_id', $request->guest_id)
            ->first();

        if ($existingCartItem) {
            $existingCartItem->increment('quantity', $data['quantity']);
            $productVariation = ProductVariation::find($existingCartItem->product_variation_id);
            $unitPrice = $productVariation ? $productVariation->sell_price : Product::find($existingCartItem->product_id)->price;
            $existingCartItem->total = $existingCartItem->quantity * $unitPrice;
            $existingCartItem->save();
             return response()->json(['message' => 'Cart updated successfully'], Response::HTTP_OK);
        }


        if (isset($data['product_variation_id'])) {
            $productVariation = ProductVariation::findOrFail($data['product_variation_id']);
            $data['total'] = $data['quantity'] * $productVariation->sell_price;
        } else {
            $product = Product::findOrFail($data['product_id']);
            $data['total'] = $data['quantity'] * $product->price;
        }

        Cart::create($data);

        return response()->json([
            'message' => 'Item added to cart successfully'
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     * This is the new, completed method.
     */
    public function update(CartUpdateRequest $request, string $id)
    {
        $data = $request->validated();

        $cartItem = Cart::findOrFail($id);

        // Update fields if they are present in the request
        if (isset($data['quantity'])) {
            $cartItem->quantity = $data['quantity'];
        }

        if (isset($data['colorId'])) {
            $cartItem->product_color_id = $data['colorId'];
        }

        if (isset($data['variationId'])) {
            $cartItem->product_variation_id = $data['variationId'];
        }

        // --- Secure Price Recalculation ---
        // Always recalculate the total on the backend to ensure data integrity
        // and prevent users from manipulating the price.
        $unitPrice = 0;
        if ($cartItem->product_variation_id) {
            // If there's a variation, its price is the source of truth.
            $variation = ProductVariation::findOrFail($cartItem->product_variation_id);
            $unitPrice = $variation->sell_price;
        } else {
            // Otherwise, use the base product's price.
            $product = Product::findOrFail($cartItem->product_id);
            $unitPrice = $product->price;
        }

        // Calculate the new total and save everything.
        $cartItem->total = $cartItem->quantity * $unitPrice;
        $cartItem->save();

        return response()->json([
            'message' => 'Cart updated successfully'
        ], Response::HTTP_OK); // 200 OK is the correct status code for a successful update.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function orderSummary(Request $request)
    {
        $cartQuery = Cart::query()
            ->where('guest_id', $request->guest_id)
            ->with([
                'product:id,name,mockup_id,category_id',
                'product.mockup:id',
                'product.category:id',
                'product.category.shippingCharge',
                'product.mockup.shippingCharge',
                'productVariation',
                'productColor'
            ]);


        $country = null;
        if ($request->filled('country')) {
            $country = Country::where('id', $request->country)->first();
            if (!$country) {
                return response()->json([
                    'message' => 'Invalid Country Code'
                ], 422);
            }
        }

       
        if ($request->filled('state')) {
            $state = State::where('id', $request->state)->first();
            if (!$state) {
                return response()->json([
                    'message' => 'Invalid State Code'
                ], 422);
            }
        }

        if ($request->filled('city')) {
            $city = City::where('id', $request->city)->first();
            if (!$city) {
                return response()->json([
                    'message' => 'Invalid City Code'
                ], 422);
            }
        }

        $items = $cartQuery->get();


        $totalShippingCharge = 0;
        if($country){
            $shippingType = getSetting('shipping_charge_type') ?? 'mockup';
            $isUS = $country && $country->code == 6701;

            foreach ($items as $item) {
                $chargeModel = $shippingType === 'mockup'
                    ? $item->product->mockup->shippingCharge
                    : $item->product->category->shippingCharge;

                if (!$chargeModel) {
                    continue; 
                }

                $quantity = max(0, $item->quantity - 1);

                $totalShippingCharge += $isUS
                    ? $chargeModel->us_charge + ($chargeModel->us_add_charge_per_item * $quantity)
                    : $chargeModel->worldwide_charge + ($chargeModel->worldwide_add_charge_per_item * $quantity);
            }
        }
        $subTotal = $items->sum('total');
        $vatPercentage = $country->vat_percentage ?? 0;
        $vatAmount = $subTotal * $vatPercentage / 100;
        $taxPercentage = $state->tax_percentage ?? 0;
        $taxAmount = $subTotal * $taxPercentage / 100;
        $grandTotal = $subTotal + $vatAmount + $totalShippingCharge + $taxAmount;

        return response()->json([
            'cart_items'   => CartResource::collection($items),
            'item_count'   => $items->count(),
            'sub_total'    => $subTotal,
            'vat'          => [
                'percentage' => number_format($vatPercentage),
                'amount'     => $vatAmount
            ],
            'tax'          => [
                'percentage' => number_format($taxPercentage),
                'amount'     => $taxAmount
            ],
            'shipping'     => $totalShippingCharge,
            'grand_total'  => number_format($grandTotal, 2,'.',''),
        ]);
    }
}
