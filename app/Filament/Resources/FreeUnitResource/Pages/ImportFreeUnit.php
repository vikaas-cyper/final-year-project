<?php

namespace App\Filament\Resources\FreeUnitResource\Pages;

use App\Filament\Resources\FreeUnitResource;
use App\Imports\FreeUnitImport;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;


class ImportFreeUnit extends Page
{
    protected static string $resource = FreeUnitResource::class;

    protected static string $view = 'filament.resources.free-unit-resource.pages.import-free-unit';
    /**
     * @var ComponentContainer|View|mixed|null
     */
    public $freeUnitFile;

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('freeUnitFile')
                ->required()
                ->label('Upload File')
                ->reactive(),
        ];
    }

    public function download()
    {
        $folderFilePath = public_path('/import_templates/storage/Template_Import_Free_Unit.xlsx');
        return response()->download($folderFilePath,"", []);
    }

    public function import(): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $file = '';

        foreach ($this->freeUnitFile as $freeUnit) {
            $file = $freeUnit->store('csv', 'local');
        }

        try
        {

            Excel::import(new FreeUnitImport(), $file);
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
