<?php

namespace Shop\MainBundle\Controller;


use Shop\MainBundle\Entity\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class CartController
 * @package Shop\MainBundle\Controller
 */
class CartController extends Controller
{

    /**
     *
     */
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

    /**
     * @return null|object|Cart
     */
    protected function getCartForCurrentUser()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository("ShopMainBundle:Cart");

        $cart = $repository ->findOneBy(
            array( 'userId' => $this->getUser()->getId())
        );



        if(is_null($cart)  )
        {
            $cart = new Cart($this->getUser()->getId());
            $em->persist($cart);
        }

        return $cart;

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $tmp = $this->getUser();
        $userId = $tmp->getId();

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("ShopMainBundle:Cart");
        $cart = $repository-> findOneBy(array('userId' => $userId ));

        $repo= $em->getRepository("ShopMainBundle:Movie");

        $totalCost = 0;
        $result = [];

        if($cart !== NULL) {
        foreach ($cart->getMoviesId() as &$item) {
            $movie = $repo->findOneBy(array('id' => $item));
            $totalCost += $movie->getPrice();
            array_push($result,$movie);
        }
    }

        /** @var TYPE_NAME $this */
        $this->setCartCount();
        return $this->render(
            'ShopMainBundle:Cart:index.html.twig',
            array(
                'cart' => $result,
                'totalCost' =>$totalCost,

            )
        );
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addToCartAction( $id)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $cart = $this->getCartForCurrentUser();


        foreach($cart->getMoviesId() as &$item)
        {
            if($item == $id)
            {
                $this->setCartCount();
                return $this->render(
                    'ShopMainBundle:Cart:addToCart.html.twig',
                    array(
                        'id' => $id,
                        'status' => 'existInCart',
                    )
                );
            }
        }
        $orders = $em->getRepository("ShopMainBundle:Order")->findBy(array('userId' =>$this->getUser()->getId()));
        foreach($orders as &$item)
        {
            foreach($item->getMovies() as &$movie)
            {
                if ($movie->getId() == $id) {
                    $this->setCartCount();
                    return $this->render(
                        'ShopMainBundle:Cart:addToCart.html.twig',
                        array(
                            'id' => $id,
                            'status' => 'bought',
                        )
                    );
                }
            }
        }


        /** @var TYPE_NAME $cart */
        $cart->addMovieId($id);
        $em->persist($cart);
        $em->flush();

        /** @var TYPE_NAME $this */
        $this->setCartCount();
        return $this->render(
            'ShopMainBundle:Cart:addToCart.html.twig',
            array(
                'id' => $id,
                'status' => 'added',
            )
        );
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeFromCartAction( $id)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $cart = $this->getCartForCurrentUser();


        $cart->removeMovieId($id);
        $em->persist($cart);
        $em->flush();

        /** @var TYPE_NAME $this */
        $this->setCartCount();
        return $this->render(
            'ShopMainBundle:Cart:removeFromCart.html.twig',
            array(
                'id' => $id,
            )
        );
    }
}