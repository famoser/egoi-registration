<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Base;

use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BaseController extends AbstractController
{
    /**
     * @return Participant|null
     */
    protected function getUser()
    {
        return parent::getUser();
    }

    public static function getSubscribedServices()
    {
        return parent::getSubscribedServices() + ['session' => '?'.SessionInterface::class];
    }

    /**
     * @param string $message the translation message to display
     * @param string $link
     */
    protected function displayError(string $message, string $link = null)
    {
        $this->displayFlash('danger', $message, $link);
    }

    /**
     * @param string $message the translation message to display
     * @param string $link
     */
    protected function displaySuccess(string $message, string $link = null)
    {
        $this->displayFlash('success', $message, $link);
    }

    /**
     * @param string $message the translation message to display
     * @param string $link
     */
    protected function displayDanger(string $message, string $link = null)
    {
        $this->displayFlash('danger', $message, $link);
    }

    /**
     * @param string $message the translation message to display
     * @param string $link
     */
    protected function displayWarning(string $message, string $link = null)
    {
        $this->displayFlash('warning', $message, $link);
    }

    /**
     * @param string $message the translation message to display
     * @param string $link
     */
    protected function displayInfo(string $message, string $link = null)
    {
        $this->displayFlash('info', $message, $link);
    }

    /**
     * @param $type
     * @param $message
     * @param string $link
     */
    private function displayFlash($type, $message, $link = null)
    {
        if (null !== $link) {
            $message = '<a href="'.$link.'">'.$message.'</a>';
        }
        $this->get('session')->getFlashBag()->add($type, $message);
    }
}
