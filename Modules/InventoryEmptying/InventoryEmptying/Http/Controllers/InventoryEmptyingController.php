<?php

namespace Modules\InventoryEmptying\Http\Controllers;
use App\Product;
use App\Variation;
use App\Transaction;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Modules\InventoryEmptying\Entities\InventoryEmptying;
use Modules\InventoryEmptying\Entities\InventoryEmptyingProducts;


class InventoryEmptyingController extends Controller
{
    protected $moduleUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
    }
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $locations = BusinessLocation::where('business_id', $business_id)->select(['business_locations.name','business_locations.id'])->get();
        $empties = InventoryEmptying::where('business_id', $business_id)->with('createdBy')->latest()->get();
        
        return view('inventoryemptying::index',compact('empties','locations'));
    }
    public function show($id)
    {
        
        $business_id = request()->session()->get('user.business_id');

        $empties = InventoryEmptying::where('id',$id)
            ->where('business_id', $business_id)
            ->with('product','product.branch','product.variation.product')
            ->firstOrFail();
        $emptyProducts = $empties->product;

        $groupbedContent = $emptyProducts->groupBy('branch.name');
        // $branches = $emptyProducts->pluck('branch.name','branch.id');
        // dd($branches);
        return view('inventoryemptying::show',compact('groupbedContent'));
    }

    public function store(Request $request)
    {

        $locations = [];
        foreach($request->location_id as $location){
            
            try{
                $locations[] = Crypt::decryptString($location);
                }  catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => 0,'msg' => __("messages.something_went_wrong")];
                return abort(400,$output);
            }
        }
        if (!auth()->user()->can('inventoryemptying.empty')) {
            abort(403, 'Unauthorized action.');
        }
        if (!(auth()->user()->can('inventoryemptying') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'inventoryemptying_module')))) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        
        
        $location = BusinessLocation::where('business_id', $business_id)->whereIn('id',$locations)->get();
        $locations = $location->pluck('id');
        $user_id = $request->session()->get('user.id');  
        
        try {
            DB::beginTransaction();
            $invEmpty = InventoryEmptying::create(['business_id'=>$business_id,'created_by'=>request()->session()->get('user.id')]);
            foreach($locations as $location_id){

                $products = Product::where('business_id', $business_id)->with('variations')->get();
                if($products->count() > 0){
                    $variations = Variation::whereIn('product_id', $products->pluck('id'))
                    ->whereHas('variation_location_details' , function ($q) use ($location_id) {
                        $q->where('location_id', $location_id);
                    })->with(['variation_location_details' => function ($q) use ($location_id) {
                        $q->where('location_id', $location_id);
                    }])
                        ->get();
                        if($variations->count() > 0){
                            foreach($variations as $var){
                                $product_qty = $var->variation_location_details[0]->qty_available;

                                if($product_qty == 0){continue;}

                                $transaction = Transaction::create([
                                    'business_id'=>$business_id,
                                    'created_by'=>$user_id,
                                    'transaction_date'=>now(),
                                    'status'=>'final',
                                    'type'=>'empty',
                                    'location_id'=>$location_id,
                                ]);

                                InventoryEmptyingProducts::create([
                                    'variation_id'=>$var->id,
                                    'branch_id'=>$location_id,
                                    'transaction_id'=>$transaction->id,
                                    'inventories_empty_id'=>$invEmpty->id,
                                    'qty_before'=>$product_qty,
                                    'type'=>($product_qty < 0 ? 'inc' : 'dec'),
                                ]);

                                $var->variation_location_details[0]->update(['qty_available'=>$this->productUtil->num_uf(0)]);

                        }
                    }
                }
            }
                DB::commit();
                return response()->json(['status'=>true,'msg'=>'Successfully Delete All']);
        }
        catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            return [
                    'success'   => 0,
                    'msg'       => __("messages.something_went_wrong")
                ];
        }



    }
}

