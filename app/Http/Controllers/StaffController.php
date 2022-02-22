<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\User;
use Validator;
use Hash;

class StaffController extends Controller
{
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where("user_type","staff")
                     ->Where("company_id",company_id())
                     ->orderBy("id","desc")->get();
        return view('backend.accounting.staff.list',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.staff.create');
		}else{
           return view('backend.accounting.staff.modal.create');
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {	
		$validator = Validator::make($request->all(), [
			'name' => 'required|max:191',
			'email' => 'required|email|unique:users|max:191',
			'password' => 'required|max:20|min:6|confirmed',
			'status' => 'required',
			'role_id' => 'required',
			'profile_picture' => 'nullable|image|max:5120',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('staffs.create')
							->withErrors($validator)
							->withInput();
			}			
		}
			
        $user= new User();
	    $user->name = $request->input('name');
		$user->email = $request->input('email');
		$user->password = Hash::make($request->password);
		$user->user_type = 'staff';
		$user->role_id = $request->input('role_id');
		$user->status = $request->input('status');
	    $user->profile_picture = 'default.png';
	    $user->currency = currency();
		if ($request->hasFile('profile_picture')){
           $image = $request->file('profile_picture');
           $file_name = "profile_".time().'.'.$image->getClientOriginalExtension();
           $image->move(base_path('public/uploads/profile/'),$file_name);
           $user->profile_picture = $file_name;
		}
        $user->company_id = company_id();
        $user->save();
		
        
		if(! $request->ajax()){
           return redirect()->route('staffs.index')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$user]);
		}
        
   }
	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $user = User::where("id",$id)
                    ->where("company_id",company_id())
                    ->first();
		if(! $request->ajax()){
		    return view('backend.accounting.staff.view',compact('user','id'));
		}else{
			return view('backend.accounting.staff.modal.view',compact('user','id'));
		} 
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $user = User::where("id",$id)
                    ->where("company_id",company_id())
                    ->first();
		if(! $request->ajax()){
		   return view('backend.accounting.staff.edit',compact('user','id'));
		}else{
           return view('backend.accounting.staff.modal.edit',compact('user','id'));
		}  
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		$validator = Validator::make($request->all(), [
			'name' => 'required|max:191',
			'email' => [
                'required',
                Rule::unique('users')->ignore($id),
            ],
			'password' => 'nullable|max:20|min:6|confirmed',
			'status' => 'required',
            'role_id' => 'required',
			'profile_picture' => 'nullable|image|max:5120',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('staffs.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        $user = User::where("id",$id)
                    ->where("company_id",company_id())->first();
		$user->name = $request->input('name');
		$user->email = $request->input('email');
		if($request->password){
            $user->password = Hash::make($request->password);
        }
		$user->user_type = 'staff';
        $user->role_id = $request->input('role_id');
		$user->status = $request->input('status');
	    if ($request->hasFile('profile_picture')){
           $image = $request->file('profile_picture');
           $file_name = "profile_".time().'.'.$image->getClientOriginalExtension();
           $image->move(base_path('public/uploads/profile/'),$file_name);
           $user->profile_picture = $file_name;
		}
        $user->company_id = company_id();
        $user->save();
		
		if(! $request->ajax()){
           return redirect()->route('staffs.index')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$user]);
		}
	    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where("id",$id)
                    ->where("company_id",company_id());
        $user->delete();
        return back()->with('success',_lang('Removed Sucessfully'));
    }
	
}
