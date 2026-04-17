<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobPostStoreRequest;
use App\Http\Requests\JobPostUpdateRequest;
use App\Models\JobPost;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::query()->with('employer:id,name,avatar');
        $query->where('status', 'open');

        if ($search = $request->query('q')) {
            $query->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('requirements', 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($employerId = $request->query('employer_id')) {
            $query->where('employer_id', $employerId);
        }

        $sort = $request->query('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage < 1 ? 1 : $perPage;
        $perPage = $perPage > 50 ? 50 : $perPage;

        return response()->json($query->paginate($perPage));
    }

    public function show(Request $request, JobPost $jobPost)
    {
        if ($jobPost->status === 'closed') {
            $user = $request->user();
            $canView = $user && $jobPost->employer_id === $user->id;

            if (!$canView) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        return response()->json(['job' => $jobPost->load('employer:id,name,avatar')]);
    }

    public function myJobs(Request $request)
    {
        $user = $request->user();

        $query = JobPost::query()->where('employer_id', $user->id)->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage < 1 ? 1 : $perPage;
        $perPage = $perPage > 50 ? 50 : $perPage;

        return response()->json($query->paginate($perPage));
    }

    public function store(JobPostStoreRequest $request)
    {
        $data = $request->validated();

        $jobPost = JobPost::create([
            'employer_id' => $request->user()->id,
            'title' => $data['title'],
            'category' => $data['category'],
            'type' => $data['type'],
            'salary_range' => $data['salary_range'] ?? null,
            'description' => $data['description'],
            'requirements' => $data['requirements'] ?? null,
            'status' => $data['status'] ?? 'open',
        ]);

        return response()->json(['job' => $jobPost], 201);
    }

    public function update(JobPostUpdateRequest $request, JobPost $jobPost)
    {
        if ($jobPost->employer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $jobPost->fill($request->validated());
        $jobPost->save();

        return response()->json(['job' => $jobPost]);
    }

    public function destroy(Request $request, JobPost $jobPost)
    {
        if ($jobPost->employer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $jobPost->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
