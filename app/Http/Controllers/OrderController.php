<?php

namespace App\Http\Controllers;

use App\Events\AddNewOrderEvent;
use App\Events\OnGoingOrderEvent;
use App\Events\PastOrdersEvent;
use App\Events\ReadyToDeliverEvent;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PasOrderResource;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\SubOrder;
use App\Models\Table;
use App\SecurityChecker\Checker;
use App\Status\OrderStatus;
use App\Status\UserType;
use App\Traits\CustomResponse;
use Illuminate\Http\Request;
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

            $queryParams = \request()->query();
            if(count($queryParams) > 1) {
                return  Checker::CheckerResponse('only state query params allowed for this api and its must from integer type');
            }

            \request()->validate([
                'state' => 'integer'
            ]);

            $orders = SubOrder::where('order_state' , request()->get('state'))->get();

            return OrderResource::collection($orders);

        }catch (\Throwable $th){
            return $this->customResponse(
                null,
                $th->getMessage(),
                500
            );
        }


    }


    public function pastOrders(){
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        $orders = Order::whereHas('subOrders' , fn($query) =>
            $query->where('order_state' , OrderStatus::Ready)
        )->get();
        return PasOrderResource::collection($orders);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }
        return OrderResource::collection([$order]);
    }

    public function toReady(SubOrder $subOrder){

        // we need to make this sub order ready to let waiter and kitchen see it as spreat ready order for specific table
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }

        $subOrder->update([
            'order_state' => OrderStatus::Ready
        ]);

        // then broadcast this ready order in readyOrder Channel
        $data = OrderResource::collection([$subOrder]);
        event(new ReadyToDeliverEvent($data));

        // but in casher we need to broadcast the parent order of this sub order with all sub orders that have state ready

        // so first : get the parent of this sub order

        $parent = Order::where('id' , $subOrder['order_id'])->first();

        $parent->update([
           'total' =>  $parent['total'] + $subOrder['total']
        ]);

        event(new PastOrdersEvent(PasOrderResource::collection([$parent])));



        return $this->customResponse($subOrder , 'your request was successfully and you order is ready now');
    }

    public function startPreparing(SubOrder $subOrder){
        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }

        $subOrder->update([
            'order_state' => OrderStatus::Preparing
        ]);


        $data = OrderResource::collection([$subOrder]);
        event(new OnGoingOrderEvent($data));

        return $this->customResponse($subOrder,"Order's State Updated Successfully");
    }

    public function acceptOrder(SubOrder $subOrder){



        if (Checker::isParamsFoundInRequest()){
            return Checker::CheckerResponse();
        }

        $subOrder->update([
            'order_state' => OrderStatus::New
        ]);



        $data = OrderResource::collection([$subOrder]);

        event(new AddNewOrderEvent($data));

        return $this->customResponse($subOrder,"Order's State Updated Successfully");
    }
}
