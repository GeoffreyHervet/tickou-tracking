<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fulfillment;
use AppBundle\Entity\User;
use AppBundle\Form\FulfillmentForm;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

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

    /**
     * @Route("/fulfillment/{id}", name="fulfillment_show")
     * @Method("GET")
     */
    public function showAction(UserInterface $user, Fulfillment $fulfillment)
    {
        if ($fulfillment->getUser()->getId() !== $user->getId()) {
            return $this->createAccessDeniedException();
        }

        return $this->render('AppBundle:Fulfillment:show.html.twig', [
            'user' => $user,
            'fulfillment' => $fulfillment
        ]);
    }

    /**
     * @Route("/fulfillment/delete/{id}", name="fulfillment_delete")
     * @Method("GET")
     */
    public function deleteAction(UserInterface $user, Fulfillment $fulfillment)
    {
        if ($fulfillment->getUser()->getId() !== $user->getId()) {
            return $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($fulfillment);
        $em->flush($fulfillment);

        return $this->redirectToRoute('app_fulfillment');
    }

    /**
     * @Route("/fulfillment/edit/{id}", name="fulfillment_edit")
     * @Method("GET")
     */
    public function editAction(UserInterface $user, Fulfillment $fulfillment)
    {
        if ($fulfillment->getUser()->getId() !== $user->getId()) {
            return $this->createAccessDeniedException();
        }

        return $this->render('AppBundle:Fulfillment:edit.html.twig', [
            'user' => $user,
            'fulfillment' => $fulfillment,
            'form' => $this->getForm($fulfillment)->createView()
        ]);
    }

    private function getForm(Fulfillment $fulfillment)
    {
        return $this->createForm(FulfillmentForm::class, $fulfillment, [
            'action' => $this->generateUrl('fulfillment_edit', ['id' => $fulfillment->getId()]),
            'method' => 'POST',
        ]);
    }

    /**
     * @Route("/fulfillment/edit/{id}")
     * @Method("POST")
     */
    public function updateAction(
        UserInterface $user,
        Fulfillment $fulfillment,
        Request $request,
        ManagerRegistry $doctrine
    ) {
        if ($fulfillment->getUser()->getId() !== $user->getId()) {
            return $this->createAccessDeniedException();
        }

        $form = $this->getForm($fulfillment);
        if (!$form->handleRequest($request)->isSubmitted() || !$form->isValid()) {
            return $this->render('AppBundle:Fulfillment:edit.html.twig', [
                'user' => $user,
                'fulfillment' => $fulfillment,
                'form' => $form->createView()
            ]);
        }

        $doctrine->getManager()->flush($fulfillment);
        $fulfillment->setType(Fulfillment::TYPE_IN_PROGRESS);
        $request->getSession()->getFlashBag()->add('success', 'fulfillment.flash.edited');
        $this->get('old_sound_rabbit_mq.fulfillment_producer')->publish($fulfillment->getId());

        return $this->redirectToRoute('app_fulfillment');
    }
}
