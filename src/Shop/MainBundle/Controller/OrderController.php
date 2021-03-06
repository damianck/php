<?php

namespace Shop\MainBundle\Controller;

use DateTime;
use Shop\MainBundle\Entity\Order;
use Shop\MainBundle\Entity\OrderStatus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrderController extends Controller
{
	private function setCartCount()
	{
		$em = $this->getDoctrine()->getEntityManager();
		$cart = $em->getRepository("ShopMainBundle:Cart")->findOneBy(array('userId'=> $this->getUser()->getId()));

		$session = $this->getRequest()->getSession();

		if(is_null($cart) || is_null(count($cart->getMoviesId()))  )
		{
			$session->set('cartCount', 0);
		}
		else
		{
			$session->set('cartCount', count($cart->getMoviesId()));
		}

	}

	protected function GetCurrentUserOrders()
	{
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository("ShopMainBundle:Order");
		$orders = $repository-> findBy(array('userId' => $this->getUser()->getId() ));

		return $orders;
	}

	protected function GetNewOrderFromCart()
	{
		$userId = $this->getUser()->getId();
		$em = $this->getDoctrine()->getManager();

		$repository = $em->getRepository("ShopMainBundle:Cart");
		$cart = $repository-> findOneBy(array('userId' => $userId ));

		$order = new Order();
		$order->setStatus($em->getRepository("ShopMainBundle:OrderStatus")->findOneBy(array('id'=>1)));
		$order->setUserId($em->getRepository("ShopMainBundle:User")->findOneBy(array('id'=> $userId)));

		$order->setDateOfCreateOrder(new dateTime());

		$repo= $em->getRepository("ShopMainBundle:Movie");

		if($cart !== NULL) {
			foreach ($cart->getMoviesId() as &$item)
			{
				$order->addMovie($repo->findOneBy(array('id' => $item)));
			}
		}
		$em->persist($order);

		return $order;
	}


	protected function ClearCart()
	{
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository("ShopMainBundle:Cart");

		$cart = $repository ->findOneBy(
			array( 'userId' => $this->getUser()->getId())
		);

		$cart->setMoviesId([]);

		$em->persist($cart);
		$em->flush();
		$this->setCartCount();
	}

	protected function InitializeOrderStatus()
	{
		$em = $this->getDoctrine()->getEntityManager();

		$status = $em->getRepository("ShopMainBundle:OrderStatus")->findAll();

		if(is_null($status) || count($status) === 0)
		{
			$status1 = new OrderStatus();
			$status1->setStatus("Utworzone");

			$em->persist($status1);

			$status2 = new OrderStatus();
			$status2->setStatus("Oczekiwanie na płatnośc");

			$em->persist($status2);

			$status3 = new OrderStatus();
			$status3->setStatus("Zrealizowane");

			$em->persist($status3);

			$em->flush();
		}
	}



	public function createAction()
	{
		$this->InitializeOrderStatus();
		$em = $this->getDoctrine()->getManager();

		$user = $this->getUser();
		$order = $this->GetNewOrderFromCart();

		if(count($order->getMovies()) == 0)
		{
			return $this->redirect($this->generateUrl('shop_main_homepage'));
		}

		$user->addOrder($order);
		$em->persist($user);
		$em->flush();

		$totalCost = 0;
		$result = [];
		if($order !== NULL) {
			foreach ($order->getMovies() as &$movie) {
				$totalCost += $movie->getPrice();
				array_push($result,$movie);
			}
		}

		$this->ClearCart();


		return $this->render(
			'ShopMainBundle:Order:create.html.twig',
			array(
				'order' => $result,
				'status' => $order->getStatus()->getStatus(),
				'totalCost' =>$totalCost,

			)
		);
	}

	public function indexAction(Request $request)
	{
		$this->InitializeOrderStatus();
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository("ShopMainBundle:Order");

		$collectionMovies = $repository->findAll();


		return $this->render(
			'ShopMainBundle:Order:index.html.twig',
			array(
				'movies' => $collectionMovies,
			)
		);
	}

}
