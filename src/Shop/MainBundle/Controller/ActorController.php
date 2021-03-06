<?php
namespace Shop\MainBundle\Controller;
use Shop\MainBundle\Form\ActorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Shop\MainBundle\Entity\Actor;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ActorController
 * @package Shop\MainBundle\Controller
 */
class ActorController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
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

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ShopMainBundle:Actor");
        $collectionActors = $repository->findAll();
        return $this->render(
            'ShopMainBundle:Actor:index.html.twig',
            array(
                'movies' => $collectionActors,
            )
        );
    }

    /**
     * @param Request $request
     */
    public function updateAction(Request $request)
    {
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ShopMainBundle:Actor");
        $actor = $repository->find($id);
        if (!$actor) {
            throw $this->createNotFoundException('No actor found for id '.$id);
        }
        //$em->remove($id);
        $em->remove($actor);
        $em->flush();
        //	return $this->redirect($this->generateUrl('ShopMainBundle:Movie:index.html.twig'));
        $actors = $repository->findAll();
        return $this->render(
            'ShopMainBundle:Actor:index.html.twig',
            array(
                'movies' => $actors,
            )
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ShopMainBundle:Actor");
        $actor = $repository->find($id);
        $category = $em->getRepository("ShopMainBundle:Category")->find($id);
        $actors = $em->getRepository("ShopMainBundle:Actor")->find($id);
        return $this->render(
            'ShopMainBundle:Actor:edit.html.twig',
            array(
                'actors' => $actor,
            )
        );
    }
}