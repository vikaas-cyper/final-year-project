<?php

namespace App\Filament\Resources\ProductTargetResource\Pages;

use App\Filament\Resources\ProductTargetResource;
use App\Imports\ProductTargetImport;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ImportProductTarget extends Page
{
    protected static string $resource = ProductTargetResource::class;

    protected static string $view = 'filament.resources.product-target-resource.pages.import-product-target';

    public $productTargetFile;

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('productTargetFile')
                ->required()
                ->label('Upload File')
                ->reactive(),
        ];
    }

    public function download()
    {
        $folderFilePath = public_path('/import_templates/storage/Template_Import_Product_Target.xlsx');
        return response()->download($folderFilePath,"", []);
    }

    public function import(): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $file = '';

        foreach ($this->productTargetFile as $productSales) {
            $file = $productSales->store('csv', 'local');
        }

        try
        {

            Excel::import(new ProductTargetImport(), $file);
            $this->notify('success', 'Imported Successfully');

        }

        catch (\Illuminate\Validation\ValidationException $exception) {

            foreach ($exception->errors() as $error) {

                preg_match_all('!\d+!', $error[0], $row);

                $words = preg_replace('/[0-9]+./', '', $error[0]);


                $message = 'Error from row number '.$row[0][0] + 2 .' . '.$words;

                $this->notify('danger', $message);
            }
        }

        catch (\Exception $exception)

        {
            $this->notify('danger', $exception->getMessage());
        }



        return redirect(url(static::$resource::getUrl('index')));
    }

}
