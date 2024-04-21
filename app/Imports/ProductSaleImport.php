<?php

namespace App\Imports;

use App\Models\Billing;
use App\Models\DistributionMethod;
use App\Models\DoctorMaster;
use App\Models\MarketingRepresentative;
use App\Models\Patch;
use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Stockist;
use Carbon\Carbon;
use Exception;
use DB;
use Heloufir\FilamentWorkflowManager\Models\WorkflowHistory;
use Heloufir\FilamentWorkflowManager\Models\WorkflowModelStatus;
use Heloufir\FilamentWorkflowManager\Models\WorkflowStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use function PHPUnit\Framework\throwException;


class ProductSaleImport implements ToCollection, WithHeadingRow
{
    private int $row_number = 1;

    /**
     * @throws ValidationException
     * @throws Exception
     */

    public function collection(Collection $rows)
    {

        Validator::make($rows->toArray(),
            [
                "*.billing_name"              =>'required',
                "*.doctor_name"               =>'required',
                "*.patch"                     =>'required',
                "*.month"                     =>'required',
                "*.marketing_representative"  =>'required',
                "*.stockist"                  =>'required',
                "*.product"                   =>'required',
                "*.free_units"                 =>'required',
                "*.sale_units"                =>'required',
                "*.total_sales"                =>'required',
                "*.total_free"               =>  'required',

            ]
        )->validate();

        $invalid_datas = array();

        foreach($rows as $row)
        {
            try
            {

                $this->row_number++;
                $patch                      = $this->searchPatch(ltrim(rtrim($row['patch'])));
                $marketing_representative   = $this->marketing_representative(ltrim(rtrim($row['marketing_representative'])));
                $stockist                   = $this->search_stockist(ltrim(rtrim($row['stockist'])));

                $billing                    = $this->searchBilling(ltrim(rtrim($row['billing_name'])), ltrim(rtrim($row['doctor_name'])), $patch->id);
                $doctor_master              = $this->searchDoctorMaster($billing->id, $marketing_representative->id);
                $product                    = $this->getProduct(ltrim(rtrim($row['product'])));
                $sales_unit                 = ltrim(rtrim($row['sale_units']));
                $free_unit                  = ltrim(rtrim($row['free_units']));
                $sales_total                = ltrim(rtrim($row['total_sales']));
                $free_total                 = ltrim(rtrim($row['total_free']));
                $month                      = $row['month'];
                if (gettype($month) != 'string'){
                    $month                       = Date::excelToDateTimeObject($row['month']);
                }
            }
            catch (Exception $exception)
            {
                $invalid_datas[] = $exception->getMessage();
            }


        }
        foreach ($invalid_datas as $error) {
            throw new Exception(implode(",\n",$invalid_datas));
        }

        foreach($rows as $row)
        {

            $this->row_number++;
            $patch                      = $this->searchPatch(ltrim(rtrim($row['patch'])));
            $marketing_representative   = $this->marketing_representative(ltrim(rtrim($row['marketing_representative'])));
            $billing                    = $this->searchBilling(ltrim(rtrim($row['billing_name'])), ltrim(rtrim($row['doctor_name'])), $patch->id);
            $stockist                   = $this->search_stockist(ltrim(rtrim($row['stockist'])));
            $doctor_master              = $this->searchDoctorMaster($billing->id, $marketing_representative->id);
            $product                    = $this->getProduct(ltrim(rtrim($row['product'])));
            $sales_unit                 = ltrim(rtrim($row['sale_units']));
            $free_unit                  = ltrim(rtrim($row['free_units']));
            $sales_total                = ltrim(rtrim($row['total_sales']));
            $free_total                 = ltrim(rtrim($row['total_free']));
            $purchase_price             = DB::table('product_buy_rates')
                                            ->select('price_per_unit')
                                            ->where('status', true)
                                            ->where('product_id', $product->id)
                                            ->pluck('price_per_unit')
                                            ->first();
            $month                      = $row['month'];
            if (gettype($month) != 'string'){
                $month                       = Date::excelToDateTimeObject($row['month']);
            }
            $model = ProductSale::create
            ([
                'doctor_master_id'      => $doctor_master->id,
                'product_id'            => $product->id,
                'stockist_id'           => $stockist->id,
                'sales_unit'            => $sales_unit,
                'sales_total'           => $sales_total,
                'free_unit'             => $free_unit,
                'free_total'            => $free_total,
                'month'                 => $month,
                'purchase_price'        => $sales_unit * $purchase_price, 
                'status'                => true,

            ]);

        }

    }

