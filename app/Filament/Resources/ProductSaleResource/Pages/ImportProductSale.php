<?php

namespace App\Filament\Resources\ProductSaleResource\Pages;

use App\Filament\Resources\ProductSaleResource;
use App\Imports\ProductMasterImport;
use App\Imports\ProductSaleImport;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use phpDocumentor\Reflection\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class ImportProductSale extends Page
{
    protected static string $resource = ProductSaleResource::class;

    protected static string $view = 'filament.resources.product-sale-resource.pages.import-product-sale';
    public $productSalesFile;

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('productSalesFile')
                ->required()
                ->label('Upload File')
                ->reactive(),
        ];
    }
    public function download()
    {
        $folderFilePath = public_path('/import_templates/storage/Template_Import_Product_Sales.xlsx');
        return response()->download($folderFilePath,"", []);
    }

    public function import(): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $file = '';

        foreach ($this->productSalesFile as $productSales) {
            $file = $productSales->store('csv', 'local');
        }

        try
        {

            Excel::import(new ProductSaleImport(), $file);
            $this->notify('success', 'Imported Successfully');

        }

        catch (\Illuminate\Validation\ValidationException $exception) {
            $invalid_datas = array();
            foreach ($exception->errors() as $error) {

                preg_match_all('!\d+!', $error[0], $row);

                $words = preg_replace('/[0-9]+./', '', $error[0]);

                $message = 'Error from row number '.$row[0][0] + 2 .' . '.$words;

                $invalid_datas[] = explode(',', $message);

                $this->notify('danger', $message);

            }
            $csvData = implode("\n",array_map(function($a) {return implode("~",$a);},$invalid_datas));
            Storage::put('data.txt', $csvData);
            $folderFilePath = public_path('../storage/app/data.txt');
            date_default_timezone_set('Asia/Kolkata');
            return response()->download($folderFilePath,'Error Logs - Product Sales '.date( 'd-m-Y h-i A', time ()), []);
        }

        catch (\Exception $exception)

        {
                $this->notify('danger', $exception->getMessage());
                $csvData = $exception->getMessage();
                Storage::put('data.txt', $csvData);
                $folderFilePath = public_path('../storage/app/data.txt');
                date_default_timezone_set('Asia/Kolkata');
                return response()->download($folderFilePath,'Error Logs - Product Sales '.date( 'd-m-Y h-i A', time ()), []);
        }



        return redirect(url(static::$resource::getUrl('index')));
    }

}
