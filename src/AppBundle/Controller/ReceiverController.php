<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Progress;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class ReceiverController extends Controller
{
    /**
     * @Route("/", name="receive")
     */
    public function receiveAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $handled = $this->get('app.handler.change_handler')->handle($data);

        return new Response($handled);

    }
}
