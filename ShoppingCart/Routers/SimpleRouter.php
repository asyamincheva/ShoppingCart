<?php

namespace Routers;

use Framework\Routers\IRouter;

require_once 'Framework/Routers/IRouter.php';

class SimpleRouter implements IRouter
{
    /**
     * @return 'package/controller/method/param[0]/param[1]
     */
    public function getURI()
    {
        return 'admin/index/test';
    }

    public function getPost()
    {
        return [
            'simple' => 'routers'
        ];
    }

    public function getRequestMethod()
    {
        return 'get';
    }
}