<?php

namespace App\Imports;

use App\Models\AreaManager;
use App\Models\Billing;
use App\Models\DoctorMaster;
use App\Models\HeadQuarter;
use App\Models\Patch;
use App\Models\MarketingRepresentative;
use App\Models\Specialist;
use App\Models\State;
use App\Models\Stockist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DoctorMasterImport implements ToCollection, WithHeadingRow
{
    public function createHeadQuarter($state_id, $hq_name, $code)
    {
        return HeadQuarter::create([
            'location' => $hq_name,
            'code' => $code,
            'state_id' => $state_id,
        ]);
    }

    public function searchHeadQuarter($state_id, $hq_name, $code)
    {
        $head_quarter = HeadQuarter::where('location', '=', strtoupper($hq_name))->where('state_id', '=', $state_id)->first();

        if ($head_quarter == null) {
            $head_quarter = $this->createHeadQuarter($state_id, $hq_name, $code);
        }

        return $head_quarter;
    }

    public function createState($name): Model|DoctorMaster|null
    {
        return State::create([
            'state' => $name,
        ]);
    }

    public function searchState($name)
    {
        $state = State::where('state', '=', strtoupper($name))->first();

        if ($state == null) {
            $state = $this->createState($name);
        }

        return $state;
    }

    public function createPatch($name, $id): Model|DoctorMaster|null
    {
        return Patch::create([
            'patch' => $name,
            'state_id' => $id,
        ]);
    }

    public function searchPatch($name, $id)
    {
        $patch = Patch::where('patch', '=', strtoupper($name))->where('state_id', '=', $id)->first();

        if ($patch == null) {
            $patch = $this->createPatch($name, $id);
        }

        return $patch;
    }

    public function createBilling($billingName, $doctorName, $id, $specialist_id, $head_quarter_id): Model|DoctorMaster|null
    {
        return Billing::create([
            'patch_id' => $id,
            'billing_name' => $billingName,
            'doctor_name' => $doctorName,
            'head_quarter_id' => $head_quarter_id,
            'specialist_id' => $specialist_id,
        ]);
    }

    public function searchBilling($billingName, $doctorName, $id, $specialist_id, $head_quarter_id): Model|DoctorMaster|null
    {
        $billing = Billing::where('billing_name', '=', strtoupper($billingName))->where('doctor_name', '=', $doctorName)->where('patch_id', '=', $id)->where('specialist_id', '=', $specialist_id)->where('head_quarter_id', '=', $head_quarter_id)->first();

        if ($billing == null) {
            $billing = $this->createBilling($billingName, $doctorName, $id, $specialist_id ,$head_quarter_id);
        }

        return $billing;
    }

    public function createMarketingRepresentative($area_manager_id, $name, $email): Model|DoctorMaster|null
    {
        return MarketingRepresentative::create([
            'area_manager_id' => $area_manager_id,
            'name' => $name,
            'email' => $email,
        ]);
    }

    public function searchMarketingRepresentative($area_manager_id, $name, $email): Model|DoctorMaster|null
    {
        $marketing_representative_id = MarketingRepresentative::where('name', '=', strtoupper($name))->where('area_manager_id', '=', $area_manager_id)->first();

        if ($marketing_representative_id == null) {
            $marketing_representative_id = $this->createMarketingRepresentative($area_manager_id, $name, $email);
        }

        return $marketing_representative_id;
    }

    public function createAreaManager($state_id, $name, $email): Model|DoctorMaster|null
    {
        return AreaManager::create([
            'state_id' => $state_id,
            'name' => $name,
            'email' => $email,
        ]);
    }

    public function searchAreaManager($state_id, $name, $email)
    {
        $area_manager = AreaManager::where('name', '=', strtoupper($name))->where('state_id', '=', $state_id)->first();

        if ($area_manager == null) {
            $area_manager = $this->createAreaManager($state_id, $name, $email);
        }

        return $area_manager;
    }

    public function createStockist($marketing_representative_id, $name): Model|DoctorMaster|null
    {

        return Stockist::create([
            'marketing_representative_id' => $marketing_representative_id,
            'name' => $name,
        ]);
    }

    public function searchStockist($marketing_representative_id, $name)
    {
        $stockist = Stockist::where('name', '=', strtoupper($name))->where('marketing_representative_id', '=', $marketing_representative_id)->first();

        if ($stockist == null) {
            $stockist = $this->createStockist($marketing_representative_id, strtoupper($name));
        }

        return $stockist;
    }

    public function searchDoctorMaster($billing_id, $marketing_representative_id)
    {
        $doctor_master = DoctorMaster::where('billing_id', '=', $billing_id)->where('marketing_representative_id', '=', $marketing_representative_id)->first();

        if ($doctor_master == null) {
            $doctor_master = DoctorMaster::create([
                'billing_id' => $billing_id,
                'marketing_representative_id' => $marketing_representative_id,
            ]);
        }

        return $doctor_master;
    }

    public function createSpecialist($specialist_in): Model|DoctorMaster|null
    {
        return Specialist::create([
            'specialist_in' => $specialist_in,

        ]);
    }

    public function searchSpecialist($specialist_in)
    {
        $specialists = Specialist::where('specialist_in', '=', $specialist_in)->first();

        if ($specialists == null) {
            $specialists = $this->createSpecialist($specialist_in);
        }

        return $specialists;
    }

    public function headingRow(): int
    {
        return 1;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function collection(Collection $rows)
    {
        Validator::make($rows->toArray(),
            [
                '*.state'           => 'required',
                '*.hq_name'         => 'required',
                '*.hq_code'         => 'required',
                '*.patch'           => 'required',
                '*.billing_name'    => 'required',
                '*.doctor_name'     => 'required',
                '*.mr'              => 'required',
                '*.mr_email'        => 'required',
                '*.stockist'        => 'required',
                '*.area_manager'    => 'required',
                '*.am_email'         => 'required'
            ]
        )->validate();



        foreach ($rows as $row) {
            $state = $this->searchState(rtrim(ltrim($row['state'])));

            $head_quarter = $this->searchHeadQuarter($state->id, rtrim(ltrim($row['hq_name'])), rtrim(ltrim($row['hq_code'])));

            $patch = $this->searchPatch(rtrim(ltrim($row['patch'])), $state->id);

            $specialists = $this->searchSpecialist(rtrim(ltrim($row['specialist'])));

            $billing = $this->searchBilling(rtrim(ltrim($row['billing_name'])), rtrim(ltrim($row['doctor_name'])), $patch->id, $specialists->id, $head_quarter->id);

            $area_manager = $this->searchAreaManager($state->id, rtrim(ltrim($row['area_manager'])), rtrim(ltrim($row['am_email'])));

            $marketing_representative = $this->searchMarketingRepresentative($area_manager->id, rtrim(ltrim($row['mr'])), rtrim(ltrim($row['mr_email'])));

            $stockist = $this->searchStockist($marketing_representative->id, rtrim(ltrim($row['stockist'])));

            $this->searchDoctorMaster($billing->id, $marketing_representative->id);

        }
    }
}
