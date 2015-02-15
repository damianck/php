<?php

namespace Shop\MainBundle\Controller;

use Shop\MainBundle\Form\ActorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Shop\MainBundle\Entity\Actor;
use Symfony\Component\HttpFoundation\Request;

class ActorController extends Controller
{
    public function createAction(Request $request)
    {
        $actor = new Actor();

        $form = $this->createForm(
            new ActorType(),
            $actor
        );

        if ($request->isMethod('POST')
            && $form->handleRequest($request)
            && $form->isValid()
        ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($actor);
            $em->flush();
        }

        return $this->render(
            'ShopMainBundle:Actor:create.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ShopMainBundle:Actor");

        $collectionFeeds = $repository->findAll();


        return $this->render(
            'ShopMainBundle:Actor:index.html.twig',
            array(
                'movies' => $collectionFeeds,
            )
        );
    }
}