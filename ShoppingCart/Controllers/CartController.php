<?php

namespace Controllers;

use Framework\BaseController;
use Framework\Common;
use Models\ViewModels\CartViewModel\CartProductViewModel;
use Models\ViewModels\CartViewModel\IndexViewModel;
use Models\ViewModels\CartViewModel\CheckoutViewModel;
use Models\ViewModels\CartViewModel\Product;

class CartController extends BaseController
{
    /**
     * @Authorize
     */
    public function index()
    {
        $cart = $this->session->cart;
        $products = [];
        $totalPrice = 0;
        $money = 0;

        if ($cart) {
            foreach ($cart as $itemId) {
                $this->db->prepare("
                    SELECT p.id, p.name, p.price
                    FROM products p
                    JOIN products_categories pc
                        ON p.id = pc.productId
                    JOIN categories c
                        ON pc.categoryId = c.id
                    WHERE p.id = ?",
                    [ $itemId ]);

                $response = $this->db->execute()->fetchRowAssoc();
                $price = Common::normalize($response['price'], 'noescape|double');

                $this->db->prepare("
                    SELECT percentage
                    FROM promotions
                    WHERE productId = ? AND NOW() < endDate",
                    [ $itemId ]);
                $promos = $this->db->execute()->fetchAllAssoc();
                $bestPromo = 0;

                foreach ($promos as $promo) {
                    $currentPromo = Common::normalize($promo['percentage'], 'noescape|double');

                    if ($currentPromo > $bestPromo) {
                        $bestPromo = $currentPromo;
                    };
                }

                $price = $price * (1 - $bestPromo / 100);

                $product = new CartProductViewModel(
                    Common::normalize($response['id'], 'noescape|int'),
                    $response['name'],
                    $price);

                $products[] = $product;
                $totalPrice += $price;
            }

            $this->db->prepare("
                SELECT cash
                FROM users
                WHERE id = ? AND username = ?",
                [ $this->session->_login, $this->session->_username ]);
            $response = $this->db->execute()->fetchRowAssoc();
            $money = isset($response['cash']) ? Common::normalize($response['cash'], 'noescape|double') : 0;
        }

        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', new IndexViewModel($products, $totalPrice, $money));
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.cart');
    }

    /**
     * @Authorize
     * @Get
     * @Route("cart/add/{id:int}")
     */
    public function add()
    {
        if (!$this->session->cart) {
            $this->session->cart = [];
        }

        $cart = $this->session->cart;
        $cart[] = $this->input->get(2);
        $this->session->cart = $cart;

        $this->redirect($this->path);
    }

    /**
     * @Authorize
     * @Delete
     * @Route("cart/remove/{id:int}")
     */
    public function remove()
    {
        if (!$this->session->cart) {
            throw new \Exception("Cart is empty!", 500);
        }

        $id = $this->input->get(2);
        $cart = $this->session->cart;

        foreach ($cart as $k => $itemId) {
            if ($itemId == $id) {
                unset($cart[$k]);
                break;
            }
        }

        $this->session->cart = $cart;
        $this->redirect($this->path . 'cart');
    }

    /**
     * @Authorize
     * @Post
     * @Route("cart/checkout")
     */
    public function checkout()
    {
        $cart = $this->session->cart;

        if (!$cart) {
            throw new \Exception('Cart is empty!', 400);
        }

        $totalPrice = 0;
        $products = [];

        foreach ($cart as $itemId) {
            $this->db->prepare("
                SELECT p.price, p.name, p.id
                FROM products p
                JOIN products_categories pc
                    ON p.id = pc.productId
                JOIN categories c
                    ON pc.categoryId = c.id
                WHERE p.id = ?",
                [ $itemId ]);

            $response = $this->db->execute()->fetchRowAssoc();
            $price = Common::normalize($response['price'], 'noescape|double');

            $this->db->prepare("
                SELECT percentage
                FROM promotions
                WHERE productId = ? AND NOW() < endDate",
                [ $itemId ]);

            $promos = $this->db->execute()->fetchAllAssoc();
            $bestPromo = 0;

            foreach ($promos as $promo) {
                $currentPromo = Common::normalize($promo['percentage'], 'noescape|double');

                if ($currentPromo > $bestPromo) {
                    $bestPromo = $currentPromo;
                }
            }

            $price = $price * (1 - $bestPromo / 100);

            $products[] = new Product(
                Common::normalize($response['id'], 'noescape|int'),
                $response['name'],
                $price);

            $totalPrice += $price;
        }

        $this->db->prepare("
            SELECT Cash
            FROM users
            WHERE id = ? AND username = ?",
            [ $this->session->_login, $this->session->_username ]);

        $response = $this->db->execute()->fetchRowAssoc();
        $money = Normalizer::normalize($response['Cash'], 'noescape|double');

        if ($money - $totalPrice < 0) {
            $diff = $totalPrice - $money;
            throw new \Exception("You don't have enough money for this purchase. Needed $diff more!", 400);
        }

        $boughtProducts = [];
        $outOfStockProducts = [];

        foreach ($products as $p => $product) {
            $this->db->prepare("
                UPDATE products
                SET quantity = quantity - 1
                WHERE id = ? AND quantity > 0",
                [ $product->getId() ]);

            $response = $this->db->execute()->affectedRows();

            if ($response) {
                $this->db->prepare("
                    UPDATE users
                    SET Cash = Cash - ?
                    WHERE id = ? AND username = ?",
                    [ $product->getPrice(), $this->session->_login, $this->session->_username ]);

                $this->db->execute();
                $boughtProducts[] = $product;
            } else {
                $outOfStockProducts[] = $product;
            }
        }

        if (count($outOfStockProducts) !== 0) {
            $viewModel = new CheckoutViewModel('Not all items bought!', $outOfStockProducts);
        } else {
            $viewModel = new CheckoutViewModel('All items bought!', array());
        }

        $this->session->cart = [];
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', $viewModel);
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.checkout');
    }
}