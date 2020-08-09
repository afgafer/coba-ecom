<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories=Category::with('parent')->orderBy('created_at','DESC')->paginate(10);
        $parents=Category::getParent()->orderBy('name','ASC')->get();

        return view('category.index', compact('categories','parents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required|string|max:50|unique:categories'
        ]);
        $request->request->add(['slug'=>$request->name]);
        Category::create($request->except('_token'));
        return redirect()->route('category.index')->with(['success'=>'The category has been saved']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\category  $category
     * @return \Illuminate\Http\Response
     */
    // public function show(category $category)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category=Category::find($id);
        $parents=Category::getParent()->orderBy('name','ASC')->get();
        return view('category.edit',compact('category','parents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name'=>'required|string|max:50|unique:categories,name,'.$id
        ]);

        $category=Category::find($id);
        $category->update([
            'name'=>$request->name,
            'parent_id'=>$request->parent_id,
        ]);
        return redirect()->route('category.index')->with(['success'=>'The category has been saved']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category=Category::withCount(['child','product'])->find($id);
        if($category->child_count==0&&$category->prduct_count==0){
            $category->delete();
            return redirect()->route('category.index')->with(['success'=>'The category has been delete']);
        }
        return redirect()->route('category.index')->with(['error'=>"The category have category's child"]);
    }
}
