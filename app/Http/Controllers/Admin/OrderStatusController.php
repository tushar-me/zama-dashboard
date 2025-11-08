<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\OrderStatusRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderStatusResource;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class OrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Inertia\Response
    {
        $orderStatuses = OrderStatus::query()
            ->with(['creator:id,name', 'editor:id,name'])
            ->search(['name'], request()->search);


        return inertia('OrderStatus/Index', [
            'orderStatuses' => OrderStatusResource::collection($orderStatuses),
            'search' => request()->search,
            'limit' => request()->integer('limit', 10),
            'column' => request('column', 'order_level'),
            'order' => request('order', 'asc'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        return inertia('OrderStatus/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStatusRequest $request): RedirectResponse
    {
        OrderStatus::create($request->validated());

        return to_route('order-status.index')->with('success', 'Order Status created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderStatus $orderStatus): \Inertia\Response
    {
        return inertia('OrderStatus/Show', [
            'orderStatus' => OrderStatusResource::make($orderStatus->load(['creator:id,name', 'editor:id,name'])),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderStatus $orderStatus): \Inertia\Response
    {
        return inertia('OrderStatus/Edit', [
            'orderStatus' => $orderStatus,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderStatusRequest $request, OrderStatus $orderStatus): RedirectResponse
    {
        $orderStatus->update($request->validated());

        return to_route('order-status.index')->with('success', 'Order Status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderStatus $orderStatus): RedirectResponse
    {
        $orderStatus->delete();

        return back()->with('success', 'Order Status deleted successfully.');
    }
}
