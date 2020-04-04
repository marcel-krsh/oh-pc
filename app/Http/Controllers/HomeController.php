<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Image;
use App\Models\User;
use Yajra\Datatables\Datatables;;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct(){
        $this->allitapc();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    // Table Views
    public function usersTable(){
      return view('tables.users');
    }
    public function usersTableAjax(){
      return Datatables::of(User::query())->make(true);
    }
    public function imageGen($image)
    {
        $img = Image::canvas(800, 400, '#ccc');
        $img->text("APPROVED!", 400, 160, function ($font) {
            $font->file(base_path('storage/fonts/SourceSansPro-Light.ttf'));
            $font->size(80);
            $font->color('#000');
            $font->align('center');
            $font->valign('center');
            $font->angle(0);
        });
        $img->text("Hi ".$image."! It's ".date("M d, Y h:i:s", time()), 400, 215, function ($font) {
            $font->file(base_path('storage/fonts/SourceSansPro-Light.ttf'));
            $font->size(32);
            $font->color('#000');
            $font->align('center');
            $font->valign('center');
            $font->angle(0);
        });

        return $img->response('jpg');
    }
    public function noticeImageTrack(Notice $notice)
    {
        $img = Image::canvas(1, 1, '#fff');
      // $img->text("APPROVED!", 400, 160, function($font) {
      //     $font->file(base_path('storage/fonts/SourceSansPro-Light.ttf'));
      //     $font->size(80);
      //     $font->color('#000');
      //     $font->align('center');
      //     $font->valign('center');
      //     $font->angle(0);
      //   });
      // $img->text("Hi ".$image."! It's ".date("M d, Y h:i:s",time()), 400, 215, function($font) {
      //     $font->file(base_path('storage/fonts/SourceSansPro-Light.ttf'));
      //     $font->size(32);
      //     $font->color('#000');
      //     $font->align('center');
      //     $font->valign('center');
      //     $font->angle(0);
      //   });
        $notice->update(['read'=>1]);
        return $img->response('jpg');
    }
}
