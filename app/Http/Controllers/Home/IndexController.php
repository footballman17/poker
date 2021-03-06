<?php

namespace App\Http\Controllers\Home;

class IndexController extends \App\Http\Controllers\SuperController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->title  = 'Пятикарточный покер';
        $this->layout = env('THEME') . ".route.main";
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (!session()->has('typeForm')) {
            session(['typeForm' => 'login']);
        }
        $this->content = view(env('THEME') . '.home.index')->render();

        return $this->renderOutput();
    }
}
