<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class WikiController extends Controller
{
    public $baseURL;

    public function __construct()
    {
        $this->baseURL = 'https://en.wikipedia.org';
    }
    public function check(Request $request)
    {
        return view('check');
    }

    public function checkData(Request $request)
    {
        dd($this->getTitles($request->wiki_url));
    }

    public function getUnGrabbedPageName(array $names, string $collection)
    {
        $datas = DB::table('people')->where('mj_collection', $collection)->select('page_name')->get();
        $page_names = [];
        foreach ($datas as $data) {
            array_push($page_names, $data->page_name);
        }
        return $page_names;
    }
    public function collect(Request $request)
    {
        $request->validate([
            'wiki_url' => 'required|url',
            'stop_at' => 'required|numeric'
        ]);
        $wiki_url = $request->wiki_url;
        $url_segs = explode('/', $wiki_url);
        $collection = end($url_segs);
        $titles = $this->getTitles($wiki_url);
        $titles = array_slice($titles, 0, $request->stop_at + 1);
        foreach ($titles as $title) {
            echo $this->grabData($title, $collection) . '|collection|<br>';
        }
        return back()->with('success', 'DONE');
    }

    public function grabData($title, $collection)
    {
        $data = $this->getData($title, $collection);
        echo $data['page_id'] . '|grabData|';
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
        preg_match_all('/<li><a\s\w+="\/\w+\/(\S+)"/', $file, $data);
        return $data[1];
    }

    public function getData($title, $collection)
    {
        $file = $this->getPage($title);
        preg_match('/#REDIRECT\s?\[\[(.*)\]\]/', $file, $redirect);
        if ($redirect) {
            $title = str_replace(' ', '_', $redirect[1]);
            $file = $this->getPage($title);
        }
        preg_match_all('/^\|\W*([a-zA-Z-_0-9]+)\s*=(.*)/m', $file, $datas);
        preg_match('/pageid="(\w+)/', $file, $pageId);
        echo $pageId[1] . '|getData|\n';
        preg_match('/[sS]hort\s[Dd]escription\|(.*)}}/', $file, $shortDescription);
        $data = array_combine($datas[1], $datas[2]);
        $data['page_id'] = $pageId[1];
        $data['page_name'] = $title;
        $data['short_desc'] = $shortDescription[1] ?? '';
        $data['mj_collection'] = $collection;

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

    public function getPage($title)
    {
        $url = 'https://en.wikipedia.org/w/api.php?action=query&prop=revisions&rvprop=content&rvsection=0&format=xml&titles=' . $title;
        $response = Http::get($url);
        return $response->body();
    }
}
