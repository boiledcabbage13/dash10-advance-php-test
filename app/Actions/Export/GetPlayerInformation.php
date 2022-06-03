<?php

namespace App\Actions\Export;

use App\Models\Roster;

class GetPlayerInformation {
    public function execute($type, $query, $format) {
        $data = [];

        $query = $this->setQuery($query);

        if ($type === 'playerstats') {
            $data = $this->getPlayerStats($query);
        }

        if ($type === 'players') {
            $data = $this->getPlayers($query);
        }

        if (count($data) == 0) {
            exit("Error: No data found!");
        }

        return $data;
    }

    private function setQuery($query) {
        $search = [];
        $searchArgs = ['player', 'playerId', 'team', 'position', 'country'];
        foreach ($query as $key => $value) {
            if ($key !== 'type' && in_array($key, $searchArgs)) {
              $search[$key] = $value;
            }
        }

        $fields = [
            'team_code' => 'team',
            'pos' => 'position',
            'name' => 'player',
            'nationality' => 'country',
            'id' => 'playerId',
        ];

        $params = [];

        foreach ($fields as $key => $value) {
            if (isset($search[$value])) {
                $params[$key] = $search[$value];
            }
        }

        return $params;
    }

    public function getPlayerStats($query) {
        $roster = Roster::with(['team', 'player_total']);
        foreach ($query as $key => $value) {
            $roster->where($key, $value);
        }

        return $roster->get();
    }

    public function getPlayers($query) {
        $roster = Roster::query();

        foreach ($query as $key => $value) {
            $roster->where($key, $value);
        }

        return $roster->get();
    }
}