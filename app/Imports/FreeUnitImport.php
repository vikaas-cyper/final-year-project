<?php

namespace App\Imports;

use App\Models\Billing;
use App\Models\DistributionMethod;
use App\Models\DoctorMaster;
use App\Models\FreeUnit;
use App\Models\MarketingRepresentative;
use App\Models\Patch;
use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Stockist;
use Dompdf\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class FreeUnitImport implements ToCollection , WithHeadingRow
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
                "*.product"                   =>'required',
                "*.free_unit"                 =>'required',

            ]
        )->validate();

        foreach($rows as $row)
        {

            $this->row_number++;
            $patch                      = $this->searchPatch(ltrim(rtrim($row['patch'])));
            $marketing_representative   = $this->marketing_representative(ltrim(rtrim($row['marketing_representative'])));
            $billing                    = $this->searchBilling(ltrim(rtrim($row['billing_name'])), ltrim(rtrim($row['doctor_name'])), $patch->id);
            $doctor_master              = $this->searchDoctorMaster($billing->id, $marketing_representative->id);
            $product                    = $this->getProduct(ltrim(rtrim($row['product'])), $marketing_representative->head_quarter->state->id , 1);
            $free_unit                  = ltrim(rtrim($row['free_unit']));
            $month                      = Date::excelToDateTimeObject($row['month']);
            FreeUnit::create
            ([
                'doctor_master_id'      => $doctor_master->id,
                'product_id'            => $product->id,
                'free_unit'             => $free_unit,
                'month'                 => $month,
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
    private function getProduct($product_name, $state_id)
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
            throw new Exception('Billing name with given Doctor name and Patch given in row '. $this->row_number . ' not matched to database');
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
            throw new Exception(  'Billing with Marketing Representative from row '.$this->row_number.' not matched to database');
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
