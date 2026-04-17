<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminUserUpdateRequest;
use App\Models\Application;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function stats()
    {
        $usersByRole = User::query()
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $jobsByStatus = JobPost::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $applicationsByStatus = Application::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'users' => $usersByRole,
            'jobs' => $jobsByStatus,
            'applications' => $applicationsByStatus,
        ]);
    }

    public function users(Request $request)
    {
        $query = User::query()->with('profile');

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        if ($search = $request->query('q')) {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 20);
        $perPage = $perPage < 1 ? 1 : $perPage;
        $perPage = $perPage > 50 ? 50 : $perPage;

        return response()->json($query->paginate($perPage));
    }

    public function showUser(User $user)
    {
        return response()->json(['user' => $user->load('profile')]);
    }

    public function updateUser(AdminUserUpdateRequest $request, User $user)
    {
        $user->fill($request->validated());
        $user->save();

        return response()->json(['user' => $user->load('profile')]);
    }

    public function jobs(Request $request)
    {
        $query = JobPost::query()->with('employer:id,name,avatar');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        $perPage = (int) $request->query('per_page', 20);
        $perPage = $perPage < 1 ? 1 : $perPage;
        $perPage = $perPage > 100 ? 100 : $perPage;

        return response()->json($query->latest()->paginate($perPage));
    }

    public function applications(Request $request)
    {
        $query = Application::query()->with(['jobPost', 'worker:id,name,email']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($jobPostId = $request->query('job_post_id')) {
            $query->where('job_post_id', $jobPostId);
        }

        if ($workerId = $request->query('worker_id')) {
            $query->where('worker_id', $workerId);
        }

        $perPage = (int) $request->query('per_page', 20);
        $perPage = $perPage < 1 ? 1 : $perPage;
        $perPage = $perPage > 100 ? 100 : $perPage;

        return response()->json($query->latest()->paginate($perPage));
    }
}
