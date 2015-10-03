<?php

namespace Controllers;

use Framework\App;
use Framework\BaseController;
use Framework\Common;
use Models\BindingModels\ProductBindingModel\ChangeProductBindingModel;
use Models\BindingModels\ProductBindingModel\SellProductBindingModel;
use Models\BindingModels\NameBindingModel;
use Models\ViewModels\ProductViewModel\EditViewModel;
use Models\ViewModels\ProductViewModel\IndexViewModel;
use Models\ViewModels\ProductViewModel\ProductViewModel;
use Models\ViewModels\ProductViewModel\ProductMessage;

class ProductController extends BaseController
{
    /**
     * @Get
     * @Route("Products/{start:int}/{end:int}")
     */
    public function index()
    {
        $skip = $this->input->get(1);
        $take = $this->input->get(2) - $skip;
        $this->db->prepare("
            SELECT p.id, p.name, p.description, p.price, p.quantity, c.name as category
            FROM products p
            JOIN products_categories pc
                ON p.id = pc.productId
            JOIN categories c
                ON pc.categoryId = c.id
            WHERE quantity > 0
            ORDER BY p.id LIMIT {$take} OFFSET {$skip}");

        $response = $this->db->execute()->fetchAllAssoc();
        $products = [];

        foreach ($response as $product) {
            $productId = Common::normalize($product['id'], 'noescape|int');

            $this->db->prepare("
                SELECT percentage
                FROM promotions
                WHERE productId = ? AND NOW() < endDate",
                [ $productId ]);

            $promos = $this->db->execute()->fetchAllAssoc();
            $bestPromo = 0;

            foreach ($promos as $promo) {
                $currentPromo = Common::normalize($promo['percentage'], 'noescape|double');

                if ($currentPromo > $bestPromo) {
                    $bestPromo = $currentPromo;
                };
            }

            $products[] = new ProductViewModel(
                $productId,
                $product['name'],
                $product['description'],
                Common::normalize($product['price'], 'noescape|double'),
                Common::normalize($product['quantity'], 'noescape|int'),
                $product['category'],
                $bestPromo);
        }

        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', new IndexViewModel($products, $skip, $take + $skip));
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.products');
    }

    /**
     * @Get
     * @Route("product/{id:int}/show")
     */
    public function product()
    {
        $id = $this->input->get(1);
        $this->db->prepare("
            SELECT p.id, p.name, p.description, p.price, p.quantity, c.name as category
            FROM products p
            JOIN products_categories pc
              ON p.id = pc.productId
            JOIN categories c
              ON pc.categoryId = c.id
            WHERE p.id = ?",
            [ $id ]);

        $response = $this->db->execute()->fetchRowAssoc();

        if (!$response) {
            throw new \Exception("No product with id '$id'!", 404);
        }

        $quantity = Common::normalize($response['quantity'], 'noescape|int');

        if ($quantity <= 0) {
            if (!App::getInstance()->isAdmin() && !App::getInstance()->isEditor()) {
                throw new \Exception("No product with id '$id'!", 404);
            }
        }

        $this->db->prepare("
            SELECT u.username, u.isAdmin, u.isEditor, u.isModerator, r.message, r.id
            FROM reviews r
            JOIN products p
                ON r.productId = p.id
            JOIN users u
                ON r.userId = u.id
            WHERE p.id = ?",
            [ $id ]);

        $reviews = $this->db->execute()->fetchAllAssoc();
        $givenReviews = [];

        foreach ($reviews as $review) {
            $givenReviews[] = new ProductMessage(
                Common::normalize($review['id'], 'noescape|int'),
                $review['username'],
                $review['message'],
                Common::normalize($review['isAdmin'], 'noescape|bool'),
                Common::normalize($review['isEditor'], 'noescape|bool'),
                Common::normalize($review['isModerator'], 'noescape|bool')
            );
        }

        $this->db->prepare("
            SELECT percentage
            FROM promotions
            WHERE productId = ? AND NOW() < endDate",
            [ $id ]);

        $promos = $this->db->execute()->fetchAllAssoc();
        $bestPromo = 0;

        foreach ($promos as $promo) {
            $currentPromo = Common::normalize($promo['percentage'], 'noescape|double');

            if ($currentPromo > $bestPromo) {
                $bestPromo = $currentPromo;
            };
        }

        $product = new ProductViewModel(
            Common::normalize($response['id'], 'noescape|int'),
            $response['name'],
            $response['description'],
            Common::normalize($response['price'], 'noescape|double'),
            $quantity,
            $response['category'],
            $bestPromo,
            $givenReviews
        );

        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', $product);
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.product');
    }

    /**
     * @Route("products/sell")
     * @Authorize
     */
    public function sell()
    {
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', 'sell');
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.sellProduct');
    }

    /**
     * @Route("product/add")
     * @Post
     * @Authorize
     * @param SellProductBindingModel $model
     * @throws \Exception
     */
    public function add(SellProductBindingModel $model)
    {
        $this->db->prepare("
            SELECT id, name
            FROM categories
            WHERE name LIKE ?",
            [ $model->getCategory() ]);

        $response = $this->db->execute()->fetchRowAssoc();
        $categoryId = Common::normalize($response['id'], 'noescape|int');

        if (!$response) {
            $name = $model->getCategory();
            throw new \Exception("No category '$name'!", 404);
        }

        $this->db->prepare("
            INSERT INTO products (name, description, price, quantity)
            VALUES (?, ?, ?, ?)",
            [ $model->getName(), $model->getDescription(), $model->getPrice(), 1 ]);
        $this->db->execute();

        $this->db->prepare("
            SELECT id
            FROM products
            WHERE name = ? AND description = ?",
            [ $model->getName(), $model->getDescription() ]);
        $response = $this->db->execute()->fetchRowAssoc();
        $productId = Common::normalize($response['id'], 'noescape|int');

        $this->db->prepare("
            INSERT INTO products_categories (productId, categoryId)
            VALUES (?, ?)",
            [ $productId, $categoryId ]);
        $this->db->execute();

        $this->redirect("{$this->path}product/$productId/show");
    }

    /**
     * @Post
     * @Route("products/find")
     * @param NameBindingModel $model
     */
    public function find(NameBindingModel $model)
    {
        $this->db->prepare("
            SELECT id
            FROM products
            WHERE name LIKE ?",
            [ $model->getName() ]);

        $response = $this->db->execute()->fetchRowAssoc();

        if ($response) {
            $productId = Common::normalize($response['id'], 'noescape|int');
            $this->redirect("{$this->path}product/$productId/show");
        } else {
            $this->redirect("{$this->path}editor");
        }
    }

    /**
     * @Get
     * @Role("Editor")
     * @Route("product/{id:int}/edit")
     */
    public function edit()
    {
        $id = $this->input->get(1);

        $this->db->prepare("
            SELECT p.id, p.name, p.description, p.price, p.quantity, c.name as category
            FROM products p
            JOIN products_categories pc
                ON p.id = pc.productId
            JOIN categories c
                ON pc.categoryId = c.id
            WHERE p.id = ?",
            [ $id ]);

        $response = $this->db->execute()->fetchRowAssoc();

        if (!$response) {
            throw new \Exception("No product with id '$id'!", 404);
        }

        $product = new EditViewModel(
            Common::normalize($response['id'], 'noescape|int'),
            $response['name'],
            $response['description'],
            Common::normalize($response['price'], 'noescape|double'),
            $quantity = Common::normalize($response['quantity'], 'noescape|int'),
            $response['category']);

        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('body', $product);
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.product');
    }

    /**
     * @Put
     * @Role("Editor")
     * @Route("product/change/{id:int}")
     * @param ChangeProductBindingModel $model
     * @throws \Exception
     */
    public function change(ChangeProductBindingModel $model)
    {
        $this->db->prepare("
            SELECT id
            FROM categories
            WHERE name LIKE ?",
            [ $model->getCategory() ]);

        $response = $this->db->execute()->fetchRowAssoc();
        $categoryId = Common::normalize($response['id'], 'noescape|int');

        if (!$response) {
            $name = $model->getCategory();
            throw new \Exception("No category '$name'!", 404);
        }

        $id = $this->input->get(2);

        $this->db->prepare("
            UPDATE products_categories
            SET categoryId = ?
            WHERE productId = ?",
            [ $categoryId, $id ])->execute();

        $this->db->prepare("
            UPDATE products
            SET name = ?, description = ?, price = ?, quantity = ?
            WHERE id = ?",
            [
                $model->getName(),
                $model->getDescription(),
                $model->getPrice(),
                $model->getQuantity(),
                $id
            ])->execute();

        $this->redirect("{$this->path}product/$id/show");
    }

    /**
     * @Delete
     * @Role("Editor")
     * @Route("product/{id:int}/delete")
     * @throws \Exception
     */
    public function delete()
    {
        $id = $this->input->get(1);

        $this->db->prepare("
            DELETE FROM products_categories
            WHERE productId = ?",
            [ $id ])->execute();

        $this->db->prepare("
            DELETE FROM products
            WHERE id = ?",
            [ $id ])->execute();

        $this->redirect($this->path);
    }
}