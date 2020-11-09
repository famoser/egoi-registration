<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\Delegation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class IndexController extends BaseController
{
    /**
     * @Route("", name="index")
     *
     * @return Response
     */
    public function indexAction()
    {
        $delegations = $this->getDoctrine()->getRepository(Delegation::class)->findBy([], ['name' => 'ASC']);

        return $this->render('index.html.twig', ['delegations' => $delegations]);
    }
}
