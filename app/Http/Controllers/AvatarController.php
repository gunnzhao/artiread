<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use App\User;

class AvatarController extends Controller
{
    private $bgColor = [
        '#66CCFF', '#66CCCC', '#CCFF99', '#FFCC66', '#CC3333',
        '#FF3366', '#3399CC', '#339999', '#669933', '#FFCC33',
        '#FF0033', '#CC0066', '#006699', '#006666', '#336633',
        '#FF9933', '#CC0033', '#990066', '#336699', '#003333',
        '#336633', '#FF6633', '#990033', '#660066', '#999999',
        '#666666', '#000000'
    ];

    public function show(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            abort(404);
        }

        $bgIndex = $user->id % count($this->bgColor);
        $text = strtoupper(substr($user->email, 0, 2));

        $img = Image::canvas(300, 300, $this->bgColor[$bgIndex]);
        $img->text($text, 150, 150, function($font) {
            $font->file(public_path() . '/font/SourceCodeVariable-Roman.ttf');
            $font->size(200);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });
        return $img->response('jpg');
    }
}
