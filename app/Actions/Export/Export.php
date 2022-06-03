<?php

namespace App\Actions\Export;

use App\Models\Roster;
use LSS\Array2Xml;

class Export {
    public function execute($type, $data, $format) {
        if ($format === 'xml') {
            return $this->exportXML($type, $data->toArray());
        }

        if ($format === 'json') {
            return $this->exportJSON($type, $data);
        }

        if ($format === 'csv') {
            return $this->exportCSV($type, $data->toArray());
        }

        //html
        return $this->exportHTML($type, $data->toArray());
    }
    
    private function extractHeaders($data) {
        // extract headings
        // replace underscores with space & ucfirst each word for a decent heading
        $headingsKey = collect($data);
        $headings = $headingsKey->map(function($item, $key) {
            return collect(explode('_', $item))
                ->map(function($item, $key) {
                    return ucfirst($item);
                })
                ->join(' ');
        });
        
        return [
            'headingsKey' => $headingsKey,
            'headings' => $headings
        ];
    }

    private function exportXML($type, $data) {
        // fix any keys starting with numbers
        $keyMap = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        $xmlData = [];
        $typeData = $type === 'playerstats' ? $this->setPlayerStats($data) : $this->setPlayers($data);

        foreach ($typeData['data'] as $rowKey => $row) {
            $xmlRow = [];
            foreach ($row as $key => $value) {
                $key = preg_replace_callback('(\d)', function($matches) use ($keyMap) {
                    return $keyMap[$matches[0]] . '_';
                }, $key);
                $xmlRow[$key] = $value;
            }
            $xmlData[] = $xmlRow;
        }

        $xml = Array2XML::createXML('data', [
            'entry' => $xmlData
        ]);

        return response($xml->saveXML(), 200)->header('Content-Type', 'application/xml');
    }

    private function exportJSON($type, $data) {
        $typeData = $type === 'playerstats' ? $this->setPlayerStats($data) : $this->setPlayers($data);
        return json_encode($typeData['data']);
    }

    private function exportCSV($type, $data) {
        if (count($data) == 0) {
            return;
        }

        $csv = [];

        // extract headings
        // replace underscores with space & ucfirst each word for a decent headings
        $typeData = $type === 'playerstats' ? $this->setPlayerStats($data) : $this->setPlayers($data);
        $csv[] = $typeData['headings']->join(',');

        // format data
        foreach ($typeData['data'] as $dataRow) {
            $csv[] = implode(',', array_values($dataRow));
        }

        return response(implode("\n", $csv), 200)->header('Content-type', 'text/csv')
        ->header('Content-Disposition', 'attachment; filename="export.csv";');
    }

    private function exportHTML($type, $data) {

        $typeData = $type === 'playerstats' ? $this->setPlayerStats($data) : $this->setPlayers($data);

        return view('export_default', [
            'headings' => $typeData['headings'],
            'headingsKey' => $typeData['headingsKey'],
            'data' => $typeData['data']
        ]);
    }

    private function setPlayerStats($data) {
        $playerStatsHeadingsKey = [
            "name",
            "age",
            "games",
            "games_started",
            "minutes_played",
            "field_goals",
            "field_goals_attempted",
            "3pt",
            "3pt_attempted",
            "2pt",
            "2pt_attempted",
            "free_throws",
            "free_throws_attempted",
            "offensive_rebounds",
            "defensive_rebounds",
            "assists",
            "steals",
            "blocks",
            "turnovers",
            "personal_fouls",
            "total_points",
            "field_goals_pct",
            "3pt_pct",
            "2pt_pct",
            "free_throws_pct",
            "total_rebounds",
        ];

        $headings = $this->extractHeaders($playerStatsHeadingsKey);

        $newData = [];
        foreach ($data as $key => $value) {
            $newData[] = [
                "name" => $value["name"],
                "age" => $value['player_total']["age"],
                "games" => $value['player_total']["games"],
                "games_started" => $value['player_total']["games_started"],
                "minutes_played" => $value['player_total']["minutes_played"],
                "field_goals" => $value['player_total']["field_goals"],
                "field_goals_attempted" => $value['player_total']["field_goals_attempted"],
                "3pt" => $value['player_total']["3pt"],
                "3pt_attempted" => $value['player_total']["3pt_attempted"],
                "2pt" => $value['player_total']["2pt"],
                "2pt_attempted" => $value['player_total']["2pt_attempted"],
                "free_throws" => $value['player_total']["free_throws"],
                "free_throws_attempted" => $value['player_total']["free_throws_attempted"],
                "offensive_rebounds" => $value['player_total']["offensive_rebounds"],
                "defensive_rebounds" => $value['player_total']["defensive_rebounds"],
                "assists" => $value['player_total']["assists"],
                "steals" => $value['player_total']["steals"],
                "blocks" => $value['player_total']["blocks"],
                "turnovers" => $value['player_total']["turnovers"],
                "personal_fouls" => $value['player_total']["personal_fouls"],
                "total_points" => $value['player_total']["total_points"],
                "field_goals_pct" => $value['player_total']["field_goals_pct"],
                "3pt_pct" => $value['player_total']["3pt_pct"],
                "2pt_pct" => $value['player_total']["2pt_pct"],
                "free_throws_pct" => $value['player_total']["free_throws_pct"],
                "total_rebounds" => $value['player_total']["total_rebounds"],
            ];
        }

        return [
            'headings' => $headings['headings'],
            'headingsKey' => $headings['headingsKey'],
            'data' => $newData
        ];
    }

    private function setPlayers($data) {
        $playerHeadingKeys = [
            "team_code",
            "number",
            "name",
            "pos",
            "height",
            "weight",
            "dob",
            "nationality",
            "years_exp",
            "college",
        ];

        $headings = $this->extractHeaders($playerHeadingKeys);

        $newData = [];
        foreach ($data as $key => $value) {
            $newData[] = [
                "team_code" => $value["team_code"],
                "number" => $value["number"],
                "name" => $value["name"],
                "pos" => $value["pos"],
                "height" => $value["height"],
                "weight" => $value["weight"],
                "dob" => $value["dob"],
                "nationality" => $value["nationality"],
                "years_exp" => $value["years_exp"],
                "college" => $value["college"],
            ];
        }

        return [
            'headings' => $headings['headings'],
            'headingsKey' => $headings['headingsKey'],
            'data' => $data
        ];
    }
}