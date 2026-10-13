<?php

namespace App\Http\Controllers;

use App\Models\SentMail;

class SentMailController extends Controller
{
    public function track(string $id)
    {
        $sentMail = SentMail::find($id);

        if ($sentMail && (int) $sentMail->opened !== 1) {
            $sentMail->opened = 1;
            $sentMail->save();
        }

        // Return a transparent 1x1 GIF image
        $image = base64_decode('R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=');

        return response($image, 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}