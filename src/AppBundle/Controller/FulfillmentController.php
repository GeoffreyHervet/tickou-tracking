<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fulfillment;
use AppBundle\Entity\User;
use AppBundle\Factory\FulfillmentFactory;
use AppBundle\Form\FileUploaderForm;
use AppBundle\Parser\CsvParser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Form;

class FulfillmentController extends Controller
{
    /**
     * @param Request $request
     * @param User $user
     *
     * @return RedirectResponse
     *
     * @Route("/fulfillment", name="app_fulfillment")
     * @Method("GET")
     */
    public function indexAction(UserInterface $user): Response
    {
        $fulfillments = $this->getDoctrine()->getManager()->getRepository(Fulfillment::class)->findBy([
            'user' => $user
        ]);
        return $this->render('AppBundle:Fulfillment:index.html.twig', [
            'user' => $user,
            'fulfillments' => $fulfillments
        ]);
    }
}
