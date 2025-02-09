<?php

namespace App\Http\Controllers\Generated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactPageController extends Controller
{
   function loader() {
    return ["title" => "Contact Page (From Loader)"];
}

function action() {
    $name = request()->input('name');
    return ["message" => "Laravel backend says: Hello " . $name];
}
}