   public function dateColumns(): array
    {
        return ['month'];
    }

    /**
     * @throws Exception
     */

    private function getProduct($product_name)
    {
        $product = Product::where('name', '=', $product_name)->where('status', '=', true)->first();

        if (! $product)
        {
            throw new Exception('Product given in row '.$this->row_number.' not matched to database');
        }
            return $product;

    }
    /**
     * @param $method
     * @return Model
     * @throws Exception
     */

    /**
     * @param $patch_name
     * @throws Exception
     * @return Model
     */

    private function search_stockist($name): Model
    {
        $stockist = Stockist::where('name', '=' ,$name)->where('status', '=' , true)->first();
        if (! $stockist)
        {
            throw new Exception('Stockist given in row '.$this->row_number.' not matched to database');
        }

        return $stockist;
    }

    private function searchPatch($patch_name): Model
    {
        $patch = Patch::where('patch', '=', $patch_name)->where('status', '=', true)->first();
        if($patch == null)
        {
            throw new Exception('Patch found in row '.$this->row_number.' not matched to database' );

        }

        return $patch;

    }

    /**
     * @param $billing_name
     * @param $doctor_name
     * @param $patch_id
     * @return Model
     * @throws Exception
     */

    private function searchBilling($billing_name, $doctor_name, $patch_id): Model
    {
        $billing = Billing::where('patch_id' , '=', $patch_id)->where('billing_name', '=', $billing_name)->where('doctor_name', '=', $doctor_name)->where('status', '=', true)->first();

        if ($billing == null)
        {

            DB::statement('insert into billings (patch_id, billing_name, doctor_name,  specialist_id, status, created_by, updated_by, head_quarter_id) values (?, ?, ?, ?, ?, ?, ?, ?)',
             [$patch_id, $billing_name, $doctor_name, 1, true, auth()->user()->email, auth()->user()->email,DB::table('billings')
             ->select('head_quarter_id')
             ->where('patch_id', $patch_id)
             ->pluck('head_quarter_id')
             ->first()]);
             $billing = Billing::where('patch_id' , '=', $patch_id)->where('billing_name', '=', $billing_name)->where('doctor_name', '=', $doctor_name)->where('status', '=', true)->first();

            // throw new Exception('Billing name with given Doctor name and Patch given in row '. $this->row_number . ' not matched to database');
        }

        return $billing;

    }

    /**
     * @param $billing_id
     * @param $marketing_representative_id
     * @return Model
     * @throws Exception
     */

    private function searchDoctorMaster($billing_id, $marketing_representative_id): Model
    {
        $doctor_master = DoctorMaster::where('billing_id' , '=', $billing_id)->where('marketing_representative_id', '=', $marketing_representative_id)->where('status', '=', true)->first();



        if ($doctor_master == null){

            DB::insert('insert into doctor_masters (billing_id, marketing_representative_id, status, created_by, updated_by) values (?, ?, ?, ?, ?)',
            [$billing_id, $marketing_representative_id, true, auth()->user()->email, auth()->user()->email]);
            $doctor_master = DoctorMaster::where('billing_id' , '=', $billing_id)->where('marketing_representative_id', '=', $marketing_representative_id)->where('status', '=', true)->first();


            // throw new Exception(  'Billing with Marketing Representative from row '.$billing_id.' not matched to database');
        }

        return $doctor_master;
    }

    /**
     * @param $name
     * @return Model
     * @throws Exception
     */

    private function marketing_representative($name): Model
    {
        $marketing_representative = MarketingRepresentative::where('name', '=', $name)->where('status', '=' , true)->first();

        if($marketing_representative == null)
        {
            throw new Exception('Marketing Representative  given in row '.$this->row_number.' not matched to database' );
        }

        return $marketing_representative;

    }

    public function headingRow(): int
    {
        return 1;
    }

}
