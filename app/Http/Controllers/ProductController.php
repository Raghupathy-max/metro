<?php

namespace App\Http\Controllers;


class ProductController extends Controller
{
    public function index()
    {
        return response('Products', [
            'products' => [
                [
                    'id' => 1,
                    'name' => 'Metro QR Ticket',
                    'description' => 'QR ticket for one-way or return journey',
                    'img' => '/img/qr.gif',
                    'url' => '/ticket/dashboard'
                ],
                [
                    'id' => 2,
                    'name' => 'Store Value Pass',
                    'description' => 'QR ticket for one-way or return journey',
                    'img' => '/img/sv_pass.gif',
                    'url' => '/sv/dashboard'
                ],
                [
                    'id' => 2,
                    'name' => 'Trip Pass',
                    'description' => 'QR ticket for one-way or return journey',
                    'img' => '/img/tp_pass.gif',
                    'url' => '/tp/dashboard'
                ],
            ]
        ]);
    }
}
