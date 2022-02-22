<?php

namespace App\Http\Controllers;

use App\CompanySetting;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompanySettingsController extends Controller {
    public function __construct() {
        header('Cache-Control: no-cache');
        header('Pragma: no-cache');
    }

    public function settings($store = "", Request $request) {
        if ($store == "") {
            return view('backend.accounting.general_settings.settings');
        } else {

            $company_id = company_id();
            foreach ($_POST as $key => $value) {
                if ($key == "_token") {
                    continue;
                }

                $data               = array();
                $data['value']      = $value;
                $data['company_id'] = $company_id;
                $data['updated_at'] = Carbon::now();

                if (CompanySetting::where('name', $key)->where("company_id", $company_id)->exists()) {
                    CompanySetting::where('name', '=', $key)
                        ->where("company_id", $company_id)
                        ->update($data);
                } else {
                    $data['name']       = $key;
                    $data['created_at'] = Carbon::now();
                    CompanySetting::insert($data);
                }
            } //End Loop

            if (!$request->ajax()) {
                return redirect()->route('company.change_settings')->with('success', _lang('Saved Sucessfully'));
            } else {
                return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Saved Sucessfully')]);
            }

        }
    }

    public function upload_logo(Request $request) {
        $this->validate($request, [
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:8192',
        ]);

        $company_id = company_id();

        if ($request->hasFile('logo')) {
            $image           = $request->file('logo');
            $name            = 'company_logo' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/company');
            $image->move($destinationPath, $name);

            $data               = array();
            $data['value']      = $name;
            $data['company_id'] = $company_id;
            $data['updated_at'] = Carbon::now();

            if (CompanySetting::where('name', "company_logo")->where("company_id", $company_id)->exists()) {
                CompanySetting::where('name', '=', "company_logo")
                    ->where("company_id", $company_id)
                    ->update($data);
            } else {
                $data['name']       = "company_logo";
                $data['created_at'] = Carbon::now();
                CompanySetting::insert($data);
            }

            if (!$request->ajax()) {
                return redirect()->route('company.change_settings')->with('success', _lang('Saved Sucessfully'));
            } else {
                return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Logo Upload successfully')]);
            }

        }
    }

    public function upload_file($file_name, Request $request) {

        if ($request->hasFile($file_name)) {
            $file            = $request->file($file_name);
            $name            = 'file_' . time() . "." . $file->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/media');
            $file->move($destinationPath, $name);

            $data               = array();
            $data['value']      = $name;
            $data['company_id'] = company_id();
            $data['updated_at'] = Carbon::now();

            if (Setting::where('name', $file_name)->exists()) {
                Setting::where('name', '=', $file_name)->update($data);
            } else {
                $data['name']       = $file_name;
                $data['created_at'] = Carbon::now();
                Setting::insert($data);
            }
        }
    }

}