<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index(){
        return \view(
            'redesign.pages.tournament',
        );
    }
}
