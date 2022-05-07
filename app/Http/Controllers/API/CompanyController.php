<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $companies = Company::query();
        if ($request->has('category_id')) {
            $companies = $companies->with('category');
        }
        $companies = $companies->paginate(10);

        return response()->json($companies);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //Validation Message is not used in this project due to time constraints
        $request->validate([
            'title' => 'required|max:255',
            'status' => 'required',
            'category_id' => 'exists:company_categories,id'
        ]);

        $newCompany = new Company;
        if($request->hasFile('image')){
            $request->validate([
                'image' =>'image|mimes:jpeg,bmp,png,jpg'
            ]);
            $imageName = 'ek_'. time() . '.' .$request->image->exestion();
            $request->image->move(public_path('images'), $imageName);
            $newCompany->image = $imageName;
        }

            $newCompany->category_id = $request->get('category_id') ?? '';
            $newCompany->title = $request->get('title');
            $newCompany->description = $request->get('description');
            $newCompany->status = $request->get('status');
        $newCompany->save();
        return response()->json([ "success" => true, "entities" => $newCompany::all()], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request,$id)
    {
        $companies = Company::query()->where('id',$id);
        if ($request->has('category_id')){
           $companies =  $companies->with('category');
        }
        $companies = $companies->paginate(10);
        return response()->json($companies);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //Validation Message is not used in this project due to time constraints
        $request->validate([
            'category_id' => 'exists:company_categories,id'
        ]);
        $newCompany = Company::query()->findOrFail($id);
        if($request->hasFile('image')){
            $request->validate([
                'image' =>'image|mimes:jpeg,bmp,png,jpg'
            ]);
            $imageName = 'ek_'. time() . '.' .$request->image->exestion();
            if ($imageName != $newCompany->image) {
                $request->image->move(public_path('images'), $imageName);
                $newCompany->image = $imageName;
            }
        }

        $newCompany->category_id = $request->get('category_id') ?? $newCompany->category_id;
        $newCompany->title = $request->get('title') ?? $newCompany->title;
        $newCompany->description = $request->get('description') ?? $newCompany->description;
        $newCompany->status = $request->get('status') ?? $newCompany->status;

        $newCompany->save();
        return response()->json([ "success" => true, "entities" => $newCompany::all()], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $company = Company::query()->findOrFail($id);
        $imageName = $company->image;
        $company->delete();
        $image_path = public_path("images\\"). $imageName;
        if(Storage::exists($image_path)){
            Storage::delete($image_path);
        }
        return response()->json([ "success" => true, "entities" => $company::all()], 200);
    }
}
