<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function show()
    {
        return view('onboarding.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Personal information
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:' . now()->subYears(13)->toDateString()],
            'address' => ['required', 'string', 'max:500'],
            'gender' => ['required', Rule::in(['man', 'vrouw'])],
            'height_cm' => ['required', 'integer', 'min:140', 'max:220'],
            'weight_kg' => ['required', 'numeric', 'min:40', 'max:200'],
            
            // Injuries and goals  
            'injuries' => ['nullable', 'string', 'max:1000'],
            'training_goal' => ['required', 'string', 'max:1000'],
            'training_period' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            
            // Training frequency
            'trainings_per_week' => ['required', 'integer', 'min:2', 'max:6'],
            'multiple_per_day' => ['required', Rule::in(['ja', 'nee'])],
            'multiple_when' => ['nullable', 'string', 'max:255'],
            'session_duration' => ['required', 'integer', Rule::in([45, 60, 75, 90])],
            
            // Training background
            'training_background' => ['required', 'string', 'max:1000'],
            'current_frequency' => ['required', 'string', 'max:500'],
            'current_activities' => ['required', 'string', 'max:1000'],
            
            // Facilities and equipment
            'training_location' => ['required', Rule::in(['thuis', 'sportschool', 'buiten', 'combinatie'])],
            'equipment' => ['nullable', 'string', 'max:1000'],
            'hyrox_equipment' => ['nullable', 'string', 'max:1000'],
            
            // Additional notes
            'additional_notes' => ['nullable', 'string', 'max:1000'],
            
            // Heart rate data (all optional)
            'max_hr' => ['nullable', 'integer', 'min:150', 'max:220'],
            'rest_hr' => ['nullable', 'integer', 'min:40', 'max:100'],
            'zone1' => ['nullable', 'integer', 'min:100', 'max:200'],
            'zone2' => ['nullable', 'integer', 'min:100', 'max:200'],
            'zone3' => ['nullable', 'integer', 'min:100', 'max:200'],
            'zone4' => ['nullable', 'integer', 'min:100', 'max:200'],
            'zone5' => ['nullable', 'integer', 'min:100', 'max:220'],
            
            // Tests
            'cooper_done' => ['required', Rule::in(['ja', 'nee'])],
            'cooper_result' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'fivek_done' => ['required', Rule::in(['ja', 'nee'])],
            'fivek_result' => ['nullable', 'string', 'max:10'], // Format: "25:30"
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Create user account with real name
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => 'hyrox_' . Str::random(10) . '@temp.local',
                    'password' => Hash::make(Str::random(16)),
                    'is_temp_account' => true,
                    'email_verified_at' => null,
                ]);

                // Log user in
                Auth::login($user);

                // Process HYROX intake data
                $this->processHyroxIntakeData($validated, $user);
            });

            return response()->json([
                'success' => true,
                'message' => 'HYROX intake voltooid!',
                'redirect' => route('app')
            ]);

        } catch (\Exception $e) {
            Log::error('HYROX intake failed', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Er ging iets mis. Probeer het opnieuw.'
            ], 500);
        }
    }

    private function processHyroxIntakeData(array $data, User $user)
    {
        // 1. Update user profile with HYROX specific data
        $user->profile()->create([
            'birth_date' => $data['birth_date'],
            'address' => $data['address'],
            'gender' => $data['gender'],
            'height_cm' => $data['height_cm'],
            'weight_kg' => $data['weight_kg'],
            'injuries' => $data['injuries'],
            'training_location' => $data['training_location'],
            'equipment' => $data['equipment'],
            'hyrox_equipment' => $data['hyrox_equipment'],
            'additional_notes' => $data['additional_notes'],
        ]);

        // 2. Create HYROX training goal
        $user->goals()->create([
            'goal_type' => 'hyrox',
            'training_goal' => $data['training_goal'],
            'training_period' => $data['training_period'],
            'start_date' => $data['start_date'],
            'is_active' => true,
        ]);

        // 3. Create training schedule
        $user->trainingSchedules()->create([
            'trainings_per_week' => $data['trainings_per_week'],
            'session_duration' => $data['session_duration'],
            'multiple_per_day' => $data['multiple_per_day'] === 'ja',
            'multiple_when' => $data['multiple_when'],
            'is_active' => true,
        ]);

        // 4. Store training background
        $user->trainingBackground()->create([
            'background' => $data['training_background'],
            'current_frequency' => $data['current_frequency'],
            'current_activities' => $data['current_activities'],
        ]);

        // 5. Store heart rate data if provided
        if ($data['max_hr'] || $data['rest_hr'] || $data['zone1']) {
            $user->heartRateData()->create([
                'max_hr' => $data['max_hr'],
                'rest_hr' => $data['rest_hr'],
                'zone1' => $data['zone1'],
                'zone2' => $data['zone2'],
                'zone3' => $data['zone3'],
                'zone4' => $data['zone4'],
                'zone5' => $data['zone5'],
            ]);
        }

        // 6. Store Cooper test result
        if ($data['cooper_done'] === 'ja' && $data['cooper_result']) {
            $user->testResults()->create([
                'test_type' => 'cooper_12min',
                'result_value' => $data['cooper_result'],
                'result_unit' => 'meters',
                'test_date' => now(),
            ]);
        }

        // 7. Store 5K test result  
        if ($data['fivek_done'] === 'ja' && $data['fivek_result']) {
            $user->testResults()->create([
                'test_type' => '5k_run',
                'result_value' => $data['fivek_result'],
                'result_unit' => 'time',
                'test_date' => now(),
            ]);
        }
    }
}