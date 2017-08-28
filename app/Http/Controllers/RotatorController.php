<?php

namespace App\Http\Controllers;

use App\Helpers\WebsiteMeta\WebsiteMeta;
use App\Models\MainMeta;
use Carbon\Carbon;
use Helpers\Functions\Users;
use Illuminate\Http\Request;

class RotatorController extends Controller
{

    public function index(){

        $mainMeta = MainMeta::firstOrFail();
        if(!empty($mainMeta)){
            $title = $mainMeta->title;
            $description = $mainMeta->description;
            $keywords = explode(",", $mainMeta->keywords);
            $publishedTime = Carbon::now()->toW3cString();
            $modifiedTime = Carbon::now()->toW3cString();
            $author = Users::adminUser()->fullName();
            $currentUrl = env('APP_URL');
            $image = env('APP_URL') . '/assets/images/og/bitcoin.png';
            $categoryDescription = "Bitcoin Faucet Rotator";

            WebsiteMeta::setCustomMeta($title, $description, $keywords, $publishedTime, $modifiedTime, $author, $currentUrl, $image, $categoryDescription);
        }

        return view('rotator.index');
    }
}
