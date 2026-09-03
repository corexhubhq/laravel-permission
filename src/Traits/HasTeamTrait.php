<?php

namespace Corexhubhq\LaravelPermission\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTeamTrait
{
    /**
     * Default relationship using package models from config.
     * Override this method on the consuming model to add extra scopes
     * (e.g. system_id filtering for multi-tenancy).
     */
    public function teams(): MorphToMany
    {
        $teamModel  = config('permission.team_model');
        $pivotTable = config('permission.pivot_table');

        return $this->morphToMany($teamModel, 'model', $pivotTable, 'user_id', 'model_id')
                    ->where("$pivotTable.related", $teamModel);
    }

    public function hasTeam(string $team): bool
    {
        return in_array($team, $this->getTeams('id'));
    }

    public function getTeams(?string $pluck = null): array
    {
        $teams = $this->teams()->get();
        if ($teams->isEmpty()) {
            return [];
        }

        if ($pluck) {
            return $teams->pluck($pluck)->toArray();
        }

        return $teams->keyBy('id')->toArray();
    }
}
