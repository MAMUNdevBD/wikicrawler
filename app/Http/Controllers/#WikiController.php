<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class WikiController extends Controller
{
    public $baseURL;

    public function __construct()
    {
        $this->baseURL = 'https://en.wikipedia.org/api/rest_v1';
    }

    public function getInfo(int $month, int $day)
    {
        // dd($this->getSinglePageInfo(338256));
        // $finalInfo = [];
        $persons = $this->onThisDay($month, $day);
        foreach ($persons->births as $person) {
            echo $person->pages[0]->pageid . '||';
            $infos = (object) [];
            foreach ($person->pages[0] as $key => $value) {
                $infos->{$key} = $value;
            }
            $pageInfos = $this->getSinglePageInfo($person->pages[0]->pageid);
            foreach ($pageInfos as $key => $value) {
                $infos->{$key} = $value;
            }
            $thumbnail = $infos->thumbnail ?? null;
            $originalimage = $infos->originalimage ?? null;
            $person = new Person();
            $person->pageid = $infos->pageid;
            $person->name = $infos->name ?? null;
            $person->birth_name = $infos->birth_name ?? null;
            $person->birth_date = $infos->birth_date ?? null;
            $person->birth_place = $infos->birth_place ?? null;
            $person->description = $infos->description ?? null;
            $person->occupation = $infos->occupation ?? null;
            $person->country = $infos->country ?? null;
            $person->thumbnail = json_encode($thumbnail);
            $person->originalimage = json_encode($originalimage);
            $person->save();
            // array_push($finalInfo, $infos);
        }
        echo "DONE";
    }

    public function onThisDay(int $month, int $day)
    {
        $url = $this->baseURL . '/feed/onthisday/births/' . $month . '/' . $day;
        return json_decode(file_get_contents($url));
        // 'date' => $day . ' ' . $month
    }

    public function getSinglePageInfo($pageID)
    {
        $url = 'https://en.wikipedia.org/w/api.php?action=query&prop=revisions&rvprop=content&rvsection=0&pageids=' . $pageID . '&format=json';
        $data = json_decode(file_get_contents($url));

        $stringData = $data->query->pages->$pageID->revisions[0]->{'*'};
        // dd($stringData);
        $infos = preg_grep('/^\|\s?[a-zA-Z_\s]+=.*/', explode("\n", $stringData));
        $datas = [];
        foreach ($infos as $info) {
            $d = null;
            preg_match('/[a-zA-Z]\S.*/', $info, $d);
            // echo $d[0] . '<br>';
            $a = explode('=', $d[0]);
            $datas[trim($a[0])] = trim($a[1]);
        }
        return $datas;
    }
}
