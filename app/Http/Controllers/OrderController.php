<?php

namespace App\Http\Controllers;

use App\Events\AddNewOrderEvent;
use App\Events\OnGoingOrderEvent;
use App\Events\ReadyToDeliverEvent;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\Table;
use App\Status\OrderStatus;
use App\Status\UserType;
use App\Traits\CustomResponse;
use Illuminate\Support\Facades\Auth;

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

        if ($order['is_first']){
            $order->update([
                'order_state' => OrderStatus::Ready
            ]);
            $data = OrderResource::collection([$order]);
            event(new ReadyToDeliverEvent($data));
            return $this->customResponse($data,"Order's State Updated Successfully");
        }


        $firstOrder = Order::where('table_id' , $order['table_id'])
            ->where('is_first' , true)
            ->where('order_state' , OrderStatus::Ready)
            ->first();

        if ($firstOrder && !$order['is_first']){
            $newTotal = $firstOrder['total'];
            foreach ($order->orderItems as $orderItem){
                $newTotal += $orderItem['total'];
                $orderItem->update([
                    'order_id' =>  $firstOrder['id']
                ]);
            }

            $order->delete();

            $data = OrderResource::collection([$firstOrder]);
            event(new ReadyToDeliverEvent($data));
            return $this->customResponse($data,"Order's State Updated Successfully");
        }

        if (!$firstOrder && !$order['is_first']){
            $firstOrder = Order::where('table_id' , $order['table_id'])
                ->where('is_first' , true)
                ->first();
            $firstOrder->update([
                'is_first' => false
            ]);

            $order->update([
                'is_first' => true,
                'order_state' => OrderStatus::Ready
            ]);
            $data = OrderResource::collection([$order]);
            event(new ReadyToDeliverEvent($data));
            return $this->customResponse($data,"Order's State Updated Successfully");
        }
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
