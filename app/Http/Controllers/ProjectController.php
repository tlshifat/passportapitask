<?php

namespace App\Http\Controllers;

use App\Product;
use App\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects;

        return response()->json([
                                    'success' => true,
                                    'data' => $projects
                                ]);
    }

    public function show($id)
    {
        $product = auth()->user()->products()->find($id);

        if (!$product) {
            return response()->json([
                                        'success' => false,
                                        'message' => 'Product with id ' . $id . ' not found'
                                    ], 400);
        }

        return response()->json([
                                    'success' => true,
                                    'data' => $product->toArray()
                                ], 400);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'user_id' => 'integer',
        ]);

        $project = new Project();
        $project->name = $request->name;
        $project->description = $request->description;
        if( !empty(auth()->user()->is_admin) && !empty($request->user_id)){
            $project->user_id = intval($request->user_id);
            if($project->save()){
                return response()->json([
                                            'success' => true,
                                            'data' => $project->toArray()
                                        ]);
            }else {
                return response()->json([
                                            'success' => false,
                                            'message' => 'Project could not be added'
                                        ], 500);
            }
        }

        if (auth()->user()->projects()->save($project))
            return response()->json([
                                        'success' => true,
                                        'data' => $project->toArray()
                                    ]);
        else
            return response()->json([
                                        'success' => false,
                                        'message' => 'Project could not be added'
                                    ], 500);
    }

    public function update(Request $request, $id)
    {

        $project =Project::whereId($id)->first();
        if (!$project) {
            return response()->json([
                                        'success' => false,
                                        'message' => 'Project with id ' . $id . ' not found'
                                    ], 400);
        }

        if($project->user_id !=  auth()->user()->id && empty(auth()->user()->is_admin)){
            return response()->json([
                                        'success' => false,
                                        'message' => 'Projects can be edited by users (only those that the user owns) or admins'
                                    ], 400);
        }


        $updated = $project->fill($request->all())->save();

        if ($updated)
            return response()->json([
                                        'success' => true
                                    ]);
        else
            return response()->json([
                                        'success' => false,
                                        'message' => 'Project could not be updated'
                                    ], 500);
    }

    public function destroy($id)
    {
        $project =Project::whereId($id)->first();

        if (!$project) {
            return response()->json([
                                        'success' => false,
                                        'message' => 'Project with id ' . $id . ' not found'
                                    ], 400);
        }
        if($project->user_id !=  auth()->user()->id && empty(auth()->user()->is_admin)){
            return response()->json([
                                        'success' => false,
                                        'message' => 'Projects can be deleted  by users (only those that the user owns) or admins'
                                    ], 400);
        }
        if ($project->delete()) {
            return response()->json([
                                        'success' => true
                                    ]);
        } else {
            return response()->json([
                                        'success' => false,
                                        'message' => 'Project could not be deleted'
                                    ], 500);
        }
    }
}
