<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationStatusRequest;
use App\Models\Application;
use App\Models\JobPost;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request, JobPost $jobPost)
    {
        $user = $request->user();

        if ($jobPost->status !== 'open') {
            return response()->json(['message' => 'Job is closed'], 422);
        }

        if ($jobPost->employer_id === $user->id) {
            return response()->json(['message' => 'Cannot apply to your own job'], 422);
        }

        $alreadyApplied = Application::query()
            ->where('job_post_id', $jobPost->id)
            ->where('worker_id', $user->id)
            ->exists();

        if ($alreadyApplied) {
            return response()->json(['message' => 'Already applied'], 409);
        }

        $application = Application::create([
            'job_post_id' => $jobPost->id,
            'worker_id' => $user->id,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        return response()->json(['application' => $application], 201);
    }

    public function myApplications(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage < 1 ? 1 : $perPage;
        $perPage = $perPage > 50 ? 50 : $perPage;

        $applications = Application::query()
            ->with('jobPost.employer:id,name,avatar')
            ->where('worker_id', $request->user()->id)
            ->latest()
            ->paginate($perPage);

        return response()->json($applications);
    }

    public function indexForEmployer(Request $request, JobPost $jobPost)
    {
        if ($jobPost->employer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage < 1 ? 1 : $perPage;
        $perPage = $perPage > 50 ? 50 : $perPage;

        $applications = Application::query()
            ->with('worker:id,name,email,phone_number,avatar')
            ->where('job_post_id', $jobPost->id)
            ->latest()
            ->paginate($perPage);

        return response()->json($applications);
    }

    public function updateStatus(ApplicationStatusRequest $request, Application $application)
    {
        $user = $request->user();

        if ($application->jobPost->employer_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $application->update(['status' => $request->validated()['status']]);

        return response()->json(['application' => $application]);
    }
}
