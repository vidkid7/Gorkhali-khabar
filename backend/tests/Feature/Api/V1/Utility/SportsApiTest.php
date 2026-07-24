<?php

namespace Tests\Feature\Api\V1\Utility;

use App\Models\MatchRecord;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SportsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_tournaments_and_matches_preserve_filters_and_relations(): void
    {
        $active = Tournament::query()->create(['id' => 'active-tournament', 'name' => 'League', 'slug' => 'league', 'sport_type' => 'football', 'is_active' => true]);
        Tournament::query()->create(['id' => 'inactive-tournament', 'name' => 'Old League', 'slug' => 'old-league', 'sport_type' => 'football', 'is_active' => false]);
        $home = Team::query()->create(['id' => 'home-team', 'name' => 'Home', 'name_en' => 'Home', 'logo' => '/home.png']);
        $away = Team::query()->create(['id' => 'away-team', 'name' => 'Away', 'name_en' => 'Away', 'logo' => '/away.png']);
        MatchRecord::query()->create(['id' => 'completed-match', 'tournament_id' => $active->id, 'home_team_id' => $home->id, 'away_team_id' => $away->id, 'match_date' => '2026-07-22 10:00:00', 'status' => 'COMPLETED']);
        MatchRecord::query()->create(['id' => 'live-match', 'tournament_id' => $active->id, 'home_team_id' => $home->id, 'away_team_id' => $away->id, 'match_date' => '2026-07-23 10:00:00', 'status' => 'LIVE']);

        $this->getJson('/api/v1/sports/tournaments?active=true')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 'active-tournament')
            ->assertJsonPath('data.0._count.matches', 2);
        $this->getJson('/api/v1/sports/matches?status=LIVE&pageSize=1')
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.data.0.id', 'live-match')
            ->assertJsonPath('data.data.0.tournament.slug', 'league')
            ->assertJsonPath('data.data.0.home_team.id', 'home-team');
    }

    public function test_only_admin_can_create_tournaments_and_matches_or_update_scores(): void
    {
        $editor = $this->user('sports-editor', 'EDITOR');
        $admin = $this->user('sports-admin', 'ADMIN');
        $home = Team::query()->create(['id' => 'admin-home', 'name' => 'Home']);
        $away = Team::query()->create(['id' => 'admin-away', 'name' => 'Away']);

        $this->actingAs($editor)->postJson('/api/v1/sports/tournaments', ['name' => 'League', 'slug' => 'league', 'sport_type' => 'football'])->assertStatus(403);
        $tournament = $this->actingAs($admin)->postJson('/api/v1/sports/tournaments', ['name' => 'League', 'slug' => 'league', 'sport_type' => 'football'])
            ->assertCreated();
        $tournamentId = $tournament->json('data.id');
        $this->actingAs($admin)->postJson('/api/v1/sports/tournaments', ['name' => 'Duplicate', 'slug' => 'league', 'sport_type' => 'football'])->assertStatus(409);

        $match = $this->actingAs($admin)->postJson('/api/v1/sports/matches', [
            'tournament_id' => $tournamentId,
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'match_date' => '2026-07-23T12:00:00Z',
            'status' => 'LIVE',
        ])->assertCreated()->assertJsonPath('data.status', 'LIVE');
        $matchId = $match->json('data.id');

        $this->actingAs($admin)->putJson('/api/v1/sports/matches/'.$matchId, [
            'home_score' => '2',
            'away_score' => '1',
            'status' => 'COMPLETED',
        ])->assertOk()
            ->assertJsonPath('data.home_score', 2)
            ->assertJsonPath('data.away_score', 1)
            ->assertJsonPath('data.status', 'COMPLETED');
    }

    public function test_sports_writes_validate_required_relations_and_dates(): void
    {
        $admin = $this->user('sports-validation-admin', 'ADMIN');

        $this->actingAs($admin)->postJson('/api/v1/sports/tournaments', ['name' => '', 'slug' => '', 'sport_type' => ''])->assertStatus(400);
        $this->actingAs($admin)->postJson('/api/v1/sports/matches', [
            'tournament_id' => 'missing',
            'home_team_id' => 'missing-home',
            'away_team_id' => 'missing-away',
            'match_date' => 'invalid',
        ])->assertStatus(400);
    }

    private function user(string $id, string $role): User
    {
        return User::query()->create([
            'id' => $id,
            'name' => $id,
            'email' => $id.'@example.com',
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
