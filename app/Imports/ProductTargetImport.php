<?php

namespace App\Imports;

use Exception;
use App\Models\Product;
use App\Models\HeadQuarter;
use App\Models\ProductTarget;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductTargetImport implements ToCollection, WithHeadingRow
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
                '*.hq'          => 'required' ,
                '*.brand_name'  => 'required' ,
                '*.scope'       => 'required' ,
                '*.target'      => 'required' ,
                '*.month'       => 'required'
            ]
        )->validate();

        foreach ($rows as $row)
        {
            $this->row_number++;
            $head_quarter = $this->searchHeadQuarter(ltrim(rtrim($row['hq'])));
            $product      = $this->searchProduct(ltrim(rtrim($row['brand_name'])));

            $scope        = strtolower(ltrim(rtrim($row['scope'])));
            $month        = Date::excelToDateTimeObject($row['month']);

            ProductTarget::create([
                'product_id'            => $product->id,
                'head_quarter_id'       => $head_quarter->id,
                'scope'                 => $scope,
                'target'                => ltrim(rtrim($row['target'])),
                'month'                 => $month
            ]);

        }
    }

    /**
     * @throws Exception
     */
    private function searchHeadQuarter($hq_name)
    {
        $head_quarter = HeadQuarter::where('location', '=', strtoupper($hq_name))->where('status' , '=' , true)->first();

        if (! $head_quarter)
        {
            throw new Exception('Hq given in row '.$this->row_number.' not matched to database');
        }

        return $head_quarter;
    }

    /**
     * @throws Exception
     */
    private function searchProduct($name)
    {
        $product = Product::where('name', '=' , $name)->where('status', '=', true)->first();

        if (! $product)
        {
            throw new Exception('Product given in row '.$this->row_number.' not matched to database');
        }

        return $product;
    }

    public function headingRow(): int
    {
        return 1;
    }

}
