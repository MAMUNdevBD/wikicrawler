<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class WikiController extends Controller
{
    public $baseURL;

    public function __construct()
    {
        $this->baseURL = 'https://en.wikipedia.org';
    }
    public function check()
    {
    }
    public function collect(Request $request)
    {
        $request->validate([
            'wiki_url' => 'required|url'
        ]);
        $titles = $this->getTitles($request->wiki_url);
        foreach ($titles as $title) {
            echo $this->grabData($title) . '\n';
        }
        return back()->with('success', 'DONE');
    }

    public function grabData($title)
    {
        $data = $this->getData($title);
        echo $data['page_id'] . '\n';
        foreach ($data as $key => $value) {
            $this->column($key);
        }
        if (DB::table('people')->where('page_id', $data['page_id'])->count() == 0) {
            DB::table('people')->insert($data);
        }
        return $data['page_id'];
    }

    public function getTitles($urls)
    {
        $file = file_get_contents($urls);
        $data = preg_grep('/<li>{1}/', explode("\n", $file));
        $infos = preg_replace('/.*href="\/wiki\/[List_of|Help|Portal|Demographic].*/', '', $data);
        $titles = [];
        foreach ($infos as $info) {
            preg_match('/href="\/wiki\/["\']?([^"\'>]+)["\']?/', $info, $match);
            if (count($match) > 0) array_push($titles, $match[1]);
        }
        return $titles;
    }

    public function getData($title)
    {

        $url = $this->baseURL . '/w/api.php?action=query&prop=revisions&rvprop=content&rvsection=0&format=xml&titles=' . $title;
        $file = file_get_contents($url);
        preg_match_all('/\|\s?(.*)\s+ =(.*)/', $file, $datas);
        preg_match('/pageid="(\w+)/', $file, $pageId);
        echo $pageId[1] . '\n';
        preg_match('/[sS]hort\s[Dd]escription\|(.*)}}/', $file, $shortDescription);
        $values = array_map(function ($data) {
            if (!empty($data)) {
                $d = trim($data);
            } else {
                $d = $data;
            }
            return $d;
        }, $datas[2]);
        $keys = array_map(function ($data) {
            if (!empty($data)) {
                $d = 'mj_' . trim($data);
            }
            return $d;
        }, $datas[1]);
        $data = array_combine($keys, $values);
        $data['page_id'] = $pageId[1];
        $data['page_name'] = $title;
        $data['short_desc'] = $shortDescription[1] ?? '';

        return $data;
    }
    // public function trimvalu($value)
    // {
    //     if (!empty($value)) {
    //         $data = trim($value);
    //     } else {
    //         $data = $value;
    //     }
    //     return $data;
    // }
    public function column($column)
    {
        if (!Schema::hasColumn('people', $column)) {
            Schema::table('people', function (Blueprint $table) use ($column) {
                $table->text($column)->nullable();
            });
        }
    }
}
