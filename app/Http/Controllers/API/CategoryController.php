<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CompanyCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if($request->has('keyword')) {
            $categories = $this->search($request);
        }
        else {
            $categories = CompanyCategory::query()
                ->paginate(10);
        }
        return response()->json($categories);
    }
    public function search(Request $request){
        $keyword = $request->get('keyword');
        $category = CompanyCategory::query()
            ->where('title','like','%'.$keyword.'%')
            ->get();
        return $category;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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
        $request->validate([
            'title' => 'required|max:255'
        ]);
        $title = $request->input('title');
        $category = new CompanyCategory([
            'title' => $title
        ]);
        $category->save();
        return response()->json([ "success" => true, "entities" => $category->title], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = CompanyCategory::query()
                    ->where('company_categories.id',$id)
                    ->with('companies')
                    ->get();
        return  response()->json(["entities" =>$category],200);
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
        $title = $request->input('title');
        $category = CompanyCategory::query()->findOrFail($id);
        $category->title = $title;
        $category->save();

        return response()->json([ "success" => true, "entities" => $category->title], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = CompanyCategory::query()->findOrFail($id);
        $category->delete();
        return response()->json([ "success" => true, "entities" => $category::all()], 200);
    }
}
