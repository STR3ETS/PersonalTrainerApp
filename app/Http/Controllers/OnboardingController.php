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
            // Onboarding data validatie (zonder email/name)
            'goal_type' => ['required', Rule::in(['cut', 'bulk', 'fit'])],
            'target_weight_kg' => ['nullable', 'numeric', 'min:30', 'max:300'],
            'fit_goal_text' => ['nullable', 'string', 'max:500'],
            'target_date' => ['nullable', 'date', 'after:today'],
            
            'current_weight_kg' => ['required', 'numeric', 'min:30', 'max:300'],
            'height_cm' => ['required', 'integer', 'min:120', 'max:230'],
            'birth_year' => ['required', 'integer', 'min:1930', 'max:' . (now()->year - 13)],
            'sex' => ['required', Rule::in(['male', 'female'])],
            'activity_level' => ['required', Rule::in(['sedentary', 'light', 'moderate', 'very'])],
            'experience_level' => ['required', Rule::in(['beginner', 'intermediate', 'advanced'])],
            
            'train_location' => ['required', Rule::in(['home', 'gym'])],
            'equipment' => ['nullable', 'array'],
            'equipment.*' => [Rule::in(['mat', 'dumbbells', 'bands', 'none'])],
            
            'days_per_week' => ['required', 'integer', 'min:2', 'max:7'],
            'session_minutes' => ['required', 'integer', Rule::in([30, 60, 90, 120])],
            'weekdays' => ['nullable', 'array'],
            'weekdays.*' => [Rule::in(['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'])],
            
            'bench_mode' => ['nullable', Rule::in(['1rm', '5reps', '10reps', 'unknown'])],
            'bench_weight_kg' => ['nullable', 'numeric', 'min:20', 'max:300'],
            
            'injuries' => ['nullable', 'array'],
            'injuries.*' => [Rule::in(['knee', 'back', 'shoulder', 'none', 'other'])],
            
            'nutrition_enabled' => ['required', Rule::in(['yes', 'no'])],
            'nutrition_rate_pct' => ['nullable', Rule::in(['-20', '-15', '-10', '+5', '+10'])],
            'diet_pref' => ['nullable', Rule::in(['none', 'vegetarian', 'halal', 'allergy', 'other'])],
            'diet_pref_text' => ['nullable', 'string', 'max:255'],
            
            'notify_channel' => ['required', Rule::in(['push', 'email', 'whatsapp', 'none'])],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // 1. Create anonymous user account
                $user = User::create([
                    'name' => 'Guest User',
                    'email' => 'guest_' . Str::random(10) . '@temp.local',
                    'password' => Hash::make(Str::random(16)),
                    'is_temp_account' => true, // Flag for temp account
                    'email_verified_at' => null,
                ]);

                // 2. Log user in
                Auth::login($user);

                // 3. Process onboarding data
                $this->processOnboardingData($validated, $user);
            });

            return response()->json([
                'success' => true,
                'message' => 'Onboarding voltooid!',
                'redirect' => route('app')
            ]);

        } catch (\Exception $e) {
            Log::error('Guest onboarding failed', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Er ging iets mis. Probeer het opnieuw.'
            ], 500);
        }
    }

    private function processOnboardingData(array $data, User $user)
    {
        // 1. Update user profile
        $user->profile()->create([
            'current_weight_kg' => $data['current_weight_kg'],
            'height_cm' => $data['height_cm'],
            'birth_year' => $data['birth_year'],
            'sex' => $data['sex'],
            'activity_level' => $data['activity_level'],
            'experience_level' => $data['experience_level'],
            'train_location' => $data['train_location'],
            'equipment' => $data['equipment'] ?? [],
        ]);

        // 2. Create fitness goal
        $user->goals()->create([
            'goal_type' => $data['goal_type'],
            'target_weight_kg' => $data['goal_type'] !== 'fit' ? $data['target_weight_kg'] : null,
            'fit_goal_text' => $data['goal_type'] === 'fit' ? $data['fit_goal_text'] : null,
            'target_date' => $data['target_date'] ?? null,
            'is_active' => true,
        ]);

        // 3. Create training schedule
        $user->trainingSchedules()->create([
            'days_per_week' => $data['days_per_week'],
            'session_minutes' => $data['session_minutes'],
            'weekdays' => $data['weekdays'] ?? [],
            'is_active' => true,
        ]);

        // 4. Create bench press record if provided
        if (!empty($data['bench_mode']) && $data['bench_mode'] !== 'unknown' && !empty($data['bench_weight_kg'])) {
            $reps = match($data['bench_mode']) {
                '1rm' => 1,
                '5reps' => 5,
                '10reps' => 10,
                default => null,
            };

            if ($reps) {
                $user->performanceRecords()->create([
                    'exercise_type' => 'bench_press',
                    'weight_kg' => $data['bench_weight_kg'],
                    'reps' => $reps,
                    'mode' => $data['bench_mode'],
                ]);
            }
        }

        // 5. Create nutrition settings
        $user->nutritionSettings()->create([
            'enabled' => ($data['nutrition_enabled'] ?? 'no') === 'yes',
            'calorie_adjustment_pct' => $data['nutrition_rate_pct'] ?? null,
            'diet_preference' => $data['diet_pref'] ?? 'none',
            'diet_preference_text' => $data['diet_pref_text'] ?? null,
            'injuries' => $data['injuries'] ?? [],
        ]);

        // 6. Create notification settings
        $user->notificationSettings()->create([
            'channel' => $data['notify_channel'] ?? 'none'
        ]);
    }
}