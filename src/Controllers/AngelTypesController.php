<?php

declare(strict_types=1);

namespace Engelsystem\Controllers;

use Engelsystem\Http\Response;
use Engelsystem\Models\AngelType;

class AngelTypesController extends BaseController
{
    public function __construct(protected Response $response)
    {
    }

    public function about(): Response
    {
        $angeltypes = AngelType::all();
        $groups = [];
        foreach ($angeltypes as $angeltype) {
            [$group, $itemName] = str_contains($angeltype->name, ' - ') ? explode(' - ', $angeltype->name) : ['Sonstiges', $angeltype->name];
            $groups[$group][] = ['name' => $itemName, 'data' => $angeltype];
        }

        return $this->response->withView(
            'pages/angeltypes/about',
            ['angeltypes' => $groups]
        );
    }
}
