<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderDetailResource;
use App\Http\Resources\Admin\OrderResource;
use App\Models\CombinedOrder;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = CombinedOrder::query()
        ->latest()
        ->with('customer:id,name,email,phone')
        ->search(['order_code'], request()->search);
        return inertia('Order/Index', [
            'orders' => OrderResource::collection($orders),
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = CombinedOrder::query()->with(['customer','customer.country','customer.state','customer.city','orders','orders.orderDetails','orders.orderDetails.product:id,name'])->findOrFail($id);
        return inertia('Order/Show', [
            'order' => OrderDetailResource::make($order),
            'statuses' => OrderStatus::query()->select(['id','name'])->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'order_status_id' => 'required|exists:order_statuses,id',
            'message' => 'nullable|string|max:500'
        ]);

        $order = CombinedOrder::query()->findOrFail($id);
        $order->update([
            'order_status_id' => $request->order_status_id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
