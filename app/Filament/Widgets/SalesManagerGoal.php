<?php

namespace App\Filament\Widgets;

use App\Models\AreaManager;
use App\Models\DoctorMaster;
use App\Models\MarketingRepresentative;
use App\Models\MarketingRepresentativeTarget;
use App\Models\ProductSale;
use Filament\Widgets\Widget;

class SalesManagerGoal extends Widget
{
    protected static string $view = 'filament.widgets.sales-manager-goal';



    public function getTarget()
    {
        $email = auth()->user()->email;
        $MR = MarketingRepresentative::where('email', '=',auth()->user()->email )->where('status', '=', true)->first();
        $amount = 0;

        if ($MR!=null){
            $mr = [$MR->id];

        }

        else{
            $mr = AreaManager::where('email', '=', auth()->user()->email)->first()->marketing_representatives->pluck('id');
        }

        $target = MarketingRepresentativeTarget::whereIn('marketing_representative_id', $mr)->get()->filter(function ($mr){
            return date('M Y', strtotime($mr->month)) ==  date('M Y');
        })->where('status','=',true)->sum('target');

        $doctor_master_id = DoctorMaster::whereIn('marketing_representative_id', $mr)->where('status', '=', true)->get()->pluck('id');

        $product_sales_total = ProductSale::whereIn('doctor_master_id', $doctor_master_id)->get()->filter(function ($mr){
            return date('M Y', strtotime($mr->month)) ==  date('M Y');
        })->where('status','=',true)->sum('sales_total');

        return number_format($product_sales_total, 2, '.', ' , ') .' / '. number_format($target, 2, '.', ' , ');

    }

    public static function canView(): bool
    {

        $canView = MarketingRepresentative::where('email', '=',auth()->user()->email )->where('status', '=', true)->first()? 1:0;

        if ($canView==false){
            $canView = AreaManager::where('email', '=' ,auth()->user()->email)->where('status', '=', true)->first() ? 1:0;

        }

        return $canView;
    }
}
