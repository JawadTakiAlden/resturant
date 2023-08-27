<?php

namespace App\Http\Controllers;


use App\Events\AddWaitingOrderEvent;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\OrderItem;
use App\Models\SubOrder;
use App\Models\Table;
use App\SecurityChecker\Checker;
use App\Traits\CustomResponse;
use Illuminate\Support\Carbon;

class OrderItemController extends Controller
{
    use CustomResponse;
    public function store(StoreOrderRequest $request)
    {
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }

        $request->validated($request->all());

        // get table that we want to add order on it
        $table = Table::where('table_number' , $request->get('table_number'))->first();

        // then check if this table has customer on it
        if ($table['in_progress']){

            // so we want to get in progress order for this table
            $order = Order::where('table_id' , $table['id'])
                ->where('in_progress' , true)
                ->first();
            // then create new sub order and make it refer to table's order
            $subOrder = SubOrder::create([
                'order_id' => $order['id'],
                'table_id' => $table['id'],
            ]);

            // now we need to update total price for this sub order by loop over order items
            $total_price_of_sub_order = 0;
            $estimatedTime = 0;

            // get order items for this sub order
            foreach ($request->order_items as $order_item){
                // then get meal that this order item has it
                $meal = Meal::where('id' , $order_item['meal_id'])->first();

                // get estimated time for this order_item
                $timeInTimestamp = new Carbon($meal['estimated_time']);
                $totalTimeStamp = $timeInTimestamp->getTimestamp() * $order_item['quantity'];
                if ($estimatedTime < $totalTimeStamp){
                    $estimatedTime = $totalTimeStamp;
                }
                // calc total price of this order ite,
                $total_price_of_item = $order_item['quantity'] * $meal->price;
                // initilize array of order item data
                $order_item_data = array_merge($order_item , ['sub_order_id' => $subOrder['id'] , 'total' => $total_price_of_item]);
                OrderItem::create($order_item_data);

                // update total price of sub order
                $total_price_of_sub_order += $total_price_of_item;
            }

            $estimatedTimeOfThisOrder = Carbon::createFromTimestamp($estimatedTime)->format('H:i:s');


            $subOrder->update([
                'total' => $total_price_of_sub_order,
                'estimated_time' => $estimatedTimeOfThisOrder
            ]);

            $myNewOrder = SubOrder::where('id' , $subOrder['id'])->first();

            $data = OrderResource::collection([$myNewOrder]);

            event(new AddWaitingOrderEvent($data));

            return $this->customResponse(null , "Your Order Ordered Successfully");
        }else {

            // if table wasn't in progress and new order on it , then we need to switch it to in progress
            $table->update([
               'in_progress' => true
            ]);

            $newOrder = Order::create([
               'table_id' => $table->id
            ]);

            $subOrder = SubOrder::create([
                'order_id' => $newOrder['id'],
                'table_id' => $table['id'],
            ]);

            $total_price_of_sub_order = 0;
            $estimatedTime = 0;
            // get order items for this sub order

            foreach ($request->order_items as $order_item){
                // then get meal that this order item has it
                $meal = Meal::where('id' , $order_item['meal_id'])->first();
                // get estimated time for this order_item
                $timeInTimestamp = Carbon::parse($meal['estimated_time']);
                $totalTimeStamp = $timeInTimestamp->copy()->getTimestamp() * $order_item['quantity'];
                $estimatedTime = max($estimatedTime , $totalTimeStamp);
                // calc total price of this order ite,
                $total_price_of_item = $order_item['quantity'] * $meal->price;
                // initilize array of order item data
                $order_item_data = array_merge($order_item , ['sub_order_id' => $subOrder['id'] , 'total' => $total_price_of_item]);
                OrderItem::create($order_item_data);

                // update total price of sub order
                $total_price_of_sub_order += $total_price_of_item;
            }
            $estimatedTimeOfThisOrder = Carbon::createFromTimestamp($estimatedTime)->format('H:i:s');


            $subOrder->update([
                'total' => $total_price_of_sub_order,
                'estimated_time' => $estimatedTimeOfThisOrder
            ]);

            $myNewOrder = SubOrder::where('id' , $subOrder['id'])->first();

            $data = OrderResource::collection([$myNewOrder]);

            event(new AddWaitingOrderEvent($data));

            return $this->customResponse(null , "Your Order Ordered Successfully");
        }
    }
}
