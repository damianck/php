<?php
 
namespace Shop\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Shop\MainBundle\Entity\Movie;
use Shop\MainBundle\Form\MovieType;
use Symfony\Component\HttpFoundation\Request;

class MovieController extends Controller
{
	public function createAction(Request $request)
	{
		$movie = new Movie();
		$movie->setNumberOfSales(0);


		$form = $this->createForm(
			new MovieType(),
			$movie
		);

		if ($request->isMethod('POST')
		&& $form->handleRequest($request)
		&& $form->isValid()
		) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($movie);
			$em->flush();
		}

		return $this->render(
			'ShopMainBundle:Movie:create.html.twig',
			array(
				'form' => $form->createView(),
			)
		);
	}

	public function indexAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository("ShopMainBundle:Movie");

		$collectionMovies = $repository->findAll();


		return $this->render(
			'ShopMainBundle:Movie:index.html.twig',
			array(
				'movies' => $collectionMovies,
			)
		);
	}

	public function detailsAction(Request $request,$id)
	{
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository("ShopMainBundle:Movie");


		$movie = $repository->find($id);
		$category = $em->getRepository("ShopMainBundle:Category")->find($id);
		$actors = $em->getRepository("ShopMainBundle:Actor")->find($id);

		if (!$movie) {
			throw $this->createNotFoundException('Unable to find movie entity.');
		}
		
		return $this->render(
			'ShopMainBundle:Movie:details.html.twig',
			array(
				'movies' => $movie,
				'category' => $category,
				'actors' => $actors,
			)
		);
	}

	public function editAction(Request $request,$id)
	{
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository("ShopMainBundle:Movie");

		$movie = $repository->find($id);
		$category = $em->getRepository("ShopMainBundle:Category")->find($id);
		$actors = $em->getRepository("ShopMainBundle:Actor")->find($id);

		return $this->render(
			'ShopMainBundle:Movie:edit.html.twig',
			array(
				'movies' => $movie,
			)
		);
	}

	public function deleteAction(Request $request,$id)
	{
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository("ShopMainBundle:Movie");

		$collectionMovies = $repository->find($id);
		// ???

		return $this->render(
			'ShopMainBundle:Movie:index.html.twig',
			array(
				'movies' => $collectionMovies,
			)
		);
	}

}
