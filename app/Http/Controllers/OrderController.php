<?php

namespace App\Http\Controllers;

use App\Events\AddNewOrderEvent;
use App\Events\OnGoingOrderEvent;
use App\Events\ReadyToDeliverEvent;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Status\OrderStatus;
use App\Traits\CustomResponse;

class OrderController extends Controller
{
    use CustomResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {


            \request()->validate([
                'state' => 'integer'
            ]);

            $orders = Order::where('order_state' , request('state'))->get();

            return OrderResource::collection($orders);

        }catch (\Throwable $th){
            return $this->customResponse(
                null,
                $th->getMessage(),
                500
            );
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return OrderResource::collection([$order]);
    }

    public function toReady(Order $order){
        $order->update([
           'order_state' => OrderStatus::Ready
        ]);

        $data = OrderResource::collection([$order]);

        event(new ReadyToDeliverEvent($data));

        return $this->customResponse($order,"Order's State Updated Successfully");
    }

    public function startPreparing(Order $order){
        $order->update([
            'order_state' => OrderStatus::Preparing
        ]);

        $data = OrderResource::collection([$order]);
        event(new OnGoingOrderEvent($data));

        return $this->customResponse($order,"Order's State Updated Successfully");
    }

    public function acceptOrder(Order $order){
        $order->update([
            'order_state' => OrderStatus::New
        ]);

        $data = OrderResource::collection([$order]);

        event(new AddNewOrderEvent($data));

        return $this->customResponse($order,"Order's State Updated Successfully");
    }
}
