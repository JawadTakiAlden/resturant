<?php

namespace App\Http\Controllers;


use App\Events\AddWaitingOrderEvent;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\OrderItem;
use App\Models\Table;
use App\Traits\CustomResponse;

class OrderItemController extends Controller
{
    use CustomResponse;
    public function store(StoreOrderRequest $request)
    {
        $request->validated($request->all());

        $table = Table::where('table_number' , $request->get('table_number'))->first();

        if ($table['in_progress']){
            $order = Order::create([
                "table_id" => $table['id'],
            ]);
            $total_price_of_order = 0;

            foreach ($request->order_items as $order_item){
                $meal = Meal::where('id' , $order_item['meal_id'])->first();
                $total_price_of_item = $order_item['quantity'] * $meal->price;
                $order_item_data = array_merge($order_item , ['order_id' => $order['id'] , 'total' => $total_price_of_item]);
                OrderItem::create($order_item_data);
                $total_price_of_order += $total_price_of_item;
            }
            $order->update([
                'total' => $total_price_of_order
            ]);

            $myNewOrder = Order::where('id' , $order->id)->first();

            $data = OrderResource::collection([$myNewOrder]);

            event(new AddWaitingOrderEvent($data));

            return $this->customResponse(null , "Your Order Ordered Successfully");
        }else {
            $order = Order::create([
                "table_id" => $table['id'],
                "is_first" => true
            ]);

            $table->update([
               'in_progress' => true
            ]);

            $total_price_of_order = 0;

            foreach ($request->order_items as $order_item){
                $meal = Meal::where('id' , $order_item['meal_id'])->first();
                $total_price_of_item = $order_item['quantity'] * $meal->price;
                $order_item_data = array_merge($order_item , ['order_id' => $order['id'] , 'total' => $total_price_of_item]);
                OrderItem::create($order_item_data);
                $total_price_of_order += $total_price_of_item;
            }
            $order->update([
                'total' => $total_price_of_order
            ]);

            $myNewOrder = Order::where('id' , $order->id)->first();

            $data = OrderResource::collection([$myNewOrder]);

            event(new AddWaitingOrderEvent($data));

            return $this->customResponse(null , "Your Order Ordered Successfully");
        }
    }
}
