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

class ImporterController extends Controller
{
    /**
     * @param Request $request
     * @param User $user
     *
     * @return RedirectResponse
     *
     * @Route("/application", name="app_index")
     * @Method("GET")
     */
    public function indexAction(UserInterface $user): Response
    {
        return $this->renderWithForm($user, $this->getForm());
    }

    private function getForm(): Form
    {
        return $this->createForm(FileUploaderForm::class, null, [
            'action' => $this->generateUrl('upload')
        ]);
    }

    /**
     * @Route("/upload", name="upload")
     */
    public function uploadCsvAction(Request $request, UserInterface $user, CsvParser $parser)
    {
        $user = $this->getDoctrine()->getManager()->find(User::class, $user->getId());
        $form = $this->getForm();
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->renderWithForm($user, $form);
        }

        $file = $form->getData()['file'];
        $em = $this->getDoctrine()->getManager();
        $lineNumber = 1;
        $fulfillments = [];
        try {
            foreach ($parser->parse($file) as $line) {
                $lineNumber++;
                if (!isset($line['tracking'], $line['order'])) {
                    throw new \Exception('Tracking column or order column is missing.');
                }
                $fulfillment = FulfillmentFactory::createFromArray($line, $user);
                $fulfillments[] = $fulfillment;
                $em->persist($fulfillment);
            }
        } catch (\Exception $exception) {
            return $this->renderWithError($user, $exception);
        }

        $em->flush();
        $producer = $this->get('old_sound_rabbit_mq.fulfillment_producer');
        /** @var Fulfillment $fulfillment */
        foreach ($fulfillments as $fulfillment) {
            $producer->publish($fulfillment->getId());
        }

        return $this->redirectToRoute('app_fulfillment');
    }

    private function renderWithForm(User $user, Form $form)
    {
        return $this->render('AppBundle:Importer:index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
