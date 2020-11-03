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
 * @Route("/delegation")
 */
class DelegationController extends BaseController
{
    /**
     * @Route("/{name}", name="delegation")
     *
     * @return Response
     */
    public function indexAction(Delegation $delegation)
    {
        return $this->render('delegation/index.html.twig', ['delegation' => $delegation]);
    }
}
