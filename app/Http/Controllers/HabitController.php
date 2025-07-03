<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HabitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user =auth()->user();
        $habits =$user->habits()->with('trackings')->get();
        return response()->json(["data"=>$habits]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user =auth()->user();
        if(!$user){
            return response()->json(["errors"=>"Unauthenticated"]);
        }
        $validator =Validator::make($request->all(),[
            'name'=>'required|string',
            'description'=>'required|string',
            'category'=>'required|string',
            'frequency'=>'required|string|in:daily,weekly,monthly',
            'target'=>'required|integer',
            'color'=>'nullable|string',
            'icon'=>'nullable|string',
            'is_active'=>'required|boolean',
            'current_streak'=>'required|integer',
            'longest_streak'=>'required|integer',
            'total_completions'=>'required|integer',
            'reminder_time' => 'nullable|date_format:H:i',
            'reminder_days' => 'nullable|array',
            'reminder_days.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            'difficulty'=>'required|string|in:easy,medium,hard'

        ]);
        if($validator->fails()){
            return response()->json(["errors"=>$validator->errors()]);
        }
        try{
            $user->habits()->create($request->all());
            return response()->json(["message"=>"Habit created successfully"]);
        }catch(\Exception $e){
            return response()->json(["errors"=>$e->getMessage()]);
        }



    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user =auth()->user();
        if(!$user){
            return response()->json(["errors"=>"Unauthenticated"]);
        }
        $habit =$user->habits()->with('trackings')->find($id);
        return response()->json(["data"=>$habit]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Habit $habit)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(["errors" => "Unauthenticated"], 401);
        }

        // Vérifier si l'utilisateur est bien le propriétaire
        if ($habit->user_id !== $user->id) {
            return response()->json(["errors" => "Unauthorized. You are not the owner "], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string',
            'frequency' => 'required|string|in:daily,weekly,monthly',
            'target' => 'required|integer',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'required|boolean',
            'current_streak' => 'required|integer',
            'longest_streak' => 'required|integer',
            'total_completions' => 'required|integer',
            'reminder_time' => 'nullable|date_format:H:i',
            'reminder_days' => 'nullable|array',
            'reminder_days.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            'difficulty' => 'required|string|in:easy,medium,hard'
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        try {
            $habit->update($request->all());
            return response()->json(["message" => "Habit updated successfully"]);
        } catch (\Exception $e) {
            return response()->json(["errors" => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user =auth()->user();
        if(!$user){
            return response()->json(["errors"=>"Unauthenticated"]);
        }
        try{
            $user->habits()->find($id)->delete();
            return response()->json(["message"=>"Habit deleted successfully"]);
        }catch(\Exception $e){
            return response()->json(["errors"=>$e->getMessage()]);
        }
    }

    /**
     * Marquer une habitude comme complétée pour aujourd’hui
     */
    public function markComplete(string $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(["errors" => "Unauthenticated"], 401);
        }

        // Vérifier que l'habitude existe et appartient bien à l'utilisateur
        $habit = $user->habits()->find($id);

        if (!$habit) {
            return response()->json(["errors" => "Habit not found or unauthorized"], 404);
        }

        try {
            $today = now()->toDateString();

            $tracking = $habit->trackings()->firstOrCreate(
                ['date' => $today],
                ['completed' => true]
            );

            if (!$tracking->wasRecentlyCreated) {
                $tracking->completed = true;
                $tracking->save();
            }

            // Mettre à jour les stats proprement
            $habit->total_completions++;
            $habit->current_streak++;

            if ($habit->current_streak > $habit->longest_streak) {
                $habit->longest_streak = $habit->current_streak;
            }

            $habit->save();

            return response()->json([
                "success" => true,
                "message" => "Habit marked as complete for today",
                "data" => $tracking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }
}
