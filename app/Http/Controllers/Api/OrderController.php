<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\Enum\OrderStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::guard('seller')->user();
        if($user->hasRole('admin')) {
            $orders = Order::paginate(10);
        } else {
            $orders = Order::whereHas('orderItems', function ($query) use ($user) {
                $query->where('seller_id', $user->id);
            })->paginate(10);
        }
        if(!$orders->isEmpty())
        {
            $data = [
                'orders' => OrderResource::collection($orders),
                'pagination' => [
                        'total' => $orders->total(),
                        'current_page' => $orders->currentPage(),
                        'per_page' => $orders->perPage(),
                        'links' => [
                            'first_page' => $orders->url(1),
                            'last_page' => $orders->url($orders->lastPage()),
                        ]
                    ]
            ];
            return response()->json($data);
        }
        return response()->json(['message' => 'No orders found'], 404);
    }

    /**
     * Display a listing of the resource for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function myOrders()
    {
        $orders = Auth::user()->orders()->paginate(10);
        if(!$orders->isEmpty())
        {
            $data = [
                'orders' => OrderResource::collection($orders),
                'pagination' => [
                        'total' => $orders->total(),
                        'current_page' => $orders->currentPage(),
                        'per_page' => $orders->perPage(),
                        'links' => [
                            'first_page' => $orders->url(1),
                            'last_page' => $orders->url($orders->lastPage()),
                        ]
                    ]
            ];
            return response()->json($data);
        }
        return response()->json(['message' => 'No orders found'], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $totalPrice = 0;

        $order = Order::create([
            'user_id' => $data['user_id'],
            'shipping_address' => $data['shipping_address'],
            'total_price' => 0,
            'status' => OrderStatusEnum::PENDING->value,
        ]);

        foreach ($data['order_items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            if ($product->quantity < $item['quantity']) {
                return response()->json(['message' => 'Insufficient stock for product ID: ' . $product->id], 400);
            }

            $subtotal = $product->discounted_price * $item['quantity'];
            $totalPrice += $subtotal;

            $order->orderItems()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'seller_id' => $product->seller_id,
                'price' => $product->discounted_price,
                'color' => $item['color'] ?? null,
                'size' => $item['size'] ?? null,
            ]);

            $product->decrement('quantity', $item['quantity']);
        }

        $order->update(['total_price' => $totalPrice]);

        return response()->json(new OrderResource($order), 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $order = Order::where('slug', $slug)->first();
        if ($order) {
            if(Auth::guard('seller')->check())
            {
                return response()->json(new OrderResource($order));
            }
            else
            {
                if (Auth::user()->id === $order->user_id) {
                    return response()->json(new OrderResource($order));
                }
                else
                {
                    return response()->json(['message' => 'You are not authorized to view this order'], 403);
                }
            }
        }
        return response()->json(['message' => 'Order not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderStatusRequest $request, string $slug)
    {
        $order = Order::where('slug', $slug)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $user = Auth::guard('seller')->user();

        if ($user->hasRole('admin')) {
            $order->update($request->validated());
            return response()->json(new OrderResource($order));
        }

        $ownsProduct = $order->orderItems()->where('seller_id', $user->id)->exists();

        if ($ownsProduct) {
            $order->update($request->validated());
            return response()->json(new OrderResource($order));
        }

        return response()->json(['message' => 'You are not authorized to update this order'], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $order = Order::where('slug', $slug)->first();
        if ($order) {

            if (!Auth::guard('seller')->check() && $order->status->value !== OrderStatusEnum::PENDING->value) {
                return response()->json(['message' => 'You can only delete pending orders'], 403);
            }

            $order->delete();
            return response()->json(['message' => 'Order deleted'], 200);
        }
        return response()->json(['message' => 'Order not found'], 404);
    }

}
