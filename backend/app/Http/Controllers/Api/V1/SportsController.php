<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MatchRecord;
use App\Models\Tournament;
use App\Services\AuditService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SportsController extends Controller
{
    private const STATUSES = ['UPCOMING', 'LIVE', 'COMPLETED', 'CANCELLED'];

    public function tournaments(Request $request): JsonResponse
    {
        $query = Tournament::query()->withCount('matches')->orderByDesc('created_at');
        if (in_array($request->query('active'), ['true', 'false'], true)) {
            $query->where('is_active', $request->query('active') === 'true');
        }

        return ApiResponse::success($query->get()->map(function (Tournament $tournament): array {
            $data = $tournament->attributesToArray();
            $data['_count'] = ['matches' => (int) $tournament->matches_count];

            return $data;
        }));
    }

    public function storeTournament(Request $request, AuditService $audit): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'sport_type' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);
        if (Tournament::query()->where('slug', $data['slug'])->exists()) {
            return ApiResponse::error('यो स्लग पहिले नै प्रयोग भइसकेको छ', 409);
        }
        $tournament = Tournament::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'Tournament', $tournament->id, newValue: [
            'name' => $tournament->name,
            'slug' => $tournament->slug,
        ]);

        return ApiResponse::success($tournament, 201);
    }

    public function matches(Request $request): JsonResponse
    {
        $page = max(1, $request->integer('page', 1));
        $pageSize = min(50, max(1, $request->integer('pageSize', 20)));
        $query = MatchRecord::query()
            ->with([
                'tournament:id,name,slug,sport_type',
                'homeTeam:id,name,name_en,logo',
                'awayTeam:id,name,name_en,logo',
            ]);
        if ($request->filled('tournament_id')) {
            $query->where('tournament_id', $request->string('tournament_id'));
        }
        if (in_array($request->query('status'), self::STATUSES, true)) {
            $query->where('status', $request->query('status'));
        }
        $total = (clone $query)->count();

        return ApiResponse::success([
            'data' => $query->orderByDesc('match_date')->forPage($page, $pageSize)->get(),
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => (int) ceil($total / $pageSize),
        ]);
    }

    public function storeMatch(Request $request, AuditService $audit): JsonResponse
    {
        $data = $request->validate([
            'tournament_id' => ['required', 'string', 'exists:tournaments,id'],
            'home_team_id' => ['required', 'string', 'exists:teams,id', 'different:away_team_id'],
            'away_team_id' => ['required', 'string', 'exists:teams,id'],
            'match_date' => ['required', 'date'],
            'venue' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
        ]);
        $data['status'] = in_array($data['status'] ?? null, self::STATUSES, true) ? $data['status'] : 'UPCOMING';
        $match = MatchRecord::query()->create($data)->load([
            'tournament:id,name,slug',
            'homeTeam:id,name,logo',
            'awayTeam:id,name,logo',
        ]);
        $audit->record($request->user(), 'CREATE', 'Match', $match->id, newValue: [
            'tournament_id' => $match->tournament_id,
            'home_team_id' => $match->home_team_id,
            'away_team_id' => $match->away_team_id,
            'match_date' => $match->match_date,
        ]);

        return ApiResponse::success($match, 201);
    }

    public function updateMatch(Request $request, AuditService $audit, string $id): JsonResponse
    {
        $match = MatchRecord::query()->find($id);
        if (! $match) {
            return ApiResponse::error('खेल फेला परेन', 404);
        }
        $data = $request->validate([
            'home_score' => ['sometimes', 'integer'],
            'away_score' => ['sometimes', 'integer'],
            'status' => ['sometimes', 'string'],
            'venue' => ['sometimes', 'nullable', 'string', 'max:255'],
            'match_date' => ['sometimes', 'date'],
        ]);
        if (isset($data['status']) && ! in_array($data['status'], self::STATUSES, true)) {
            unset($data['status']);
        }
        $oldValue = ['home_score' => $match->home_score, 'away_score' => $match->away_score, 'status' => $match->status];
        $match->update($data);
        $match->load(['tournament:id,name,slug', 'homeTeam:id,name,logo', 'awayTeam:id,name,logo']);
        $audit->record($request->user(), 'UPDATE', 'Match', $match->id, $oldValue, [
            'home_score' => $match->home_score,
            'away_score' => $match->away_score,
            'status' => $match->status,
        ]);

        return ApiResponse::success($match);
    }
}
